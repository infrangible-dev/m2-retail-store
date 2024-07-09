<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Block\Adminhtml\Feature;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Form
    extends \Infrangible\BackendWidget\Block\Form
{
    protected function prepareFields(\Magento\Framework\Data\Form $form)
    {
        $object = $this->getObject();

        $informationFieldSet = $form->addFieldset('information', ['legend' => __('Information')]);

        $this->addTextField($informationFieldSet, 'name', __('Name')->render(), true);
        $informationFieldSet->addField('image', 'image', ['name' => 'image',
            'label' => __('Image'),
            'value' => $object->getId() ? $object->getDataUsingMethod('image') : '']);
        $this->addYesNoField($informationFieldSet, 'status', __('Active')->render(), true);
    }

    protected function isUploadForm(): bool
    {
        return true;
    }
}
