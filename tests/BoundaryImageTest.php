<?php
/**
 * Tests the functionality of adding text with boundaries on an image.
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
 * Tests the functionality of adding text with boundaries on an image.
 * php version 7.1
 *
 * @category Tests
 * @package  BoundaryImageTest
 * @author   Anton Lukin <anton@lukin.me>
 * @license  MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link     https://github.com/antonlukin/poster-editor
 */
class BoundaryImageTest extends TestCase
{
    /**
     * Tests adding multi-line text within boundaries and verifies the output matches the reference.
     *
     * @return void
     */
    public function testTextBoundaryRendering()
    {
        $image = new PosterEditor();
        $image->make(__DIR__ . '/images/bridge.jpg')->crop(900, 600, ['x' => 0, 'y' => 100]);

        $image->grayscale()->brightness(-40);

        $image->text(
            'Large title with unknown size. Can be multi-line',
            array(
                'x'          => 50,
                'y'          => 100,
                'width'      => 800,
                'fontpath'   => __DIR__ . '/fonts/merriweather.ttf',
                'fontsize'   => 48,
                'lineheight' => 1.5,
                'color'      => '#9999ff',
            ),
            $boundary
        );

        $image->text(
            'This text appears right after title using smart boundaries',
            array(
                'x'          => 50,
                'y'          => $boundary['y'] + $boundary['height'],
                'width'      => 800,
                'fontpath'   => __DIR__ . '/fonts/opensans.ttf',
                'fontsize'   => 20,
                'lineheight' => 1.5,
                'color'      => '#ff9999',
            ),
            $boundary
        );

        $generatedPath = __DIR__ . '/output/boundary.jpg';
        $referencePath = __DIR__ . '/references/boundary.jpg';

        $image->save($generatedPath);

        // Compare file checksums
        $generatedHash = md5_file($generatedPath);
        $referenceHash = md5_file($referencePath);

        $this->assertEquals($referenceHash, $generatedHash);

        // Remove the temporary file after verification
        unlink($generatedPath);
    }
}
