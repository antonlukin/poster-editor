<?php
/**
 * Text center image example.
 * php version 7.1
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
    $image->make('images/bridge.jpg')->fit(1200, 630);
    $image->grayscale()->brightness(-40);

    $image->text(
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat Lorem ipsum dolor sit amet', // phpcs:ignore
        array(
            'x'          => 100,
            'y'          => 100,
            'width'      => 1000,                 // Calculate width for nulled values
            'height'     => 400,                  // Calculate height for nulled values
            'horizontal' => 'center',             // Can be left/right/center/justify
            'vertical'   => 'center',             // Can be top/center/bottom/justify
            'fontpath'   => 'fonts/opensans.ttf',
            'fontsize'   => 24,
            'lineheight' => 1.75,
            'color'      => '#ffffff',
            'opacity'    => 1,
            'debug'      => true,
        )
    );

    $image->show('jpg', 70);

} catch(Exception $e) {
    echo $e->getMessage();
    exit;
}
