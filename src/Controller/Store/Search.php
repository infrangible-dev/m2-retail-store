<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Controller\Store;

use Infrangible\Core\Helper\Registry;
use Infrangible\Core\Helper\Stores;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Theme\Block\Html\Breadcrumbs;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Search
    extends Action
{
    /** @var Registry */
    protected $registryHelper;

    /** @var \Infrangible\RetailStore\Helper\Search */
    protected $retailStoreSearchHelper;

    /** @var Stores */
    protected $storeHelper;

    public function __construct(
        Context $context,
        Registry $registryHelper,
        \Infrangible\RetailStore\Helper\Search $retailStoreSearchHelper,
        Stores $storeHelper)
    {
        parent::__construct($context);

        $this->registryHelper = $registryHelper;
        $this->retailStoreSearchHelper = $retailStoreSearchHelper;
        $this->storeHelper = $storeHelper;
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $request = $this->getRequest();

        $location = $request->getParam('location');
        $maxDistance = (int)$request->getParam('max_distance');

        try {
            if ($location) {
                $retailStores =
                    $this->retailStoreSearchHelper->searchRetailStoresWithLocation($location, 0, $maxDistance);

                $this->registryHelper->register('retail_stores', $retailStores);
                $this->registryHelper->register('result_block_id', $request->getParam('result_block_id'));
                $this->registryHelper->register('no_result_block_id', $request->getParam('no_result_block_id'));
            } else {
                return $this->_redirect('/');
            }
        } catch (LocalizedException $exception) {
        }

        $update = $this->_view->getLayout()->getUpdate();
        $update->addHandle('default');

        $this->_view->addActionLayoutHandles();
        $this->_view->loadLayoutUpdates();
        $this->_view->generateLayoutXml();
        $this->_view->generateLayoutBlocks();

        $pageConfig = $this->_view->getPage()->getConfig();

        $pageConfig->getTitle()->set(__('Retail Store Search Result'));

        /** @var Breadcrumbs $breadcrumbs */
        $breadcrumbs = $this->_view->getLayout()->getBlock('breadcrumbs');

        if ($breadcrumbs) {
            $breadcrumbs->addCrumb('home', ['label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->storeHelper->getWebUrl()]);

            $breadcrumbs->addCrumb('search',
                ['label' => __('Retail Store Search Result'), 'title' => __('Retail Store Search Result')]);
        }

        return $this->_view->getPage();
    }
}
