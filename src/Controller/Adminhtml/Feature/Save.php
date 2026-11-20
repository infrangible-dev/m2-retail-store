<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Controller\Adminhtml\Feature;

use Exception;
use Infrangible\BackendWidget\Model\Backend\Session;
use Infrangible\Core\Helper\Instances;
use Infrangible\Core\Helper\Registry;
use Infrangible\RetailStore\Model\ImageUploader;
use Infrangible\RetailStore\Traits\Feature;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Model\AbstractModel;
use Psr\Log\LoggerInterface;

/**
 * Saves a feature with its images.
 */
class Save extends \Infrangible\BackendWidget\Controller\Backend\Object\Save
{
    use Feature;

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
     * Returns the message after the feature was created.
     *
     * @return string
     */
    protected function getObjectCreatedMessage(): string
    {
        return __('Successfully created feature.')->render();
    }

    /**
     * Returns the message after the feature was updated.
     *
     * @return string
     */
    protected function getObjectUpdatedMessage(): string
    {
        return __('Successfully updated feature.')->render();
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

        parent::beforeSave($object);
    }
}
