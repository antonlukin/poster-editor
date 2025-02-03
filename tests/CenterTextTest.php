<?php
/**
 * Tests the functionality of centering text.
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
 * Tests the functionality of centering text.
 * php version 7.3
 *
 * @category Tests
 * @package  PosterEditorTest
 * @author   Anton Lukin <anton@lukin.me>
 * @license  MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link     https://github.com/antonlukin/poster-editor
 */
class CenterTextTest extends TestCase
{
    /**
     * Save and compare rendered image
     *
     * @return void
     */
    public function testRendring()
    {
        $image = new PosterEditor\PosterEditor();
        $image->make(ASSET_PATH . '/images/bridge.jpg')->fit(1200, 630);
        $image->grayscale()->brightness(-40);

        $image->text(
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat Lorem ipsum dolor sit amet', // phpcs:ignore
            array(
            'x'          => 100,
            'y'          => 100,
            'width'      => 1000,                 // Calculate width for nulled values
            'height'     => 400,                  // Calculate height for nulled values
            'horizontal' => 'center',             // Can be left/right/center/justify
            'vertical'   => 'center',             // Can be top/center/bottom/justify
            'fontpath'   => ASSET_PATH. '/fonts/opensans.ttf',
            'fontsize'   => 24,
            'lineheight' => 1.75,
            'color'      => '#ffffff',
            'opacity'    => 0,
            )
        );

        $generatedPath = __DIR__ . '/output/center.jpg';
        $referencePath = __DIR__ . '/references/center.jpg';

        $image->save($generatedPath);

        // Compare file checksums
        $generatedHash = md5_file($generatedPath);
        $referenceHash = md5_file($referencePath);

        $this->assertEquals($referenceHash, $generatedHash);

        // Remove the temporary file after verification
        unlink($generatedPath);
    }
}
