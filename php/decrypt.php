<?php
if ( $_POST['message'] == "" ) { 
    echo "<form method='POST'>
    <input type='text' name='message'>
    <input type='submit' name='submit'>
    </form>";
} else {


    
    $im = imagecreatefromjpeg($_POST['message']);


    $char["00000001"] = "a";
    $char["00000010"] = "b";
    $char["00000011"] = "c";
    $char["00000100"] = "d";
    $char["00000101"] = "e";
    $char["00000110"] = "f";
    $char["00000111"] = "g";
    $char["00001000"] = "h";
    $char["00001001"] = "i";
    $char["00001010"] = "j";
    $char["00001011"] = "k";
    $char["00001100"] = "l";
    $char["00001101"] = "m";
    $char["00001110"] = "n";
    $char["00001111"] = "o";
    $char["00010000"] = "p";
    $char["00010001"] = "q";
    $char["00010010"] = "r";
    $char["00010011"] = "s";
    $char["00010100"] = "t";
    $char["00010101"] = "u";
    $char["00010110"] = "v";
    $char["00010111"] = "w";
    $char["00011000"] = "x";
    $char["00011001"] = "y";
    $char["00011010"] = "z";
    
    $char["00100001"] = "A";
    $char["00100010"] = "B";
    $char["00100011"] = "C";
    $char["00100100"] = "D";
    $char["00100101"] = "E";
    $char["00100110"] = "F";
    $char["00100111"] = "G";
    $char["00101000"] = "H";
    $char["00101001"] = "I";
    $char["00101010"] = "J";
    $char["00101011"] = "K";
    $char["00101100"] = "L";
    $char["00101101"] = "M";
    $char["00101110"] = "N";
    $char["00101111"] = "O";
    $char["00110000"] = "P";
    $char["00110001"] = "Q";
    $char["00110010"] = "R";
    $char["00110011"] = "S";
    $char["00110100"] = "T";
    $char["00110101"] = "U";
    $char["00110110"] = "V";
    $char["00110111"] = "W";
    $char["00111000"] = "X";
    $char["00111001"] = "Y";
    $char["00111010"] = "Z";
    
    $char["11111111"] = " ";
    $char["00000000"] = "";



    $curpos = 0;
    $count = 3;

    while ($count < 2048) {
        $rgb = imagecolorat($im, $count, 0);
        $r[$count] = ($rgb >> 16) & 0xFF;
        $g[$count] = ($rgb >> 8) & 0xFF;
        $b[$count] = $rgb & 0xFF;

        $pix[$count] = $r[$count] + $b[$count] + $g[$count];
        $count = $count + 8;
    }

    #print_r($data);

    $output = "";

    foreach ( $pix as $key => $data ) {
        if ( $data > 300 ) {
            $output .= "1";
        } else {
            $output .= "0";
        }
    }

    $count = 0;

    $out = explode(".",chunk_split($output,8,"."));

    foreach ($out as $test) {

        echo $char[$test];
    }

    #print $output;
}
?>

