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
    $file = file_get_contents('images/bridge.jpg');

    $image = new PosterEditor\PosterEditor();
    $image->make($file)->downsize(null, 200)->invert()->show();

    $image->insert('images/logo.png')->show('png');

} catch(Exception $e) {
    echo $e->getMessage();
}
