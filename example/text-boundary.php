<?php
/**
 * Text boundary image example.
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

    // Create from image and crop 900x600 left-bottom area.
    $image->make('images/bridge.jpg')->crop(
        900, 600,
        array(
            'x' => '0',
            'y' => '100%'
        )
    );

    // Add filters
    $image->grayscale()->brightness(-40);

    // Draw title with unknown height
    $image->text(
        'Large title with unknown height. Can be multi-line',
        array(
            'x'          => 50,
            'y'          => 100,
            'width'      => 800,
            'fontfile'   => __DIR__ . '/fonts/alice.ttf',
            'fontsize'   => 48,
            'lineheight' => 1.5,
            'color'      => '#9999ff',
        ),
        $boundary
    );

    // Draw text right after title
    $image->text(
        'This text appears right after title using smart boundaries',
        array(
            'x'          => 50,
            'y'          => 100 + $boundary['height'],
            'width'      => 800,
            'fontfile'   => __DIR__ . '/fonts/opensans.ttf',
            'fontsize'   => 20,
            'lineheight' => 1.5,
            'color'      => '#ff9999',
        ),
        $boundary
    );

    // Show it.
    $image->show();

} catch(Exception $e) {
    echo $e->getMessage();
}