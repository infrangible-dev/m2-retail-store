<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Block\Widget\GoogleMaps;

use Infrangible\GoogleMaps\Block\Widget\GoogleMaps\Autocomplete;
use Infrangible\RetailStore\Block\Widget\GoogleMaps\Search\Buttons;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Search
    extends Template
    implements BlockInterface
{
    /** @var FormKey */
    protected $formKey;

    /** @var ResponseInterface */
    protected $response;

    public function __construct(
        Template\Context  $context,
        FormKey           $formKey,
        ResponseInterface $response,
        array             $data = [])
    {
        parent::__construct($context, $data);

        $this->formKey = $formKey;
        $this->response = $response;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->setTemplate('Infrangible_RetailStore::search.phtml');

        parent::_construct();
    }

    protected function _afterToHtml($html): string
    {
        if ($this->response instanceof Http) {
            $this->response->setNoCacheHeaders();
        }

        return parent::_afterToHtml($html);
    }

    public function getLocationInput(): string
    {
        /** @var  $locationInputBlock */
        try {
            /** @var Autocomplete $locationInputBlock */
            $locationInputBlock = $this->getLayout()->createBlock(Autocomplete::class);

            $locationInputBlock->setData('input_id', 'location');
            $locationInputBlock->setData('component_restrictions', 'country:"de"');

            return $locationInputBlock->toHtml();
        } catch (LocalizedException $exception) {
            return '';
        }
    }

    /**
     * @return string[]
     */
    public function getDistances(): array
    {
        return [10 => '10 km', 25 => '25 km', 50 => '50 km', 100 => '100 km'];
    }

    public function getMaxDistanceValue(): ?string
    {
        $request = $this->getRequest();

        return $request->getParam('max_distance');
    }

    public function getResultBlockId(): int
    {
        return (int)$this->getData('result_block_id');
    }

    public function getNoResultBlockId(): int
    {
        return (int)$this->getData('no_result_block_id');
    }

    public function getFormKey(): string
    {
        try {
            return $this->formKey->getFormKey();
        } catch (LocalizedException $exception) {
            return '';
        }
    }

    public function getButtonsHtml(): string
    {
        try {
            $block = $this->getLayout()->createBlock(Buttons::class);

            return $block->toHtml();
        } catch (LocalizedException $exception) {
            $this->_logger->error($exception);

            return '';
        }
    }
}
