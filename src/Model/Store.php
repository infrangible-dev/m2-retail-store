<?php /** @noinspection PhpDeprecationInspection The constructor of Magento's models needs the registry. */

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Model;

use Infrangible\Core\Helper\Stores;
use Infrangible\Core\Helper\Url;
use Infrangible\RetailStore\Model\ResourceModel\Feature\CollectionFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * A retail store with its address, contact data, location and features.
 *
 * @method string|null getCode()
 * @method $this setCode(string $code)
 * @method string|null getStreet()
 * @method $this setStreet(string $street)
 * @method string|null getPostcode()
 * @method $this setPostcode(string $postcode)
 * @method string|null getCity()
 * @method $this setCity(string $city)
 * @method string|null getCountry()
 * @method $this setCountry(string $country)
 * @method string|null getPhoneNumber()
 * @method $this setPhoneNumber(string $phoneNumber)
 * @method string|null getEmail()
 * @method $this setEmail(string $email)
 * @method string|null getFaxNumber()
 * @method $this setFaxNumber(string $faxNumber)
 * @method string|null getImage()
 * @method $this setImage(string $image)
 * @method string|null getOpeningHours()
 * @method $this setOpeningHours(string $openingHours)
 * @method string|null getOpeningHoursSpecial()
 * @method $this setOpeningHoursSpecial(string $openingHoursSpecial)
 * @method $this setName(string $name)
 * @method $this setDescription(string $description)
 * @method $this setLatitude(string $latitude)
 * @method $this setLongitude(string $longitude)
 * @method $this setUrlKey(string $urlKey)
 * @method $this setCmsBlockId(int $cmsBlockId)
 * @method $this setStatus(int $status)
 * @method string|null getCreatedAt()
 * @method $this setCreatedAt(string $createdAt)
 * @method string|null getUpdatedAt()
 * @method $this setUpdatedAt(string $updatedAt)
 * @method string|null getSeoText()
 * @method $this setSeoText(string $seoText)
 */
class Store extends AbstractModel
{
    /** Code of the EAV entity type. */
    public const ENTITY = 'retail_store';

    /** @var string */
    protected $_eventPrefix = 'retail_store_store';

    /** @var string */
    protected $_eventObject = 'store';

    /** @var Feature[]|null */
    private ?array $features = null;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Stores $storeHelper
     * @param Url $urlHelper
     * @param CollectionFactory $featureCollectionFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     * @phpstan-param AbstractDb<AbstractModel>|null $resourceCollection
     * @phpstan-param array<string, mixed> $data
     * @throws LocalizedException
     */
    public function __construct(
        Context $context,
        Registry $registry,
        protected Stores $storeHelper,
        protected Url $urlHelper,
        protected CollectionFactory $featureCollectionFactory,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Sets the resource model.
     *
     * @return void
     * @throws LocalizedException
     */
    protected function _construct(): void
    {
        $this->_init(ResourceModel\Store::class);
    }

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->stringValue('name');
    }

    /**
     * Returns the description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->stringValue('description');
    }

    /**
     * Returns the latitude, or null if the store has none.
     *
     * @return float|null
     */
    public function getLatitude(): ?float
    {
        $latitude = $this->getData('latitude');

        return is_numeric($latitude) ? (float)$latitude : null;
    }

    /**
     * Returns the longitude, or null if the store has none.
     *
     * @return float|null
     */
    public function getLongitude(): ?float
    {
        $longitude = $this->getData('longitude');

        return is_numeric($longitude) ? (float)$longitude : null;
    }

    /**
     * Returns the URL key.
     *
     * @return string
     */
    public function getUrlKey(): string
    {
        return $this->stringValue('url_key');
    }

    /**
     * Returns the ID of the CMS block of the store page, or 0 if the store uses the block of the configuration.
     *
     * @return int
     */
    public function getCmsBlockId(): int
    {
        $cmsBlockId = $this->getData('cms_block_id');

        return is_numeric($cmsBlockId) ? max(0, (int)$cmsBlockId) : 0;
    }

    /**
     * Checks if the store is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        $status = $this->getData('status');

        return is_numeric($status) && (int)$status === 1;
    }

    /**
     * Returns a value as text, or an empty text.
     *
     * @param string $key
     *
     * @return string
     */
    protected function stringValue(string $key): string
    {
        $value = $this->getData($key);

        return is_scalar($value) ? (string)$value : '';
    }

    /**
     * Sets the creation and update time.
     *
     * @return $this
     */
    public function beforeSave(): static
    {
        if ($this->isObjectNew()) {
            $this->setData('created_at', gmdate('Y-m-d H:i:s'));
        }

        $this->setData('updated_at', gmdate('Y-m-d H:i:s'));

        parent::beforeSave();

        return $this;
    }

    /**
     * Returns the path of the store page: the URL key with the URL suffix of categories.
     *
     * @return string
     */
    public function getUrlPath(): string
    {
        return $this->getUrlKey() . $this->getUrlSuffix();
    }

    /**
     * Returns the URL suffix of categories, which store pages use as well.
     *
     * @return string
     */
    public function getUrlSuffix(): string
    {
        $suffix = $this->storeHelper->getStoreConfig('catalog/seo/category_url_suffix');

        return is_string($suffix) ? $suffix : '';
    }

    /**
     * Returns the URL of the store page.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->urlHelper->getDirectUrl($this->getUrlPath());
    }

    /**
     * Returns the features of the store; they are loaded when the store was loaded in a collection as well.
     *
     * @return Feature[]
     */
    public function getFeatures(): array
    {
        if ($this->features === null) {
            $this->features = [];

            $featureIds = $this->getFeatureIds();

            if (count($featureIds) > 0) {
                $featureCollection = $this->featureCollectionFactory->create();

                $featureCollection->getSelect()->where('main_table.id IN (?)', $featureIds);

                foreach ($featureCollection as $feature) {
                    if ($feature instanceof Feature) {
                        $this->features[] = $feature;
                    }
                }
            }
        }

        return $this->features;
    }

    /**
     * Sets the features of the store.
     *
     * @param Feature[] $features
     *
     * @return void
     */
    public function setFeatures(array $features): void
    {
        $this->features = $features;
    }

    /**
     * Returns the IDs of the features of the store, stored as comma-separated list.
     *
     * @return int[]
     */
    public function getFeatureIds(): array
    {
        $feature = $this->getData('feature');

        if (is_array($feature)) {
            $featureIds = $feature;
        } elseif (is_string($feature) || is_int($feature)) {
            $featureIds = explode(',', (string)$feature);
        } else {
            return [];
        }

        $featureIds = array_map(
            static fn(mixed $featureId): int => is_numeric($featureId) ? (int)$featureId : 0,
            $featureIds
        );

        return array_values(array_filter($featureIds, static fn(int $featureId): bool => $featureId > 0));
    }
}
