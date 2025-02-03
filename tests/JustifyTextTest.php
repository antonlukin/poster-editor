<?php
/**
 * Tests the functionality of justifying text.
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
 * Tests the functionality of justifying text.
 * php version 7.3
 *
 * @category Tests
 * @package  PosterEditorTest
 * @author   Anton Lukin <anton@lukin.me>
 * @license  MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link     https://github.com/antonlukin/poster-editor
 */
class JustifyTextTest extends TestCase
{
    /**
     * Save and compare rendered image
     *
     * @return void
     */
    public function testRendring()
    {
        $image = new PosterEditor\PosterEditor();
        $image->make(ASSET_PATH . '/images/bridge.jpg')->fit(900, 600);
        $image->blackout(70);

        $image->text(
        'Lorem ipsum dolor d d 4 g sit amet, consectetur adipiscing et, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex eas commodo consequat sdfsdfl', // phpcs:ignore
            array(
                'x'          => 100,
                'y'          => 100,
                'width'      => 600,                   // Calculate width for nulled values
                'height'     => 300,                   // Calculate height for nulled values
                'horizontal' => 'justify',             // Can be left/right/center/justify
                'vertical'   => 'justify',             // Can be top/center/bottom/justify
                'fontpath'   => ASSET_PATH . '/fonts/opensans.ttf',
                'fontsize'   => 20,
                'lineheight' => 1.5,
                'color'      => '#ffffff',
                'opacity'    => 0,
            )
        );

        $generatedPath = __DIR__ . '/output/justify.png';
        $referencePath = __DIR__ . '/references/justify.png';

        $image->save($generatedPath);

        // Compare file checksums
        $generatedHash = md5_file($generatedPath);
        $referenceHash = md5_file($referencePath);

        $this->assertEquals($referenceHash, $generatedHash);

        // Remove the temporary file after verification
        unlink($generatedPath);
    }
}
