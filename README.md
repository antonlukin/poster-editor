# Poster Image

This class is an alternative to the package [Intervention Image](https://github.com/Intervention/image) for more flexible work with text on the image.
Use it if you need to **fit text** in a given area, automatically **calculate font size** and change **line height**. 
The text drawing method also knows how to return the actual size of the inscriptions, which will allow, for example, to place blocks under each other.

In addition to flexible work with text, the class provides an Image API similar to the Intervention package. 
Including smart poster resizing, filters, drawing shapes, and overlaying other images.

If you are not using composer in your work, you may also be interested in the simplicity of the class and the absence of any dependencies.
Note that this class supports PHP-GD driver only.
You can also easily inherit your class — all methods of the parent can be overridden.

## Installation
The best way to install Poster Image is quickly and easily with [Composer](http://getcomposer.org/).  
However, you can require the class directly without using a loader - this will not affect performance in any way.

`php composer.phar require antonlukin/poster-image`

## Usage
In case of an error, the class methods return an exception.  
Therefore, it is best to call them inside a block `try..catch`.

**Example**
```php
// Using composer
require_once __DIR__ . '/vendor/autoload.php';

// Or directly
// require_once __DIR__ . '/PosterImage.php';

try {
    $image = new PosterImage\PosterImage();
    $image->make('images/bridge.jpg')->fit(600, 600);
    $image->show();

} catch(Exception $e) {
    echo $e->getMessage();
}
```

## Contribution
All project code is stored on Github. The best way to help the project is to report a bug or add some new functionality. 
You can also open here an [issue](https://github.com/antonlukin/poster-image/issues) or send a [pull reguest](https://github.com/antonlukin/poster-image/pulls).