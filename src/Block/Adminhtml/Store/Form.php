<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\RetailStore\Block\Adminhtml\Store;

use FeWeDev\Base\Arrays;
use Infrangible\RetailStore\Model\Entity\Attribute\Source\Feature;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Form
    extends \Infrangible\BackendWidget\Block\Form
{
    /** @var Feature */
    protected $sourceFeature;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Arrays $arrays,
        \Infrangible\Core\Helper\Registry $registryHelper,
        \Infrangible\BackendWidget\Helper\Form $formHelper,
        Feature $sourceFeature,
        array $data = [])
    {
        parent::__construct($context, $registry, $formFactory, $arrays, $registryHelper, $formHelper, $data);

        $this->sourceFeature = $sourceFeature;
    }

    protected function prepareFields(\Magento\Framework\Data\Form $form)
    {
        $object = $this->getObject();

        $informationFieldSet = $form->addFieldset('information', ['legend' => __('Information')]);

        $this->addTextField($informationFieldSet, 'code', __('Code')->render(), true);
        $this->addTextField($informationFieldSet, 'name', __('Name')->render(), true);
        $this->addTextareaField($informationFieldSet, 'description', __('Description')->render());
        $informationFieldSet->addField('image', 'image', ['name' => 'image',
            'label' => __('Image'),
            'value' => $object->getId() ? $object->getDataUsingMethod('image') : '']);
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
        $locationFieldSet->addField('map_image', 'image', ['name' => 'map_image',
            'label' => __('Map Image'),
            'value' => $object->getId() ? $object->getDataUsingMethod('map_image') : '']);

        $frontendFieldSet = $form->addFieldset('frontend', ['legend' => __('Frontend')]);

        $this->addCmsBlockSelectField($frontendFieldSet, 'cms_block_id', __('Template')->render(), null, true);
        $this->addTextField($frontendFieldSet, 'url_key', __('URL Key')->render(), true);
        $this->addEditorField($frontendFieldSet, 'seo_text', __('SEO Text')->render());

        $featuresFieldSet = $form->addFieldset('features', ['legend' => __('Features')]);

        $this->addOptionsMultiSelectField($featuresFieldSet, 'feature', __('Features')->render(),
            $this->sourceFeature->toOptionArray(), null);
    }

    protected function isUploadForm(): bool
    {
        return true;
    }
}
