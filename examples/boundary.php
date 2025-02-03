<?php
/**
 * Text boundary image example.
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
        'Large title with unknown height. Can be multi-line',
        array(
            'x'          => 50,
            'y'          => 100,
            'width'      => 800,
            'fontpath'   => ASSET_PATH . '/fonts/merriweather.ttf',
            'fontsize'   => 48,
            'lineheight' => 1.5,
            'color'      => '#9999ff',
        ),
        $boundary
    );

    $image->text(
        'This text appears right after title using smart boundaries',
        array(
            'x'          => 50,
            'y'          => 100 + $boundary['height'],
            'width'      => 800,
            'fontpath'   => ASSET_PATH . '/fonts/opensans.ttf',
            'fontsize'   => 20,
            'lineheight' => 1.5,
            'color'      => '#ff9999',
        ),
        $boundary
    );

    $image->show();

} catch(Exception $e) {
    echo $e->getMessage();
}
