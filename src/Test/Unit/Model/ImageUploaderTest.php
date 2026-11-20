<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Test\Unit\Model;

use Exception;
use Infrangible\RetailStore\Model\ImageUploader;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Model\AbstractModel;
use Magento\MediaStorage\Model\File\UploaderFactory;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests that only images in the directory of the images are used and deleted.
 */
#[AllowMockObjectsWithoutExpectations]
class ImageUploaderTest extends TestCase
{
    /** @var WriteInterface&MockObject */
    private MockObject $mediaDirectory;

    /**
     * Creates the media directory.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->mediaDirectory = $this->createMock(WriteInterface::class);
    }

    /**
     * Returns paths and if they are paths of images.
     *
     * @return array
     * @phpstan-return array<string, array{mixed, string|null}>
     */
    public static function imagePathProvider(): array
    {
        return [
            'image'             => ['retail_store/store.png', 'retail_store/store.png'],
            'leading slash'     => ['/retail_store/store.png', 'retail_store/store.png'],
            'other directory'   => ['catalog/store.png', null],
            'parent directory'  => ['retail_store/../../app/etc/env.php', null],
            'sub directory'     => ['retail_store/a/store.png', null],
            'backslash'         => ['retail_store/..\\env.php', null],
            'dots in file name' => ['retail_store/..', null],
            'no string'         => [['retail_store/store.png'], null],
            'empty'             => ['', null]
        ];
    }

    /**
     * Tests the paths of images.
     *
     * @dataProvider imagePathProvider
     *
     * @param mixed $value
     * @param string|null $expected
     *
     * @return void
     */
    #[DataProvider('imagePathProvider')]
    public function testGetImagePath(mixed $value, ?string $expected): void
    {
        $this->assertSame($expected, $this->createUploader()->getImagePath($value));
    }

    /**
     * Tests that a deleted image is removed from the object and the media directory.
     *
     * @return void
     * @throws Exception
     */
    public function testProcessImageFieldDelete(): void
    {
        $object = $this->createObject(['image' => ['value' => 'retail_store/store.png', 'delete' => '1']]);

        $this->mediaDirectory->method('isFile')->willReturn(true);
        $this->mediaDirectory->expects($this->once())->method('delete')->with('retail_store/store.png');

        $this->createUploader()->processImageField($this->createRequest(), $object, 'image');

        $this->assertNull($object->getData('image'));
    }

    /**
     * Tests that files outside the directory of the images are not deleted.
     *
     * @return void
     * @throws Exception
     */
    public function testProcessImageFieldDeleteOtherFile(): void
    {
        $object = $this->createObject(['image' => ['value' => '../../app/etc/env.php', 'delete' => '1']]);

        $this->mediaDirectory->expects($this->never())->method('delete');

        $this->createUploader()->processImageField($this->createRequest(), $object, 'image');

        $this->assertNull($object->getData('image'));
    }

    /**
     * Tests that the current image is kept, and that other paths are replaced by the original value.
     *
     * @return void
     * @throws Exception
     */
    public function testProcessImageFieldKeep(): void
    {
        $uploader = $this->createUploader();

        $object = $this->createObject(['image' => ['value' => 'retail_store/store.png']]);
        $uploader->processImageField($this->createRequest(), $object, 'image');
        $this->assertSame('retail_store/store.png', $object->getData('image'));

        $object = $this->createObject(['image' => 'retail_store/new.png']);
        $uploader->processImageField($this->createRequest(), $object, 'image');
        $this->assertSame('retail_store/new.png', $object->getData('image'));

        $object = $this->createObject(['image' => '/etc/passwd'], ['image' => 'retail_store/store.png']);
        $uploader->processImageField($this->createRequest(), $object, 'image');
        $this->assertSame('retail_store/store.png', $object->getData('image'));
    }

    /**
     * Returns a model with the data and original data.
     *
     * @param array $data
     * @param array $origData
     * @phpstan-param array<string, mixed> $data
     * @phpstan-param array<string, mixed> $origData
     *
     * @return AbstractModel
     */
    private function createObject(array $data, array $origData = []): AbstractModel
    {
        $object = $this->getMockBuilder(AbstractModel::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        foreach ($origData as $key => $value) {
            $object->setOrigData($key, $value);
        }

        $object->setData($data);

        return $object;
    }

    /**
     * Returns a request without uploaded files.
     *
     * @return Http
     */
    private function createRequest(): Http
    {
        $request = $this->createStub(Http::class);
        $request->method('getFiles')->willReturn(null);

        return $request;
    }

    /**
     * Returns the uploader with the media directory.
     *
     * @return ImageUploader
     */
    private function createUploader(): ImageUploader
    {
        $fileSystem = $this->createStub(Filesystem::class);
        $fileSystem->method('getDirectoryWrite')->willReturn($this->mediaDirectory);

        return new ImageUploader(
            $this->createStub(UploaderFactory::class),
            $fileSystem,
            $this->createStub(AdapterFactory::class)
        );
    }
}
