<?php
/**
 * Long text without spaces example.
 * php version 7.3
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

    // Create from image and fit 1200x630 area.
    $image->make('../assets/images/bridge.jpg')->fit(1200, 630);

    // Grayscale and invert.
    $image->grayscale()->brightness(-40);

    $image->text(
        '123456789012345678901234567890123456789012345678901234567890123456789012345', // phpcs:ignore
        array(
            'x'          => 100,
            'y'          => 100,
            'width'      => 500,
            'height'     => 200,
            'horizontal' => 'left',
            'vertical'   => 'top',
            'fontpath'   => '../assets/fonts/merriweather.ttf',
            'fontsize'   => 24,
            'lineheight' => 1.75,
            'color'      => '#ffffff',
            'debug'      => true,
        )
    );

    // Save it.
    $image->show();

} catch(Exception $e) {
    echo $e->getMessage();
}
