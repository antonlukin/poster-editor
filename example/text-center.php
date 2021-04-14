<?php
/**
 * Text center image example.
 * php version 7.1
 *
 * @category PHP
 * @package  ImageText
 * @author   Anton Lukin <anton@lukin.me>
 * @license  MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link     https://github.com/antonlukin/image-text
 */

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $image = new ImageText\ImageText();

    // Create from image and fit 1200x630 area.
    $image->make('images/bridge.jpg')->fit(1200, 630);

    // Grayscale and invert.
    $image->grayscale()->brightness(-40);

    $image->text(
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat', // phpcs:ignore
        array(
            'x'          => '100',
            'y'          => '100',
            'width'      => 1000,
            'height'     => 400,
            'horizontal' => 'center',
            'vertical'   => 'center',
            'fontfile'   => __DIR__ . '/fonts/opensans.ttf',
            'fontsize'   => 24,
            'lineheight' => 1.75,
            'color'      => '#ffffff',
        )
    );

    // Show it.
    $image->show(100);

} catch(Exception $e) {
    echo $e->getMessage();
}