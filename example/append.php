<?php
/**
 * Append image example.
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
    $image->make('images/bridge.jpg')->fit(600, 300, 'top');

    $logo = new PosterEditor\PosterEditor();
    $logo->make('images/logo.png')->resize(300, 50)->grayscale();

    $image->append($logo)->show();

} catch(Exception $e) {
    echo $e->getMessage();
}
