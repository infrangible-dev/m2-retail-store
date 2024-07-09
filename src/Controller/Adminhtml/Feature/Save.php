<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Controller\Adminhtml\Feature;

use Exception;
use FeWeDev\Base\Arrays;
use Infrangible\BackendWidget\Model\Backend\Session;
use Infrangible\Core\Helper\Instances;
use Infrangible\Core\Helper\Registry;
use Infrangible\RetailStore\Traits\Feature;
use Laminas\Http\Request;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Model\AbstractModel;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Psr\Log\LoggerInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Save
    extends \Infrangible\BackendWidget\Controller\Backend\Object\Save
{
    use Feature;

    /** @var Arrays */
    protected $arrays;

    /** @var UploaderFactory */
    protected $uploaderFactory;

    /** @var Filesystem */
    protected $fileSystem;

    /** @var File */
    protected $file;

    public function __construct(
        Registry $registryHelper,
        Instances $instanceHelper,
        Context $context,
        LoggerInterface $logging,
        Session $session,
        Arrays $arrays,
        UploaderFactory $uploaderFactory,
        Filesystem $fileSystem,
        File $file)
    {
        parent::__construct($registryHelper, $instanceHelper, $context, $logging, $session);

        $this->arrays = $arrays;
        $this->uploaderFactory = $uploaderFactory;
        $this->fileSystem = $fileSystem;
        $this->file = $file;
    }

    protected function getObjectCreatedMessage(): string
    {
        return __('Successfully created feature.')->render();
    }

    protected function getObjectUpdatedMessage(): string
    {
        return __('Successfully updated feature.')->render();
    }

    /**
     * @param AbstractModel $object
     *
     * @throws FileSystemException
     * @throws Exception
     */
    protected function beforeSave(AbstractModel $object)
    {
        /** @var Request $request */
        $request = $this->getRequest();

        $files = $request->getFiles();

        $image = $files->get('image');

        $imageName = is_array($image) ? $this->arrays->getValue($image, 'name') : null;

        if (!empty($imageName)) {
            $uploader = $this->uploaderFactory->create(['fileId' => 'image']);

            $uploader->setFilesDispersion(false);
            $uploader->setFilenamesCaseSensitivity(false);
            $uploader->setAllowRenameFiles(true);
            $uploader->setAllowCreateFolders(true);

            $imageResult = $uploader->save(sprintf('%s/retail_store',
                $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath()));

            $imageName = sprintf('retail_store/%s', $this->arrays->getValue($imageResult, 'file'));
        } else {
            $objectImage = $object->getDataUsingMethod('image');

            if (is_array($objectImage) && $this->arrays->getValue($objectImage, 'delete', 0) == 1) {
                $this->file->deleteFile(sprintf('%s/%s',
                    $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath(),
                    $this->arrays->getValue($objectImage, 'value')));
            } else {
                if (is_array($objectImage)) {
                    $imageName = $this->arrays->getValue($objectImage, 'value');
                }
            }
        }

        $object->setDataUsingMethod('image', $imageName);

        parent::beforeSave($object);
    }
}
