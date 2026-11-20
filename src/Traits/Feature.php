<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Traits;

/**
 * Settings of the admin pages of the features.
 */
trait Feature
{
    /**
     * Returns the key of the ACL resource.
     *
     * @return string
     */
    protected function getResourceKey(): string
    {
        return 'infrangible_retailstore_feature';
    }

    /**
     * Returns the key of the module.
     *
     * @return string
     */
    protected function getModuleKey(): string
    {
        return 'Infrangible_RetailStore';
    }

    /**
     * Returns the name of the object.
     *
     * @return string
     */
    protected function getObjectName(): string
    {
        return 'Feature';
    }

    /**
     * Returns the key of the menu item.
     *
     * @return string
     */
    protected function getMenuKey(): string
    {
        return 'infrangible_retailstore_feature';
    }

    /**
     * Returns the title of the pages.
     *
     * @return string
     */
    protected function getTitle(): string
    {
        return __('Feature')->render();
    }

    /**
     * Objects can be added.
     *
     * @return bool
     */
    protected function allowAdd(): bool
    {
        return true;
    }

    /**
     * Objects can be edited.
     *
     * @return bool
     */
    protected function allowEdit(): bool
    {
        return true;
    }

    /**
     * Objects have no view page.
     *
     * @return bool
     */
    protected function allowView(): bool
    {
        return false;
    }

    /**
     * Objects can be deleted.
     *
     * @return bool
     */
    protected function allowDelete(): bool
    {
        return true;
    }

    /**
     * Returns the message if the object was not found.
     *
     * @return string
     */
    protected function getObjectNotFoundMessage(): string
    {
        return __('Could not find feature with id: %s')->render();
    }
}
