<?php
/**
 * Insert image example.
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
    $image->make('../assets/images/bridge.jpg')->fit(1000, 600, 'bottom-left');
    $image->grayscale()->invert();
    $image->insert('../assets/images/logo.png');

    $image->show();

} catch(Exception $e) {
    echo $e->getMessage();
}
