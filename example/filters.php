<?php
/**
 * Filters image example.
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

    // Create from image.
    $image->make('images/bridge.jpg');

    // Set filters.
    $image->blur(2)->contrast(25)->brightness(-10);

    // Show it.
    $image->show();

} catch(Exception $e) {
    echo $e->getMessage();
}