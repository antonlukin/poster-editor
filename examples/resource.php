<?php
/**
 * Resource example.
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
    $image->make(ASSET_PATH . '/images/bridge.jpg')->fit(600, 600);

    $resource = $image->get();
    imagefilter($resource, IMG_FILTER_COLORIZE, 0, 200, 0);
    $image->set($resource);

    $image->show();

} catch(Exception $e) {
    echo $e->getMessage();
}
