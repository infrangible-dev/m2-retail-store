<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Traits;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
trait Store
{
    protected function getResourceKey(): string
    {
        return 'infrangible_retailstore_manage';
    }

    protected function getModuleKey(): string
    {
        return 'Infrangible_RetailStore';
    }

    protected function getObjectName(): string
    {
        return 'Store';
    }

    protected function getObjectField(): ?string
    {
        return 'entity_id';
    }

    protected function getMenuKey(): string
    {
        return 'infrangible_retailstore_manage';
    }

    protected function getTitle(): string
    {
        return __('Retail Store')->render();
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
        return __('Could not find retail store with id: %s')->render();
    }
}
