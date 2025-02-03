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
    $image->make(ASSET_PATH . '/images/bridge.jpg')->fit(1200, 630, 'bottom')->blackout(50);

    $logo = new PosterEditor\PosterEditor();
    $logo->make(ASSET_PATH . '/images/logo.png')->downsize(150, null);

    $image->insert($logo, array('x' => 50, 'y' => 50));
    $image->show();

} catch(Exception $e) {
    echo $e->getMessage();
}