<?php
/**
 * Tests long text without spaces.
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
 * Tests long text without spaces.
 * php version 7.3
 *
 * @category Tests
 * @package  PosterEditorTest
 * @author   Anton Lukin <anton@lukin.me>
 * @license  MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link     https://github.com/antonlukin/poster-editor
 */
class NoSpaceText extends TestCase
{
    /**
     * Save and compare rendered image
     *
     * @return void
     */
    public function testRendring()
    {
        $image = new PosterEditor\PosterEditor();

        $image->make(ASSET_PATH . '/images/bridge.jpg')->fit(600, 600);
        $image->grayscale()->brightness(-40);

        $image->text(
            '123456789012345678901234567890123456789012345678901234567890123456789012345', // phpcs:ignore
            array(
                'x'          => 100,
                'y'          => 100,
                'width'      => 400,
                'height'     => 200,
                'horizontal' => 'left',
                'vertical'   => 'top',
                'fontpath'   => ASSET_PATH . '/fonts/merriweather.ttf',
                'fontsize'   => 24,
                'lineheight' => 1.75,
                'color'      => '#ffffff',
            )
        );

        $generatedPath = __DIR__ . '/output/nospace.png';
        $referencePath = __DIR__ . '/references/nospace.png';

        $image->save($generatedPath);

        // Compare file checksums
        $generatedHash = md5_file($generatedPath);
        $referenceHash = md5_file($referencePath);

        $this->assertEquals($referenceHash, $generatedHash);

        // Remove the temporary file after verification
        unlink($generatedPath);
    }
}
