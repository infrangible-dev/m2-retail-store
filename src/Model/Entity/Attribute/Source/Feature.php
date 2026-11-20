<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Model\Entity\Attribute\Source;

use FeWeDev\Base\Variables;
use Infrangible\RetailStore\Model\Feature as FeatureModel;
use Infrangible\RetailStore\Model\ResourceModel\Feature\CollectionFactory;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * The features of stores, with the option "None".
 */
class Feature extends AbstractSource
{
    /**
     * @param CollectionFactory $featureCollectionFactory
     * @param Variables $variables
     */
    public function __construct(
        protected CollectionFactory $featureCollectionFactory,
        protected Variables $variables
    ) {
    }

    /**
     * Returns the option "None" and the features.
     *
     * @return array
     * @phpstan-return array<int, array{value: int, label: string}>
     */
    public function getAllOptions(): array
    {
        if ($this->_options === null) {
            $this->_options = array_merge(
                [['value' => 0, 'label' => __('None')->render()]],
                $this->getFeatureOptions()
            );
        }

        return $this->_options;
    }

    /**
     * Returns the features.
     *
     * @return array
     * @phpstan-return array<int, array{value: int, label: string}>
     */
    public function getFeatureOptions(): array
    {
        $options = [];

        foreach ($this->featureCollectionFactory->create() as $feature) {
            if ($feature instanceof FeatureModel) {
                $options[] = [
                    'value' => $this->variables->intValue($feature->getId()),
                    'label' => $feature->getName()
                ];
            }
        }

        return $options;
    }
}
