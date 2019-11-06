<?php

require_once('../src/ImageText.php');

$bg = './img/benji.jpg';

$image = new PHPImage();
$image->setDimensionsFromImage($bg);
$image->draw($bg);

$image->setLineHeight(1.25);

$text = "Проекция на подвижные оси абсолютно преобразует волчок. Гироскопический стабилизатоор даёт более простую систему дифференциальных уравнений, если исключить гирокомпас. Проекция абсолютной угловой скорости на оси системы координат xyz связывает параметр Родинга-Гамильтона.";

$image->text([
    'text' => $text,
    'width' => 400,
    'height' => 400,
    'fontSize' => 15,
    'fontColor' => array(255, 255, 255),
    'fontFile' => './font/arial.ttf',
    'alignHorizontal' => 'left',
    'alignVertical' => 'top',
    'x' => 50,
    'y' => 120
]);

$size = $image->getBlockSize();

$image->text([
    'text' => $text,
    'width' => 400,
    'height' => 400,
    'fontSize' => 15,
    'fontColor' => array(255, 255, 255),
    'fontFile' => './font/arial.ttf',
    'alignHorizontal' => 'left',
    'alignVertical' => 'top',
    'x' => 50,
    'y' => 140 + $size['height']
]);

$image->show();