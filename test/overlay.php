<?php

require_once '../src/ImageText.php';

$bg = './img/benji.jpg';
$overlay = './img/paw.png';
$image = new PHPImage();
$image->setDimensionsFromImage($bg);

$image->draw($bg);
$image->draw($overlay, '50%', '75%');

$image->rectangle(40, 40, 120, 80, array(0, 0, 0), 0.5);
$image->setFont('./font/arial.ttf');
$image->setTextColor(array(255, 255, 255));

$image->text([
    'text' => 'This is a big sentence with width 200px',
    'fontSize' => 60,
    'x' => 300,
    'y' => 0,
    'width' => 200,
    'height' => 50,
    'alignHorizontal' => 'center',
    'alignVertical' => 'center',
    'debug' => true,
]);

$image->text([
    'text' => 'This is a big sentence',
    'fontSize' => 60,
    'x' => 300,
    'y' => 200,
    'width' => 200,
    'height' => 50,
    'alignHorizontal' => 'center',
    'alignVertical' => 'center',
    'debug' => true,
]);

$image->text([
    'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
    'width' => 100, 'font_size' => 8, 'x' => 50, 'y' => 70,
]);

$image->rectangle(40, 140, 170, 160, array(0, 0, 0), 0.5);

$image->text([
    'text' => 'Auto wrap and scale font size to multiline text box width and height bounds. Vestibulum venenatis risus scelerisque enim faucibus, ac pretium massa condimentum. Curabitur faucibus mi at convallis viverra. Integer nec finibus ligula, id hendrerit felis.',
    'width' => 150,
    'height' => 140,
    'fontSize' => 16,
    'x' => 50,
    'y' => 150,
]);

$image->show();
