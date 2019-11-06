<?php

require_once('../src/ImageText.php');

$image = new PHPImage('./img/benji.jpg');
$image->resize(800, 800, true)->show();