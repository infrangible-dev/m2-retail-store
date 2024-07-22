<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Block\Widget\GoogleMaps\Search;

use Magento\Framework\View\Element\Template;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Buttons
    extends Template
{
    protected function _construct(): void
    {
        $this->setTemplate($this->getTemplateName());

        parent::_construct();
    }

    public function getTemplateName(): string
    {
        return 'Infrangible_RetailStore::search/buttons.phtml';
    }
}
