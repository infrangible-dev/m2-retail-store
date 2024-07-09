<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\RetailStore\Model\Template;

use Infrangible\RetailStore\Model\Template\Filter\TranslateDirective;
use Infrangible\RetailStore\Model\Template\Filter\VarDirective;
use Magento\Framework\App\ObjectManager;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Filter
    extends \Magento\Widget\Model\Template\Filter
{
    /**
     * @param string $value
     *
     * @return string
     */
    public function filter($value): string
    {
        if ($value === null) {
            return '';
        }

        /** @var VarDirective $varDirective */
        $varDirective = ObjectManager::getInstance()->get(VarDirective::class);

        if (preg_match_all($varDirective->getRegularExpression(), (string)$value, $constructions, PREG_SET_ORDER)) {
            foreach ($constructions as $construction) {
                $replacedValue = $varDirective->process($construction, $this, $this->templateVars);

                $value = str_replace($construction[0], $replacedValue, $value);
            }
        }

        /** @var TranslateDirective $transDirective */
        $transDirective = ObjectManager::getInstance()->get(TranslateDirective::class);

        if (preg_match_all($transDirective->getRegularExpression(), $value, $constructions, PREG_SET_ORDER)) {
            foreach ($constructions as $construction) {
                $replacedValue = $transDirective->process($construction, $this, $this->templateVars);

                $value = str_replace($construction[0], $replacedValue, $value);
            }
        }

        return parent::filter($value);
    }
}
