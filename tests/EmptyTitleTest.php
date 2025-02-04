<?php
/**
 * Test empty titles breaklines for string starting with 0.
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
 * Test empty titles breaklines for string starting with 0.
 * php version 7.3
 *
 * @category Tests
 * @package  PosterEditorTest
 * @author   Anton Lukin <anton@lukin.me>
 * @license  MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link     https://github.com/antonlukin/poster-editor
 */
class EmptyTitleTest extends TestCase
{
    /**
     * Save and compare rendered image
     *
     * @return void
     */
    public function testRendring()
    {
        $image = new PosterEditor\PosterEditor();
        $image->canvas(500, 500);

        $image->text(
            "0 DAYS 0 HOURS", array(
                'color' => '#fff',
                'fontpath' => ASSET_PATH . '/fonts/liquidcrystal-bolditalic.otf',
                'x' => 0,
                'y' => 30,
                'width' => 500,
                'height' => 390,
                'horizontal' => 'center',
                'vertical' => 'center',
                'fontsize' => 50,
                'lineheight' => 1.75,
                'opacity' => 0,
            )
        );

        $generatedPath = __DIR__ . '/output/empty.png';
        $referencePath = __DIR__ . '/references/empty.png';

        $image->save($generatedPath);

        // Compare file checksums
        $generatedHash = md5_file($generatedPath);
        $referenceHash = md5_file($referencePath);

        $this->assertEquals($referenceHash, $generatedHash);

        // Remove the temporary file after verification
        unlink($generatedPath);
    }
}
