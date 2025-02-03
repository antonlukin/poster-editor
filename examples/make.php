<?php
/**
 * Append image example.
 * php version 7.3
 *
 * @category PHP
 * @package  PosterEditor
 * @author   Anton Lukin <anton@lukin.me>
 * @license  MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link     https://github.com/antonlukin/poster-editor
 */

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $image = new PosterEditor\PosterEditor();
    $image->make(file_get_contents('../assets/images/bridge.jpg'))->downsize(null, 1000)->invert();

    $logo = new PosterEditor\PosterEditor();
    $logo->make('../assets/images/logo.png')->downsize(null, 100)->invert();

    $image->insert($logo, array('y' => 0))->show('png');

} catch(Exception $e) {
    echo $e->getMessage();
}
