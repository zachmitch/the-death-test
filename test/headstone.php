<?php

// Set the content-type
header('Content-Type: image/png');

// Create the image
$im = imagecreatetruecolor(400, 30);

// Create some colors
$white = imagecolorallocate($im, 255, 255, 255);
$grey = imagecolorallocate($im, 128, 128, 128);
$black = imagecolorallocate($im, 0, 0, 0);
imagefilledrectangle($im, 0, 0, 399, 29, $white);

// The text to draw
$text = 'Testing...';
// Replace path by your own font path
$font = 'arial.ttf';

// Add some shadow to the text
imagettftext($im, 20, 0, 11, 21, $grey, $font, $text);

// Add the text
imagettftext($im, 20, 0, 10, 20, $black, $font, $text);

// Using imagepng() results in clearer text compared with imagejpeg()
imagepng($im);
imagedestroy($im);


/*

    //Get access to user info
    include "result.php";

    //Create new png image from base image
    $my_img = imagecreatefrompng("img/headstone_transparent.png");

    //Error handling
    if(!$my_img) {
      echo "<script type=\"text/javascript\">
            window.location.replace('http://www.yahoo.com');
            </script>";
      exit();
    } else {
      echo "<script type=\"text/javascript\">
            window.location.replace('http://www.google.com');
            </script>";
      exit();
      $text_color = imagecolorallocate($my_img, 0,0,0);
    }

    //Write text on gravestone
    if(!$text_color) {
      die("Couldn't attach color to image text.");
    } else {

        imagestring($my_img, 5, 500, 500, "1988", $text_color);
        imagestring($my_img, 5, 600, 500, "-", $text_color);
        imagestring($my_img, 5, 700, 500, "2088", $text_color);

    }


    imagepng( $my_img );

    imagecolordeallocate( $text_color );
    imagedestroy( $my_img );

*/

 ?>
