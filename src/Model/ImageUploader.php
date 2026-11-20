<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Model;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Model\AbstractModel;
use Magento\MediaStorage\Model\File\UploaderFactory;

/**
 * Saves the images of the forms of stores and features in the media directory "retail_store" and deletes them.
 */
class ImageUploader
{
    /** Directory of the images in the media directory. */
    public const DIRECTORY = 'retail_store';

    /** Allowed file extensions of the images. */
    public const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'gif', 'png', 'webp'];

    /**
     * @param UploaderFactory $uploaderFactory
     * @param Filesystem $fileSystem
     * @param AdapterFactory $imageAdapterFactory
     */
    public function __construct(
        protected UploaderFactory $uploaderFactory,
        protected Filesystem $fileSystem,
        protected AdapterFactory $imageAdapterFactory
    ) {
    }

    /**
     * Sets the image field of the object.
     *
     * The field gets the path of an uploaded image, an empty value if the image is deleted, or the current path.
     *
     * @param RequestInterface $request
     * @param AbstractModel $object
     * @param string $fieldName
     *
     * @return void
     * @throws Exception
     */
    public function processImageField(RequestInterface $request, AbstractModel $object, string $fieldName): void
    {
        $value = $object->getData($fieldName);

        if ($this->isUploaded($request, $fieldName)) {
            $object->setData($fieldName, $this->saveImage($fieldName));
        } elseif (is_array($value)) {
            $path = $this->getImagePath($value[ 'value' ] ?? null);

            if (($value[ 'delete' ] ?? null) == 1) {
                if ($path !== null) {
                    $this->deleteImage($path);
                }

                // sets the value null, so that the save removes the image (unsetData() would keep it)
                $object->setData($fieldName);
            } else {
                $object->setData($fieldName, $path);
            }
        } elseif ($value !== null && $this->getImagePath($value) === null) {
            $object->setData($fieldName, $object->getOrigData($fieldName));
        }
    }

    /**
     * Checks if a file was uploaded for the field.
     *
     * @param RequestInterface $request
     * @param string $fieldName
     *
     * @return bool
     */
    protected function isUploaded(RequestInterface $request, string $fieldName): bool
    {
        if (! method_exists($request, 'getFiles')) {
            return false;
        }

        $file = $request->getFiles($fieldName);

        return is_array($file) && ! empty($file[ 'name' ]) && ($file[ 'error' ] ?? UPLOAD_ERR_OK) === UPLOAD_ERR_OK;
    }

    /**
     * Saves the uploaded image of the field and returns its path in the media directory.
     *
     * @param string $fieldName
     *
     * @return string
     * @throws Exception
     */
    protected function saveImage(string $fieldName): string
    {
        $uploader = $this->uploaderFactory->create(['fileId' => $fieldName]);

        $uploader->setAllowedExtensions(self::ALLOWED_EXTENSIONS);
        $uploader->addValidateCallback(
            'retail_store_image',
            $this->imageAdapterFactory->create(),
            'validateUploadFile'
        );
        $uploader->setFilesDispersion(false);
        $uploader->setFilenamesCaseSensitivity(false);
        $uploader->setAllowRenameFiles(true);
        $uploader->setAllowCreateFolders(true);

        $result = $uploader->save(
            $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath(self::DIRECTORY)
        );

        $file = $result[ 'file' ] ?? null;

        if (! is_string($file) || $file === '') {
            throw new LocalizedException(__('The image could not be saved.'));
        }

        return sprintf('%s/%s', self::DIRECTORY, ltrim($file, '/'));
    }

    /**
     * Deletes the image with the path in the media directory.
     *
     * @param string $path
     *
     * @return void
     * @throws FileSystemException
     */
    protected function deleteImage(string $path): void
    {
        $mediaDirectory = $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA);

        if ($mediaDirectory->isFile($path)) {
            $mediaDirectory->delete($path);
        }
    }

    /**
     * Returns the path of an image in the directory of the images, or null for other values.
     *
     * This prevents that other files are deleted or used.
     *
     * @param mixed $value
     *
     * @return string|null
     */
    public function getImagePath(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $path = ltrim($value, '/');

        return preg_match('/^' . self::DIRECTORY . '\/[^\/\\\\]+$/', $path) && ! str_contains($path, '..') ?
            $path : null;
    }
}
