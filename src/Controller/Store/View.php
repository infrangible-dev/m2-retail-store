<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Controller\Store;

use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Registry;
use Infrangible\Core\Helper\Stores;
use Infrangible\RetailStore\Model\ResourceModel\StoreFactory as StoreResourceFactory;
use Infrangible\RetailStore\Model\StoreFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Theme\Block\Html\Breadcrumbs;

/**
 * Shows the page of an active retail store; the router forwards the URLs of the stores to this action.
 */
class View implements HttpGetActionInterface
{
    /** Registry key of the current store. */
    public const REGISTRY_KEY = 'current_retail_store';

    /**
     * @param RequestInterface $request
     * @param PageFactory $pageFactory
     * @param ForwardFactory $forwardFactory
     * @param Registry $registryHelper
     * @param Variables $variables
     * @param Stores $storeHelper
     * @param StoreFactory $retailStoreFactory
     * @param StoreResourceFactory $retailStoreResourceFactory
     */
    public function __construct(
        protected RequestInterface $request,
        protected PageFactory $pageFactory,
        protected ForwardFactory $forwardFactory,
        protected Registry $registryHelper,
        protected Variables $variables,
        protected Stores $storeHelper,
        protected StoreFactory $retailStoreFactory,
        protected StoreResourceFactory $retailStoreResourceFactory
    ) {
    }

    /**
     * Shows the page of the store, or the page "not found" if there is no active store with the ID.
     *
     * @return Page|Forward
     */
    public function execute(): Page|Forward
    {
        $retailStoreId = $this->request->getParam('retail_store_id');

        $retailStore = $this->retailStoreFactory->create();

        if (is_numeric($retailStoreId)) {
            $this->retailStoreResourceFactory->create()->load($retailStore, (int)$retailStoreId);
        }

        if (! $retailStore->getId() || ! $retailStore->isActive()) {
            $forward = $this->forwardFactory->create();

            $forward->forward('noroute');

            return $forward;
        }

        $this->registryHelper->register(self::REGISTRY_KEY, $retailStore, true);

        $page = $this->pageFactory->create();

        $page->addHandle(
            sprintf('infrangible_retailstore_store_view_%d', $this->variables->intValue($retailStore->getId()))
        );

        $name = $retailStore->getName();

        $pageConfig = $page->getConfig();

        $pageConfig->getTitle()->set($name);

        $description = $retailStore->getDescription();

        if ($description !== '') {
            $pageConfig->setDescription($description);
        }

        $breadcrumbs = $page->getLayout()->getBlock('breadcrumbs');

        if ($breadcrumbs instanceof Breadcrumbs) {
            $breadcrumbs->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link'  => $this->storeHelper->getWebUrl()
                ]
            );

            $breadcrumbs->addCrumb('retail_store', ['label' => $name, 'title' => $name]);
        }

        return $page;
    }
}
