# Image Text

Wrapper for PHP's GD Library for easy image manipulation to resize, crop and draw images on top of each other preserving transparency, writing text with transparency and drawing shapes.

Based on https://github.com/kus/php-image

## Installation

Place the PHP file on your server and include it in your script.  

## Usage

```php
require_once '../src/ImageText.php';

(new PHPImage('./img/benji.jpg'))->resize(1000, 1000, true)->show();
```