<?php
/**
 * Tests core functionalities of the PosterEditor class.
 * php version 7.3
 *
 * @category Tests
 * @package  PosterEditorTest
 * @author   Anton Lukin <anton@lukin.me>
 * @license  MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link     https://github.com/antonlukin/poster-editor
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests core functionalities of the PosterEditor class.
 *
 * @category Tests
 * @package  PosterEditorTest
 * @author   Anton Lukin <anton@lukin.me>
 * @license  MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link     https://github.com/antonlukin/poster-editor
 */
class CoreFunctionTest extends TestCase
{
    /**
     * Tests if class can be instantiated
     *
     * @return void
     */
    public function testCanBeInstantiated()
    {
        $editor = new PosterEditor\PosterEditor();
        $this->assertInstanceOf(PosterEditor\PosterEditor::class, $editor);
    }

    /**
     * Tests if a canvas is created with the specified dimensions.
     *
     * @return void
     */
    public function testCanvasCreation()
    {
        $editor = new PosterEditor\PosterEditor();
        $editor->canvas(800, 600);

        $this->assertInstanceOf(PosterEditor\PosterEditor::class, $editor);
        $this->assertEquals(800, $editor->width());
        $this->assertEquals(600, $editor->height());
    }

    /**
     * Tests resizing and cropping of an image.
     *
     * @return void
     */
    public function testResizeAndCrop()
    {
        $editor = new PosterEditor\PosterEditor();
        $editor->canvas(800, 600);
        $editor->resize(400, 300);

        $this->assertEquals(400, $editor->width());
        $this->assertEquals(300, $editor->height());

        $editor->crop(200, 150);
        $this->assertEquals(200, $editor->width());
        $this->assertEquals(150, $editor->height());
    }

    /**
     * Tests saving and loading an image correctly.
     *
     * @return void
     */
    public function testSaveAndLoad()
    {
        $editor = new PosterEditor\PosterEditor();
        $editor->canvas(800, 600);

        $generatedPath = __DIR__ . '/output/saved.jpg';

        $editor->save($generatedPath);
        $this->assertFileExists($generatedPath);

        $loadedEditor = new PosterEditor\PosterEditor();
        $loadedEditor->make($generatedPath);

        $this->assertEquals($editor->width(), $loadedEditor->width());
        $this->assertEquals($editor->height(), $loadedEditor->height());

        unlink($generatedPath);
    }
}
