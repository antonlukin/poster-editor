<?php
/**
 * Tests making image from binary and path file.
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
 * Tests making image from binary and path file.
 * php version 7.3
 *
 * @category Tests
 * @package  PosterEditorTest
 * @author   Anton Lukin <anton@lukin.me>
 * @license  MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link     https://github.com/antonlukin/poster-editor
 */
class MakeTest extends TestCase
{
    /**
     * Save and compare rendered image
     *
     * @return void
     */
    public function testRendring()
    {
        $image = new PosterEditor\PosterEditor();
        $image->make(file_get_contents(ASSET_PATH . '/images/bridge.jpg'))->downsize(null, 1000)->invert();

        $logo = new PosterEditor\PosterEditor();
        $logo->make(ASSET_PATH . '/images/logo.png')->downsize(null, 100)->invert();

        $image->insert($logo, array('y' => 0));

        $generatedPath = __DIR__ . '/output/make.png';
        $referencePath = __DIR__ . '/references/make.png';

        $image->save($generatedPath);

        // Compare file checksums
        $generatedHash = md5_file($generatedPath);
        $referenceHash = md5_file($referencePath);

        $this->assertEquals($referenceHash, $generatedHash);

        // Remove the temporary file after verification
        unlink($generatedPath);
    }
}
