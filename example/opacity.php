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
    $image->canvas(1000, 400);

    $image->insert(
        'images/logo.png',
        array(
            'x' => 600,
            'y' => 0,
            'height' => 100,
            'opacity' => 0,
        )
    );

    $image->insert(
        'images/logo.png',
        array(
            'x' => 600,
            'y' => 120,
            'height' => 100,
            'opacity' => 40,
        )
    );

    $image->insert(
        'images/logo.png',
        array(
            'x' => 600,
            'y' => 240,
            'height' => 100,
            'opacity' => 80,
        )
    );

    $image->text(
        'Opacity: 0. Lorem ipsum. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex eas commodo consequat',
        array(
            'x'          => 0,
            'y'          => 0,
            'width'      => 600,
            'fontpath'   => 'fonts/opensans.ttf',
            'fontsize'   => 20,
            'lineheight' => 1.5,
            'color'      => '#fff',
            'opacity'    => 0,
        )
    );

    $image->text(
        'Opacity: 40. Lorem ipsum. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex eas commodo consequat',
        array(
            'x'          => 0,
            'y'          => 120,
            'width'      => 600,
            'fontpath'   => 'fonts/opensans.ttf',
            'fontsize'   => 20,
            'lineheight' => 1.5,
            'color'      => '#fff',
            'opacity'    => 40,
        )
    );

    $image->text(
        'Opacity: 80. Lorem ipsum. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex eas commodo consequat',
        array(
            'x'          => 0,
            'y'          => 240,
            'width'      => 600,
            'fontpath'   => 'fonts/opensans.ttf',
            'fontsize'   => 20,
            'lineheight' => 1.5,
            'color'      => '#fff',
            'opacity'    => 80,
        )
    );

    $image->show();

} catch(Exception $e) {
    echo $e->getMessage();
}
