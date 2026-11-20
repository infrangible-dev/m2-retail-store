<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Controller;

use Infrangible\Core\Helper\Stores;
use Infrangible\RetailStore\Helper\Data;
use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;

/**
 * Forwards the URLs of the store pages (URL key with the URL suffix of categories) to the store view.
 */
class Router implements RouterInterface
{
    /**
     * @param Stores $storeHelper
     * @param Data $retailStoreHelper
     * @param ActionFactory $actionFactory
     * @param LoggerInterface $logging
     */
    public function __construct(
        protected Stores $storeHelper,
        protected Data $retailStoreHelper,
        protected ActionFactory $actionFactory,
        protected LoggerInterface $logging
    ) {
    }

    /**
     * Forwards the request to the store view if its path is the URL of an active store with a CMS block.
     *
     * @param RequestInterface $request
     *
     * @return ActionInterface|null
     */
    public function match(RequestInterface $request): ?ActionInterface
    {
        if (! $request instanceof Http) {
            return null;
        }

        $identifier = urldecode(trim($request->getPathInfo(), '/'));

        $suffix = $this->storeHelper->getStoreConfig('catalog/seo/category_url_suffix');
        $suffix = is_string($suffix) ? $suffix : '';

        if ($suffix !== '') {
            if (! str_ends_with($identifier, $suffix)) {
                return null;
            }

            $identifier = substr($identifier, 0, -strlen($suffix));
        }

        try {
            $retailStore = $this->retailStoreHelper->loadRetailStoreWithKey($identifier);
        } catch (LocalizedException $exception) {
            $this->logging->error($exception);

            return null;
        }

        if ($retailStore === null ||
            $this->retailStoreHelper->getCmsBlock($this->retailStoreHelper->getStoreCmsBlockId($retailStore)) ===
            null) {

            return null;
        }

        $request->setModuleName('retail_store');
        $request->setControllerName('store');
        $request->setActionName('view');
        $request->setParam('retail_store_id', $retailStore->getId());
        $request->setAlias(UrlInterface::REWRITE_REQUEST_PATH_ALIAS, trim($request->getPathInfo(), '/'));

        return $this->actionFactory->create(Forward::class);
    }
}
