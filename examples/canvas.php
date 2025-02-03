<?php
/**
 * Canvas image example.
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

    $image->canvas(
        500, 500,
        array(
            'color' => '#cccc00',
        )
    );

    $image->show();

} catch(Exception $e) {
    echo $e->getMessage();
}
