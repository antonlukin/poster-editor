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

if (!defined('ASSET_PATH')) {
    define('ASSET_PATH', __DIR__ . '/../assets');
}

try {
    $image = new PosterEditor\PosterEditor();
    $image->make(file_get_contents(ASSET_PATH . '/images/bridge.jpg'))->downsize(null, 1000)->invert();

    $logo = new PosterEditor\PosterEditor();
    $logo->make(ASSET_PATH . '/images/logo.png')->downsize(null, 100)->invert();

    $image->insert($logo, array('y' => 0))->show('png');

} catch(Exception $e) {
    echo $e->getMessage();
}
