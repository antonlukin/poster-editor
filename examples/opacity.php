<?php
/**
 * Opacity image and text support example.
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
    $image->canvas(1000, 400);

    $image->insert(
        ASSET_PATH . '/images/logo.png',
        array(
            'x' => 600,
            'y' => 0,
            'opacity' => 0,
        )
    );

    $image->insert(
        ASSET_PATH . '/images/logo.png',
        array(
            'x' => 600,
            'y' => 120,
            'opacity' => 40,
        )
    );

    $image->insert(
        ASSET_PATH . '/images/logo.png',
        array(
            'x' => 600,
            'y' => 240,
            'opacity' => 80,
        )
    );

    $image->text(
        'Opacity: 0. Lorem ipsum. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex eas commodo consequat',
        array(
            'x'          => 0,
            'y'          => 0,
            'width'      => 600,
            'fontpath'   => ASSET_PATH . '/fonts/opensans.ttf',
            'fontsize'   => 20,
            'lineheight' => 1.5,
            'color'      => '#fff',
            'opacity'    => 0,
        )
    );

    $image->text(
        'Opacity: 40. Lorem ipsum. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex eas commodo consequat',
        array(
            'x'          => 0,
            'y'          => 120,
            'width'      => 600,
            'fontpath'   => ASSET_PATH . '/fonts/opensans.ttf',
            'fontsize'   => 20,
            'lineheight' => 1.5,
            'color'      => '#fff',
            'opacity'    => 40,
        )
    );

    $image->text(
        'Opacity: 80. Lorem ipsum. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex eas commodo consequat',
        array(
            'x'          => 0,
            'y'          => 240,
            'width'      => 600,
            'fontpath'   => ASSET_PATH . '/fonts/opensans.ttf',
            'fontsize'   => 20,
            'lineheight' => 1.5,
            'color'      => '#fff',
            'opacity'    => 80,
        )
    );

    $image->show('png');

} catch(Exception $e) {
    echo $e->getMessage();
}
