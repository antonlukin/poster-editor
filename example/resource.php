<?php
/**
 * Resource example.
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

    // Create from image and fit 600x600 area.
    $image->make('images/bridge.jpg')->fit(600, 600);

    // Get resource
    $resource = $image->getResource();

    // Use some raw GD functions
    imagefilter($resource, IMG_FILTER_COLORIZE, 0, 200, 0);

    // Set updated resource
    $image->setResource($resource);

    // Show it.
    $image->show();

} catch(Exception $e) {
    echo $e->getMessage();
}