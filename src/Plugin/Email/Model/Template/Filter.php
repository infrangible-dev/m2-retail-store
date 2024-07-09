<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Plugin\Email\Model\Template;

use FeWeDev\Base\Arrays;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Filter
{
    /** @var Arrays */
    protected $arrays;

    public function __construct(Arrays $arrays)
    {
        $this->arrays = $arrays;
    }

    public function afterVarDirective(
        \Magento\Email\Model\Template\Filter $subject,
        ?string $result,
        array $construction = []): ?string
    {
        $value =
            $this->arrays->getValue($construction, '2', '') . $this->arrays->getValue($construction, 'filters', '');

        $parts = explode('|', $value, 2);

        if (2 === count($parts)) {
            $modifier = $this->arrays->getValue($parts, '1');

            if ($modifier === 'filter') {
                $result = $subject->filter($result);
            }
        }

        return $result;
    }
}
