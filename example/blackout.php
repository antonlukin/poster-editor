<?php
/**
 * Opacity example.
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

    // Create from image and fit to 1200x630 area.
    $image->make('images/bridge.jpg')->fit(1200, 630);

    // Add blackout filter
    $image->blackout(70);

    $image->text(
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat', // phpcs:ignore
        array(
            'x'          => '100',
            'y'          => '100',
            'width'      => 1000,
            'height'     => 400,
            'fontpath'   => __DIR__ . '/fonts/opensans.ttf',
            'fontsize'   => 24,
            'lineheight' => 1.75,
            'color'      => '#fff',
        )
    );

    // Show it.
    $image->show();

} catch(Exception $e) {
    echo $e->getMessage();
}
