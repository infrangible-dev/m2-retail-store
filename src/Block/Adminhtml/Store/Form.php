<?php /** @noinspection PhpDeprecationInspection The constructor of Magento's forms needs the registry. */

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Block\Adminhtml\Store;

use FeWeDev\Base\Arrays;
use Infrangible\RetailStore\Model\Config\Source\RetailStoreNoteType;
use Infrangible\RetailStore\Model\Entity\Attribute\Source\Feature;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form as DataForm;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

/**
 * Form of a retail store.
 */
class Form extends \Infrangible\BackendWidget\Block\Form
{
    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Arrays $arrays
     * @param \Infrangible\Core\Helper\Registry $registryHelper
     * @param \Infrangible\BackendWidget\Helper\Form $formHelper
     * @param Feature $sourceFeature
     * @param RetailStoreNoteType $sourceNoteType
     * @param array $data
     * @phpstan-param array<string, mixed> $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Arrays $arrays,
        \Infrangible\Core\Helper\Registry $registryHelper,
        \Infrangible\BackendWidget\Helper\Form $formHelper,
        protected Feature $sourceFeature,
        protected RetailStoreNoteType $sourceNoteType,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $arrays, $registryHelper, $formHelper, $data);
    }

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

        $this->addTextField($informationFieldSet, 'code', __('Code')->render(), true);
        $this->addTextField($informationFieldSet, 'name', __('Name')->render(), true);
        $this->addTextareaField($informationFieldSet, 'description', __('Description')->render());
        $this->addImageField($informationFieldSet, 'image', __('Image')->render());
        $this->addTextareaField($informationFieldSet, 'opening_hours', __('Opening Hours')->render());
        $this->addTextareaField($informationFieldSet, 'opening_hours_special', __('Special Opening Hours')->render());
        $this->addYesNoField($informationFieldSet, 'status', __('Active')->render(), true);

        $contactFieldSet = $form->addFieldset('contact', ['legend' => __('Contact')]);

        $this->addTextField($contactFieldSet, 'email', __('E-Mail')->render(), true);
        $this->addTextField($contactFieldSet, 'phone_number', __('Phone Number')->render(), true);
        $this->addTextField($contactFieldSet, 'fax_number', __('Fax Number')->render());

        $addressFieldSet = $form->addFieldset('address', ['legend' => __('Address')]);

        $this->addTextField($addressFieldSet, 'street', __('Street')->render(), true);
        $this->addTextField($addressFieldSet, 'postcode', __('Postal Code')->render(), true);
        $this->addTextField($addressFieldSet, 'city', __('City')->render(), true);
        $this->addCountryField($addressFieldSet, 'country', __('Country')->render(), true);
        $this->addTextareaField($addressFieldSet, 'direction', __('Direction')->render());

        $locationFieldSet = $form->addFieldset('location', ['legend' => __('Location')]);

        $this->addTextField($locationFieldSet, 'latitude', __('Latitude')->render(), true);
        $this->addTextField($locationFieldSet, 'longitude', __('Longitude')->render(), true);
        $this->addImageField($locationFieldSet, 'map_image', __('Map Image')->render());

        $frontendFieldSet = $form->addFieldset('frontend', ['legend' => __('Frontend')]);

        $this->addCmsBlockSelectField($frontendFieldSet, 'cms_block_id', __('Template')->render());
        $this->addTextField($frontendFieldSet, 'url_key', __('URL Key')->render(), true);
        $this->addEditorField($frontendFieldSet, 'seo_text', __('SEO Text')->render());
        $this->addTextField($frontendFieldSet, 'note', __('Note')->render());
        $this->addOptionsField(
            $frontendFieldSet,
            'note_type',
            __('Note Type')->render(),
            $this->sourceNoteType->toOptionArray(),
            RetailStoreNoteType::NONE
        );

        $featuresFieldSet = $form->addFieldset('features', ['legend' => __('Features')]);

        $this->addOptionsMultiSelectField(
            $featuresFieldSet,
            'feature',
            __('Features')->render(),
            $this->sourceFeature->getFeatureOptions(),
            null
        );
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
