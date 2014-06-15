Ninepatch-for-php
=================

A simple script who can convert ninepatch (9-patch) images to usable images



Usage:

    $file = "The filename to the ninepatch image";

    // final size of the image
    $width = 300;
    $height =  800;

    $back = 'trans';// the fillup color

    ninepatchResize($file, $width,$height,$back='trans')
