<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Block;

use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Registry;
use Infrangible\RetailStore\Helper\Data;
use Infrangible\RetailStore\Model\Store;
use Magento\Framework\View\Element\Template;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Search
    extends Template
{
    /** @var Registry */
    protected $registryHelper;

    /** @var Variables */
    protected $variables;

    /** @var Data */
    protected $retailStoreHelper;

    public function __construct(
        Template\Context $context,
        Registry $registryHelper,
        Variables $variables,
        Data $retailStoreHelper,
        array $data = [])
    {
        parent::__construct($context, $data);

        $this->registryHelper = $registryHelper;
        $this->variables = $variables;
        $this->retailStoreHelper = $retailStoreHelper;
    }

    public function getContent(): string
    {
        /** @var Store[] $retailStores */
        $retailStores = $this->registryHelper->registry('retail_stores');

        if ($this->variables->isEmpty($retailStores)) {
            $noResultBlockId = $this->registryHelper->registry('no_result_block_id');

            if ($this->variables->isEmpty($noResultBlockId)) {
                return '';
            }

            try {
                return $this->retailStoreHelper->getResultBlockOutput([], (int)$noResultBlockId);
            } catch (\Exception $exception) {
                return '';
            }
        } else {
            $resultBlockId = $this->registryHelper->registry('result_block_id');

            if ($this->variables->isEmpty($resultBlockId)) {
                return '';
            }

            try {
                return $this->retailStoreHelper->getResultBlockOutput($retailStores, (int)$resultBlockId);
            } catch (\Exception $exception) {
                return '';
            }
        }
    }
}
