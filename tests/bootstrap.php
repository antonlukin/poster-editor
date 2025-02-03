<?php
/**
 * Bootstrap file for PHPUnit tests.
 * Defines common constants and autoloads necessary classes.
 * php version 7.3
 *
 * @category Configuration
 * @package  PosterEditorTest
 * @author   Anton Lukin <anton@lukin.me>
 * @license  MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link     https://github.com/antonlukin/poster-editor
 */

// Define the base path for shared assets
define('ASSET_PATH', __DIR__ . '/../assets/');

// Autoload the library
require_once __DIR__ . '/../vendor/autoload.php';
