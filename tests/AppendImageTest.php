<?php
/**
 * Tests the functionality of appending a logo to an image.
 * php version 7.1
 *
 * @category Tests
 * @package  PosterEditorTest
 * @author   Anton Lukin <anton@lukin.me>
 * @license  MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link     https://github.com/antonlukin/poster-editor
 */

use PHPUnit\Framework\TestCase;
use PosterEditor\PosterEditor;

/**
 * Tests the functionality of appending a logo to an image
 *
 * @category Tests
 * @package  AppendImageTest
 * @author   Anton Lukin <anton@lukin.me>
 * @license  MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link     https://github.com/antonlukin/poster-editor
 */
class AppendImageTest extends TestCase
{
    /**
     * Tests inserting a logo onto an image and verifies the output matches the reference.
     *
     * @return void
     */
    public function testAppendLogoToImage()
    {
        $image = new PosterEditor();
        $image->make(__DIR__ . '/images/bridge.jpg')
            ->fit(1200, 630, 'bottom')
            ->blackout(50);

        $logo = new PosterEditor();
        $logo->make(__DIR__ . '/images/logo.png')->downsize(150, null);

        $image->insert($logo, array('x' => 50, 'y' => 50));

        $generatedPath = __DIR__ . '/output/append.jpg';
        $referencePath = __DIR__ . '/references/append.jpg';

        $image->save($generatedPath);

        // Compare file checksums
        $generatedHash = md5_file($generatedPath);
        $referenceHash = md5_file($referencePath);

        $this->assertEquals($referenceHash, $generatedHash);

        // Remove the temporary file after verification
        unlink($generatedPath);
    }
}
