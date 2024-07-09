<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Controller\Adminhtml\Store;

use Exception;
use FeWeDev\Base\Arrays;
use Infrangible\BackendWidget\Model\Backend\Session;
use Infrangible\Core\Helper\Instances;
use Infrangible\Core\Helper\Registry;
use Infrangible\RetailStore\Traits\Store;
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
    use Store;

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
        return __('Successfully created retail store.')->render();
    }

    protected function getObjectUpdatedMessage(): string
    {
        return __('Successfully updated retail store.')->render();
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

        $mapImage = $files->get('map_image');

        $mapImageName = is_array($mapImage) ? $this->arrays->getValue($mapImage, 'name') : null;

        if (!empty($mapImageName)) {
            $uploader = $this->uploaderFactory->create(['fileId' => 'map_image']);

            $uploader->setFilesDispersion(false);
            $uploader->setFilenamesCaseSensitivity(false);
            $uploader->setAllowRenameFiles(true);

            $mapImageResult = $uploader->save(sprintf('%s/retail_store',
                $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath()));

            $mapImageName = sprintf('retail_store/%s', $this->arrays->getValue($mapImageResult, 'file'));
        } else {
            $mapObjectImage = $object->getDataUsingMethod('map_image');

            if (is_array($mapObjectImage) && $this->arrays->getValue($mapObjectImage, 'delete', 0) == 1) {
                $this->file->deleteFile(sprintf('%s/%s',
                    $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath(),
                    $this->arrays->getValue($mapObjectImage, 'value')));
            } else {
                if (is_array($mapObjectImage)) {
                    $mapImageName = $this->arrays->getValue($mapObjectImage, 'value');
                }
            }
        }

        $object->setDataUsingMethod('map_image', $mapImageName);

        $features = $object->getDataUsingMethod('feature');

        if (is_array($features) && count($features) === 1) {
            $featureId = reset($features);

            if ($featureId == 0) {
                $object->setDataUsingMethod('feature', null);
            }
        }

        parent::beforeSave($object);
    }
}
