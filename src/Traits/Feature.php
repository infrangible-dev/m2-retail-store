<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Traits;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
trait Feature
{
    protected function getResourceKey(): string
    {
        return 'infrangible_retailstore_feature';
    }

    protected function getModuleKey(): string
    {
        return 'Infrangible_RetailStore';
    }

    protected function getObjectName(): string
    {
        return 'Feature';
    }

    protected function getMenuKey(): string
    {
        return 'infrangible_retailstore_feature';
    }

    protected function getTitle(): string
    {
        return __('Feature')->render();
    }

    protected function allowAdd(): bool
    {
        return true;
    }

    protected function allowEdit(): bool
    {
        return true;
    }

    protected function allowView(): bool
    {
        return false;
    }

    protected function allowDelete(): bool
    {
        return true;
    }

    protected function getObjectNotFoundMessage(): string
    {
        return __('Could not find feature with id: %s')->render();
    }
}
