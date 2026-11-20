<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Block\Widget\GoogleMaps\Search;

use Magento\Framework\View\Element\Template;

/**
 * The buttons of the search form: search and determine the location.
 */
class Buttons extends Template
{
    /**
     * Sets the template.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->setTemplate($this->getTemplateName());

        parent::_construct();
    }

    /**
     * Returns the name of the template.
     *
     * @return string
     */
    public function getTemplateName(): string
    {
        return 'Infrangible_RetailStore::search/buttons.phtml';
    }
}
