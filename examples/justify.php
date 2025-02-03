<?php
/**
 * Text center image example.
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
    $image->make(ASSET_PATH . '/images/bridge.jpg')->fit(900, 600);
    $image->blackout(70);

    $image->text(
        'Lorem ipsum dolor d d 4 g sit amet, consectetur adipiscing et, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex eas commodo consequat sdfsdfl', // phpcs:ignore
        array(
            'x'          => 100,
            'y'          => 100,
            'width'      => 600,                   // Calculate width for nulled values
            'height'     => 300,                   // Calculate height for nulled values
            'horizontal' => 'justify',             // Can be left/right/center/justify
            'vertical'   => 'justify',             // Can be top/center/bottom/justify
            'fontpath'   => ASSET_PATH . '/fonts/opensans.ttf',
            'fontsize'   => 20,
            'lineheight' => 1.5,
            'color'      => '#ffffff',
            'opacity'    => 0,
            'debug'      => true,
        )
    );

    $image->show('png');

} catch(Exception $e) {
    echo $e->getMessage();
    exit;
}
