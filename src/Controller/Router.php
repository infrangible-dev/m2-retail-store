<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Controller;

use Infrangible\Core\Helper\Cms;
use Infrangible\Core\Helper\Stores;
use Infrangible\RetailStore\Helper\Data;
use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\UrlInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Router
    implements RouterInterface
{
    /** @var Stores */
    protected $storeHelper;

    /** @var Data */
    protected $retailStoreHelper;

    /** @var Cms */
    protected $cmsHelper;

    /** @var ActionFactory */
    protected $actionFactory;

    public function __construct(
        Stores $storeHelper,
        Data $retailStoreHelper,
        Cms $cmsHelper,
        ActionFactory $actionFactory)
    {
        $this->storeHelper = $storeHelper;
        $this->retailStoreHelper = $retailStoreHelper;
        $this->cmsHelper = $cmsHelper;
        $this->actionFactory = $actionFactory;
    }

    public function match(RequestInterface $request): ?ActionInterface
    {
        /** @var Http $request */
        $identifier = urldecode(trim($request->getPathInfo(), '/'));

        $categoryUrlSuffix = $this->storeHelper->getStoreConfig('catalog/seo/category_url_suffix');

        $modelIdentifier =
            empty($categoryUrlSuffix) ? $identifier : substr($identifier, 0, -strlen($categoryUrlSuffix));

        $retailStore = $this->retailStoreHelper->loadRetailStoreWithKey($modelIdentifier);

        if ($retailStore && $retailStore->getId()) {
            $cmsBlockId = $retailStore->getCmsBlockId();

            if (!$cmsBlockId) {
                $cmsBlockId = $this->storeHelper->getStoreConfig('infrangible_retailstore/store/cms_block_id');
            }

            if ($cmsBlockId) {
                $cmsBlock = $this->cmsHelper->loadCmsBlock((int)$cmsBlockId);

                if ($cmsBlock->getId()) {
                    $request->setModuleName('retail_store');
                    $request->setControllerName('store');
                    $request->setActionName('view');
                    $request->setParam('store_id', $retailStore->getId());
                    $request->setAlias(UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $identifier);

                    return $this->actionFactory->create(Forward::class);
                }
            }
        }

        return null;
    }
}
