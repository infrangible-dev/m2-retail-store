<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Console\Command;

use Magento\Framework\App\Area;
use Infrangible\Core\Console\Command\Command;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class RetailStoreDemoData
    extends Command
{
    public function getCommandName(): string
    {
        return 'retail-store:demo:data';
    }

    protected function getCommandDescription(): string
    {
        return 'Import data for retail store demo';
    }

    protected function getCommandDefinition(): array
    {
        return [];
    }

    protected function getArea(): string
    {
        return Area::AREA_ADMINHTML;
    }

    protected function getClassName(): string
    {
        return Script\RetailStoreDemoData::class;
    }
}
