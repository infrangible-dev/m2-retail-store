<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Console\Command;

use Infrangible\Core\Console\Command\Command;
use Magento\Framework\App\Area;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Command "retail-store:demo:data": creates the stores of the demo.
 */
class RetailStoreDemoData extends Command
{
    /**
     * Returns the name of the command.
     *
     * @return string
     */
    protected function getCommandName(): string
    {
        return 'retail-store:demo:data';
    }

    /**
     * Returns the description of the command.
     *
     * @return string
     */
    protected function getCommandDescription(): string
    {
        return 'Import data for retail store demo';
    }

    /**
     * The command has no options.
     *
     * @return array
     * @phpstan-return array<InputOption|InputArgument>
     */
    protected function getCommandDefinition(): array
    {
        return [];
    }

    /**
     * The script runs in the admin area.
     *
     * @return string
     */
    protected function getArea(): string
    {
        return Area::AREA_ADMINHTML;
    }

    /**
     * Returns the class of the script.
     *
     * @return string
     */
    protected function getClassName(): string
    {
        return Script\RetailStoreDemoData::class;
    }
}
