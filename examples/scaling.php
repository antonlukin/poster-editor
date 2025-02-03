<?php
/**
 * Text scaling image example.
 * php version 7.3
 *
 * @category PHP
 * @package  PosterEditor
 * @author   Anton Lukin <anton@lukin.me>
 * @license  MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link     https://github.com/antonlukin/poster-editor
 */

require_once __DIR__ . '/../vendor/autoload.php';

if (!defined('ASSET_PATH')) {
    define('ASSET_PATH', __DIR__ . '/../assets');
}

$time_start = microtime(true);

try {
    $image = new PosterEditor\PosterEditor();

    // Create from image and fit 1200x630 area.
    $image->make(ASSET_PATH . '/images/bridge.jpg')->fit(1200, 630);

    // Grayscale and invert.
    $image->grayscale()->brightness(-40);

    $image->text(
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', // phpcs:ignore
        array(
            'x'          => 100,
            'y'          => 100,
            'width'      => 900,
            'height'     => 200,
            'horizontal' => 'left',
            'vertical'   => 'top',
            'fontpath'   => ASSET_PATH . '/fonts/merriweather.ttf',
            'fontsize'   => 100,
            'lineheight' => 1.75,
            'color'      => '#ffffff',
            'debug'      => true,
        )
    );

    // Save it.
    $image->save('/tmp/temp.jpg');

} catch(Exception $e) {
    echo $e->getMessage();
}

echo '<b>Total Execution Time:</b> ' . round((microtime(true) - $time_start), 5);
