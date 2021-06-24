<?php
/**
 * Canvas image example.
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

    $image->canvas(
        500, 500,
        array(
            'color' => '#cccc00'
        )
    );

    $image->show();

} catch(Exception $e) {
    echo $e->getMessage();
}
