<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Model\Template\Filter;

use Magento\Framework\Filter\DirectiveProcessorInterface;
use Magento\Framework\Filter\Template;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class TranslateDirective
    implements DirectiveProcessorInterface
{
    public function process(array $construction, Template $filter, array $templateVariables): string
    {
        if (empty($construction[1])) {
            return $construction[0];
        }

        return __($construction[1])->render();
    }

    public function getRegularExpression(): string
    {
        return '/#trans.*?\"(.*?)\"#/si';
    }
}
