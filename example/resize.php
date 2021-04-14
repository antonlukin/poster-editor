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

    // Create from image and resize it down.
    $image->make('images/bridge.jpg')->resize(300, 500, false);

    // Show it.
    $image->show();

} catch(Exception $e) {
    echo $e->getMessage();
}