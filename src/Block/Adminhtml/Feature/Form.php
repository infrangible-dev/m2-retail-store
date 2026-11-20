<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Block\Adminhtml\Feature;

use Magento\Framework\Data\Form as DataForm;

/**
 * Form of a feature.
 */
class Form extends \Infrangible\BackendWidget\Block\Form
{
    /**
     * Adds the fields.
     *
     * @param DataForm $form
     *
     * @return void
     */
    protected function prepareFields(DataForm $form): void
    {
        $informationFieldSet = $form->addFieldset('information', ['legend' => __('Information')]);

        $this->addTextField($informationFieldSet, 'name', __('Name')->render(), true);
        $this->addImageField($informationFieldSet, 'image', __('Image')->render());
        $this->addYesNoField($informationFieldSet, 'status', __('Active')->render(), true);
    }

    /**
     * The form uploads images.
     *
     * @return bool
     */
    protected function isUploadForm(): bool
    {
        return true;
    }
}
