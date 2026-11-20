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
use Infrangible\RetailStore\Helper\Search as SearchHelper;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Theme\Block\Html\Breadcrumbs;
use Psr\Log\LoggerInterface;

/**
 * Shows the stores near the location of the search form.
 *
 * The form sends its data with GET; POST is still accepted for forms of older versions.
 */
class Search implements HttpGetActionInterface, HttpPostActionInterface
{
    /** Registry key of the found stores. */
    public const REGISTRY_KEY_RETAIL_STORES = 'retail_stores';

    /** Registry key of the ID of the CMS block for the found stores. */
    public const REGISTRY_KEY_RESULT_BLOCK_ID = 'result_block_id';

    /** Registry key of the ID of the CMS block if no stores were found. */
    public const REGISTRY_KEY_NO_RESULT_BLOCK_ID = 'no_result_block_id';

    /**
     * @param RequestInterface $request
     * @param PageFactory $pageFactory
     * @param RedirectFactory $redirectFactory
     * @param Registry $registryHelper
     * @param SearchHelper $retailStoreSearchHelper
     * @param Stores $storeHelper
     * @param Variables $variables
     * @param LoggerInterface $logging
     */
    public function __construct(
        protected RequestInterface $request,
        protected PageFactory $pageFactory,
        protected RedirectFactory $redirectFactory,
        protected Registry $registryHelper,
        protected SearchHelper $retailStoreSearchHelper,
        protected Stores $storeHelper,
        protected Variables $variables,
        protected LoggerInterface $logging
    ) {
    }

    /**
     * Searches the stores and shows the result, or redirects to the home page without a location.
     *
     * @return Page|Redirect
     */
    public function execute(): Page|Redirect
    {
        $location = trim($this->variables->stringValueOrDefault($this->request->getParam('location')));

        if ($location === '') {
            $redirect = $this->redirectFactory->create();

            $redirect->setPath('/');

            return $redirect;
        }

        $maxDistance = $this->request->getParam('max_distance');

        try {
            $retailStores = $this->retailStoreSearchHelper->searchRetailStoresWithLocation(
                $location,
                0,
                is_numeric($maxDistance) ? max(0, (int)$maxDistance) : 0
            );
        } catch (LocalizedException $exception) {
            $this->logging->error($exception);

            $retailStores = [];
        }

        $this->registryHelper->register(self::REGISTRY_KEY_RETAIL_STORES, $retailStores, true);
        $this->registryHelper->register(
            self::REGISTRY_KEY_RESULT_BLOCK_ID,
            $this->getIntParam('result_block_id'),
            true
        );
        $this->registryHelper->register(
            self::REGISTRY_KEY_NO_RESULT_BLOCK_ID,
            $this->getIntParam('no_result_block_id'),
            true
        );

        $page = $this->pageFactory->create();

        $title = __('Retail Store Search Result')->render();

        $page->getConfig()->getTitle()->set($title);

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

            $breadcrumbs->addCrumb('search', ['label' => $title, 'title' => $title]);
        }

        return $page;
    }

    /**
     * Returns the parameter as positive integer, or 0.
     *
     * @param string $name
     *
     * @return int
     */
    protected function getIntParam(string $name): int
    {
        $value = $this->request->getParam($name);

        return is_numeric($value) ? max(0, (int)$value) : 0;
    }
}
