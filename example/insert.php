<?php
/**
 * Insert image example.
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

    // Create from image and crop 600x600 area.
    $image->make('images/bridge.jpg')->fit(600, 600, 'bottom-right');

    // Grayscale and invert.
    $image->grayscale()->invert();

    $image->insert(
        'images/logo.png',
        array(
            'width'  => 500,
        )
    );

    // Show it.
    $image->show(100);

} catch(Exception $e) {
    echo $e->getMessage();
}
