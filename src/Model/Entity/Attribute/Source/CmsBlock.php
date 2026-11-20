<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Model\Entity\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * The CMS blocks, for the template of a store and the configuration.
 */
class CmsBlock extends AbstractSource
{
    /**
     * @param \Infrangible\Core\Model\Config\Source\CmsBlock $source
     */
    public function __construct(protected \Infrangible\Core\Model\Config\Source\CmsBlock $source)
    {
    }

    /**
     * Returns the CMS blocks.
     *
     * @return array
     * @phpstan-return array<int|string, mixed>
     */
    public function getAllOptions(): array
    {
        return $this->source->getAllOptions();
    }
}
