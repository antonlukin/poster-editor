<?php

require_once '../src/ImageText.php';

$bg = './img/benji.jpg';
$image = new PHPImage();
$image->setDimensionsFromImage($bg);
$image->draw($bg);
$image->setFont('./font/arial.ttf');
$image->setTextColor(array(255, 255, 255));

$image->text([
    'text' => 'Ваша фобия на сегодня астрофобия',
    'width' => 200,
    'fontSize' => 20,
    'lineHeight' => 2,
    'height' => 200,
    'x' => 50,
    'y' => 50,
    'debug' => true,
]);

$image->show();
