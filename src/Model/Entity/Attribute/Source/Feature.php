<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Model\Entity\Attribute\Source;

use Infrangible\RetailStore\Model\ResourceModel\Feature\CollectionFactory;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Feature
    extends AbstractSource
{
    /** @var CollectionFactory */
    protected $featureCollectionFactory;

    public function __construct(CollectionFactory $featureCollectionFactory)
    {
        $this->featureCollectionFactory = $featureCollectionFactory;
    }

    public function getAllOptions(): array
    {
        if (is_null($this->_options)) {
            $this->_options[] = ['label' => 'None',
                'value' => 0];

            $featureCollection = $this->featureCollectionFactory->create();

            /** @var \Infrangible\RetailStore\Model\Feature $feature */
            foreach ($featureCollection as $feature) {
                $this->_options[] = ['label' => $feature->getName(),
                    'value' => $feature->getId()];
            }
        }

        return $this->_options;
    }
}
