<?php
/**
 * Tests the functionality of image and text opacity.
 * php version 7.1
 *
 * @category Tests
 * @package  AlphaImageTest
 * @author   Anton Lukin <anton@lukin.me>
 * @license  MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link     https://github.com/antonlukin/poster-editor
 */

use PHPUnit\Framework\TestCase;
use PosterEditor\PosterEditor;

/**
 * Test opacity images overlay
 *
 * @category Tests
 * @package  AlphaImageTest
 * @author   Anton Lukin <anton@lukin.me>
 * @license  MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link     https://github.com/antonlukin/poster-editor
 */
class AlphaImageTest extends TestCase
{
    /**
     * Tests the correct rendering of images with different opacity levels.
     *
     * @return void
     */
    public function testAlphaImageRendering()
    {
        $image = new PosterEditor();
        $image->canvas(500, 500, array('color' => '#fff'));

        $image->insert(
            ASSET_PATH . '/images/icon.png',
            array(
            'y' => 50,
            'opacity' => 0,
            )
        );

        $image->insert(
            file_get_contents(ASSET_PATH . '/images/icon.png'),
            array(
            'y' => 200,
            'opacity' => 40,
            )
        );

        $icon = new PosterEditor();
        $icon->make(file_get_contents(ASSET_PATH . '/images/icon.png'));

        $image->insert(
            $icon,
            array(
            'y' => 350,
            'opacity' => 80,
            )
        );

        $generatedPath = __DIR__ . '/output/alpha.png';
        $referencePath = __DIR__ . '/references/alpha.png';

        $image->save($generatedPath);

        // Compare file checksums
        $generatedHash = md5_file($generatedPath);
        $referenceHash = md5_file($referencePath);

        $this->assertEquals($referenceHash, $generatedHash);

        // Remove the temporary file after verification
        unlink($generatedPath);
    }
}
