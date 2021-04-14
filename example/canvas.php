<?php
/**
 * Canvas image example.
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

    // Create canvas.
    $image->canvas(500, 500);

    // Set canvas color
    $image->rectangle(
        0, 0, 500, 500,
        array(
            'color' => '#cccc00'
        )
    );

    $image->show();

} catch(Exception $e) {
    echo $e->getMessage();
}