<?php
/**
 * Resize image example.
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

    // Create from image and crop 600x600 area.
    $image->make('images/bridge.jpg')->crop(600, 600, 100, 100);

    // Grayscale and invert.
    $image->grayscale()->invert();

    $image->insert(
        'images/logo.png',
        array(
            'x'      => '10%',
            'y'      => '10%',
            'width'  => 200,
        )
    );

    // Show it.
    $image->show(100);

} catch(Exception $e) {
    echo $e->getMessage();
}