<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\RetailStore\Model;

use Infrangible\Core\Helper\Stores;
use Infrangible\Core\Helper\Url;
use Infrangible\RetailStore\Model\ResourceModel;
use Infrangible\RetailStore\Model\ResourceModel\Feature\CollectionFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 *
 * @method string getCode()
 * @method void setCode(string $code)
 * @method string getName()
 * @method void setName(string $name)
 * @method string getDescription()
 * @method void setDescription(string $description)
 * @method string getStreet()
 * @method void setStreet(string $street)
 * @method string getPostcode()
 * @method void setPostcode(string $postcode)
 * @method string getCity()
 * @method void setCity(string $city)
 * @method string getCountry()
 * @method void setCountry(string $country)
 * @method string getPhoneNumber()
 * @method void setPhoneNumber(string $phoneNumber)
 * @method string getEmail()
 * @method void setEmail(string $email)
 * @method string getFaxNumber()
 * @method void setFaxNumber(string $faxNumber)
 * @method string getLatitude()
 * @method void setLatitude(string $latitude)
 * @method string getLongitude()
 * @method void setLongitude(string $longitude)
 * @method string getImage()
 * @method void setImage(string $image)
 * @method string getOpeningHours()
 * @method void setOpeningHours(string $openingHours)
 * @method string getOpeningHoursSpecial()
 * @method void setOpeningHoursSpecial(string $openingHoursSpecial)
 * @method int getStatus()
 * @method void setStatus(int $status)
 * @method string getCreatedAt()
 * @method void setCreatedAt(string $createdAt)
 * @method string getUpdatedAt()
 * @method void setUpdatedAt(string $updatedAt)
 * @method string getUrlKey()
 * @method void setUrlKey(string $urlKey)
 * @method string getSeoText()
 * @method void setSeoText(string $seoText)
 * @method int getCmsBlockId()
 * @method void setCmsBlockId(int $cmsBlockId)
 */
class Store
    extends AbstractModel
{
    /** @var string */
    public const ENTITY = 'retail_store';

    /** @var string */
    protected $_eventPrefix = 'retail_store_store';

    /** @var string */
    protected $_eventObject = 'store';

    /** @var Stores */
    protected $storeHelper;

    /** @var Url */
    protected $urlHelper;

    /** @var CollectionFactory */
    protected $featureCollectionFactory;

    /** @var Feature[] */
    private $features = [];

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Stores $storeHelper
     * @param Url $urlHelper
     * @param CollectionFactory $featureCollectionFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Stores $storeHelper,
        Url $urlHelper,
        CollectionFactory $featureCollectionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = [])
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->storeHelper = $storeHelper;
        $this->urlHelper = $urlHelper;

        $this->featureCollectionFactory = $featureCollectionFactory;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Store::class);
    }

    public function afterLoad(): AbstractModel
    {
        parent::afterLoad();

        $feature = $this->getData('feature');

        if (!empty($feature)) {
            $featureIds = explode(',', $feature);

            $featureCollection = $this->featureCollectionFactory->create();

            $featureCollection->addFieldToFilter('id', ['in' => $featureIds]);

            /** @var Feature[] $features */
            $features = $featureCollection->getItems();

            $this->setFeatures($features);
        }

        return $this;
    }

    /**
     * Processing object before save data
     *
     * @return AbstractModel
     */
    public function beforeSave(): AbstractModel
    {
        if ($this->isObjectNew()) {
            $this->setCreatedAt(gmdate('Y-m-d H:i:s'));
        }

        $this->setUpdatedAt(gmdate('Y-m-d H:i:s'));

        return parent::beforeSave();
    }

    public function getUrl(): string
    {
        $categoryUrlSuffix = $this->storeHelper->getStoreConfig('catalog/seo/category_url_suffix');

        return $this->urlHelper->getDirectUrl(sprintf('%s%s', $this->getUrlKey(),
            empty($categoryUrlSuffix) ? '' : $categoryUrlSuffix));
    }

    /**
     * @return Feature[]
     */
    public function getFeatures(): array
    {
        return $this->features;
    }

    /**
     * @param Feature[] $features
     */
    public function setFeatures(array $features)
    {
        $this->features = $features;
    }
}
