<?php
/**
 * Tests getting and setting GD resource.
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
 * Tests getting and setting GD resource.
 * php version 7.3
 *
 * @category Tests
 * @package  PosterEditorTest
 * @author   Anton Lukin <anton@lukin.me>
 * @license  MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link     https://github.com/antonlukin/poster-editor
 */
class ResourceGDTest extends TestCase
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

        $resource = $image->get();
        imagefilter($resource, IMG_FILTER_COLORIZE, 0, 200, 0);
        $image->set($resource);

        $generatedPath = __DIR__ . '/output/resource.jpg';
        $referencePath = __DIR__ . '/references/resource.jpg';

        $image->save($generatedPath);

        // Compare file checksums
        $generatedHash = md5_file($generatedPath);
        $referenceHash = md5_file($referencePath);

        $this->assertEquals($referenceHash, $generatedHash);

        // Remove the temporary file after verification
        unlink($generatedPath);
    }
}
