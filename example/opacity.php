<?php
/**
 * Opacity example.
 * php version 7.1
 *
 * @category PHP
 * @package  PosterImage
 * @author   Anton Lukin <anton@lukin.me>
 * @license  MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link     https://github.com/antonlukin/poster-image
 */

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $image = new PosterImage\PosterImage();

    // Create from image and fit to 1200x630 area.
    $image->make('images/bridge.jpg')->fit(1200, 630);

    // Create opacity layer
    $image->rectangle(
        0, 0, 1200, 630,
        array(
            'color'   => '#000',
            'opacity' => 60,
        )
    );

    $image->text(
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat', // phpcs:ignore
        array(
            'x'          => '100',
            'y'          => '100',
            'width'      => 1000,
            'height'     => 400,
            'fontfile'   => __DIR__ . '/fonts/opensans.ttf',
            'fontsize'   => 24,
            'lineheight' => 1.75,
            'color'      => '#fff',
        )
    );

    // Show it.
    $image->show();

} catch(Exception $e) {
    echo $e->getMessage();
}