<?php
/**
 * Append image example.
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
    $image->make('images/bridge.jpg')->fit(600, 300, 'top');

    $logo = new PosterImage\PosterImage();
    $logo->make('images/logo.png')->resize(300, 50)->grayscale();

    $image->append($logo)->show();

} catch(Exception $e) {
    echo $e->getMessage();
}