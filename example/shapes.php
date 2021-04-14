<?php
/**
 * Shapes image example.
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

    // Create from image and fit it from bottom.
    $image->make('images/bridge.jpg')->fit(1000, 630, 'bottom');

    // Set filters.
    $image->contrast(5)->brightness(-30);

    $image->rectangle(
        20, 20, 960, 590,
        array(
            'color'   => '#ffffff',
            'outline' => true,
            'width'   => 4,
        )
    );

    $image->ellipse(
        200, 200, 200, 200,
        array(
            'color'   => '#00ff00',
            'opacity' => 50,
        )
    );

    $image->ellipse(
        800, 200, 200, 200,
        array(
            'color'   => '#ff0000',
            'opacity' => 50,
        )
    );

    $image->rectangle(
        480, 280, 80, 140,
        array(
            'color'   => '#0000ff',
        )
    );

    $image->line(
        200, 500, 800, 500,
        array(
            'color'   => array(255, 255, 0),
            'opacity' => 10,
            'width'   => 4,
        )
    );

    // Show as png without compressing.
    $image->show(100, 'png');

} catch(Exception $e) {
    echo $e->getMessage();
}