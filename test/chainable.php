<?php

require_once '../src/ImageText.php';

(new PHPImage('./img/benji.jpg'))->resize(1000, 1000, true)->show();