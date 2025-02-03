<?php
/**
 * Tests drawing shapes.
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
 * Tests drawing shapes.
 * php version 7.3
 *
 * @category Tests
 * @package  PosterEditorTest
 * @author   Anton Lukin <anton@lukin.me>
 * @license  MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link     https://github.com/antonlukin/poster-editor
 */
class ShapesTest extends TestCase
{
    /**
     * Save and compare rendered image
     *
     * @return void
     */
    public function testRendring()
    {
        $image = new PosterEditor\PosterEditor();
        $image->make(ASSET_PATH . '/images/bridge.jpg')->fit(1000, 630, 'bottom');
        $image->contrast(5)->brightness(-30)->blur();

        $image->rectangle(
            20, 20, 960, 590,
            array(
                'color'   => '#ffffff',
                'outline' => true,
                'width'   => 4,
            )
        );

        $image->ellipse(
            200, 200, 200, 200,
            array(
                'color'   => '#00ff00',
                'opacity' => 50,
            )
        );

        $image->ellipse(
            800, 200, 200, 200,
            array(
                'color'   => '#ff0000',
                'opacity' => 50,
            )
        );

        $image->rectangle(
            480, 280, 80, 140,
            array(
                'color'   => '#0000ff',
            )
        );

        $image->line(
            200, 500, 800, 500,
            array(
                'color'   => array(255, 255, 0),
                'opacity' => 10,
                'width'   => 4,
            )
        );

        $generatedPath = __DIR__ . '/output/shapes.png';
        $referencePath = __DIR__ . '/references/shapes.png';

        $image->save($generatedPath);

        // Compare file checksums
        $generatedHash = md5_file($generatedPath);
        $referenceHash = md5_file($referencePath);

        $this->assertEquals($referenceHash, $generatedHash);

        // Remove the temporary file after verification
        unlink($generatedPath);
    }
}
