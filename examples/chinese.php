<?php
/**
 * Chinese separators example.
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
    $image->make(ASSET_PATH . '/images/bridge.jpg')->crop(
        900, 600,
        array(
            'x' => '0',
            'y' => '100'
        )
    );

    $image->grayscale()->brightness(-40);

    $image->text(
        "大家小時候都有寫過紀念冊嗎？通過紀念冊上的文字和圖案，的回憶記錄下來。今年我們希望製作出所有星詠。透過成員互相分享與回憶，化為各地星詠者對星街的支持，以及星詠者之間的連繫與羈絆。",
        array(
            'x'          => 20,
            'y'          => 0,
            'width'      => 860,
            'horizontal' => 'start',
            'vertical'   => 'center',
            'fontpath'   => ASSET_PATH . '/fonts/notosans-tc-regular.otf',
            'lineheight' => 1.75,
            'fontsize'   => 18,
            'color'      => '#ffffff',
            'opacity'    => 0,
        )
    );

    $image->show();

} catch(Exception $e) {
    echo $e->getMessage();
}
