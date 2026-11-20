<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Controller\Adminhtml\Store;

use Exception;
use Infrangible\BackendWidget\Model\Backend\Session;
use Infrangible\Core\Helper\Instances;
use Infrangible\Core\Helper\Registry;
use Infrangible\RetailStore\Model\ImageUploader;
use Infrangible\RetailStore\Traits\Store;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Model\AbstractModel;
use Psr\Log\LoggerInterface;

/**
 * Saves a retail store with its images.
 */
class Save extends \Infrangible\BackendWidget\Controller\Backend\Object\Save
{
    use Store;

    /**
     * @param Registry $registryHelper
     * @param Instances $instanceHelper
     * @param Context $context
     * @param LoggerInterface $logging
     * @param Session $session
     * @param ImageUploader $imageUploader
     */
    public function __construct(
        Registry $registryHelper,
        Instances $instanceHelper,
        Context $context,
        LoggerInterface $logging,
        Session $session,
        protected ImageUploader $imageUploader
    ) {
        parent::__construct($registryHelper, $instanceHelper, $context, $logging, $session);
    }

    /**
     * Returns the message after the retail store was created.
     *
     * @return string
     */
    protected function getObjectCreatedMessage(): string
    {
        return __('Successfully created retail store.')->render();
    }

    /**
     * Returns the message after the retail store was updated.
     *
     * @return string
     */
    protected function getObjectUpdatedMessage(): string
    {
        return __('Successfully updated retail store.')->render();
    }

    /**
     * Saves uploaded images and deletes removed images.
     *
     * @param AbstractModel $object
     *
     * @return void
     * @throws Exception
     */
    protected function beforeSave(AbstractModel $object): void
    {
        $this->imageUploader->processImageField($this->getRequest(), $object, 'image');
        $this->imageUploader->processImageField($this->getRequest(), $object, 'map_image');

        $features = $object->getData('feature');

        if (is_array($features)) {
            $features = array_values(
                array_filter($features, static fn($featureId): bool => is_numeric($featureId) && (int)$featureId > 0)
            );

            $object->setData('feature', count($features) > 0 ? implode(',', $features) : null);
        }

        parent::beforeSave($object);
    }
}
