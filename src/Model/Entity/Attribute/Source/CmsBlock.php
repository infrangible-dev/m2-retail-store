<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Model\Entity\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class CmsBlock
    extends AbstractSource
{
    /** @var \Infrangible\Core\Model\Config\Source\CmsBlock */
    protected $source;

    public function __construct(\Infrangible\Core\Model\Config\Source\CmsBlock $source)
    {
        $this->source = $source;
    }

    public function getAllOptions(): array
    {
        return $this->source->getAllOptions();
    }
}
