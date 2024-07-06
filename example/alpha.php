<?php
/**
 * Opacity image and text support example.
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
    $image->canvas(500, 500, array('color' => '#fff'));

    $image->insert(
        'images/icon.png',
        array(
            'y' => 50,
            'opacity' => 0,
        )
    );

    $image->insert(
        file_get_contents('images/icon.png'),
        array(
            'y' => 200,
            'opacity' => 40,
        )
    );

    $icon = new PosterEditor\PosterEditor();
    $icon->make(file_get_contents('images/icon.png'));

    $image->insert(
        $icon,
        array(
            'y' => 350,
            'opacity' => 80,
        )
    );

    $image->show('png');

} catch(Exception $e) {
    echo $e->getMessage();
}
