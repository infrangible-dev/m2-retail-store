<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Controller\Store;

use Exception;
use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Registry;
use Infrangible\Core\Helper\Stores;
use Infrangible\RetailStore\Model\StoreFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Theme\Block\Html\Breadcrumbs;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class View
    extends Action
{
    /** @var Registry */
    protected $registryHelper;

    /** @var Variables */
    protected $variables;

    /** @var StoreFactory */
    protected $retailStoreFactory;

    /** @var Stores */
    protected $storeHelper;

    /** @var \Infrangible\RetailStore\Model\ResourceModel\StoreFactory */
    protected $retailStoreResourceFactory;

    /** @var RawFactory */
    protected $resultRawFactory;

    public function __construct(
        Context $context,
        Registry $registryHelper,
        Variables $variables,
        Stores $storeHelper,
        StoreFactory $retailStoreFactory,
        \Infrangible\RetailStore\Model\ResourceModel\StoreFactory $retailStoreResourceFactory,
        RawFactory $resultRawFactory)
    {
        parent::__construct($context);

        $this->registryHelper = $registryHelper;
        $this->variables = $variables;
        $this->storeHelper = $storeHelper;

        $this->retailStoreFactory = $retailStoreFactory;
        $this->retailStoreResourceFactory = $retailStoreResourceFactory;
        $this->resultRawFactory = $resultRawFactory;
    }

    /**
     * @return ResultInterface|void
     * @throws Exception
     */
    public function execute()
    {
        $storeId = $this->getRequest()->getParam('store_id');

        if (!$storeId) {
            $this->_forward('noRoute');

            return;
        }

        $retailStore = $this->retailStoreFactory->create();

        $this->retailStoreResourceFactory->create()->load($retailStore, $storeId);

        if (!$retailStore->getId()) {
            $this->_forward('noRoute');

            return;
        }

        $this->registryHelper->register('current_retail_store', $retailStore, true);

        $update = $this->_view->getLayout()->getUpdate();

        $update->addHandle('default');

        $this->_view->addActionLayoutHandles();

        $update->addHandle('infrangible_retailstore_store_view');
        $update->addHandle(sprintf('infrangible_retailstore_store_view_%d', $retailStore->getId()));

        $this->_view->loadLayoutUpdates();

        $this->_view->generateLayoutXml()->generateLayoutBlocks();

        $pageConfig = $this->_view->getPage()->getConfig();

        $pageConfig->getTitle()->set($retailStore->getName());

        if (!$this->variables->isEmpty($retailStore->getDescription())) {
            $pageConfig->setDescription($retailStore->getDescription());
        }

        /** @var Breadcrumbs $breadcrumbs */
        $breadcrumbs = $this->_view->getLayout()->getBlock('breadcrumbs');

        if ($breadcrumbs) {
            $breadcrumbs->addCrumb('home', ['label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link' => $this->storeHelper->getWebUrl()]);

            $breadcrumbs->addCrumb('search', ['label' => $retailStore->getName(), 'title' => $retailStore->getName()]);
        }

        return $this->_view->getPage();
    }
}
