<?php

require_once '../src/ImageText.php';

$image = new PHPImage('./img/benji.jpg');
$image->setFont('./font/arial.ttf');

$one = clone $image;
$two = clone $image;

$one->resize(200, 100, true)->text(
    ['text' => 'one', 'fontColor' => array(0, 255, 0)]
)->save('./examples/one.jpg');

$two->resize(200, 100, true)->text(
    ['text' => 'two', 'fontColor' => array(255, 0, 0)]
)->save('./examples/two.jpg');

$three = clone $two;

$three->resize(80, 160, true)->text(
    ['text' => 'three', 'fontColor' => array(0, 0, 255)]
)->save('./examples/three.jpg');

$rotated = imagerotate($image->getResource(), 90, 0);
$image->setResource($rotated);

$image->show();
