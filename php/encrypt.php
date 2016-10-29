<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');  
if ( $_POST['message'] == "" ) { 
    echo "<form method='POST'>
    <input type='text' name='message'>
    <input type='submit' name='submit'>
    </form>";
} else {
    header('Content-Type: image/jpeg');
    $message = $_GET['message'];

    $message_split = str_split($message);



    $char["a"] = "00000001";
    $char["b"] = "00000010";
    $char["c"] = "00000011";
    $char["d"] = "00000100";
    $char["e"] = "00000101";
    $char["f"] = "00000110";
    $char["g"] = "00000111";
    $char["h"] = "00001000";
    $char["i"] = "00001001";
    $char["j"] = "00001010";
    $char["k"] = "00001011";
    $char["l"] = "00001100";
    $char["m"] = "00001101";
    $char["n"] = "00001110";
    $char["o"] = "00001111";
    $char["p"] = "00010000";
    $char["q"] = "00010001";
    $char["r"] = "00010010";
    $char["s"] = "00010011";
    $char["t"] = "00010100";
    $char["u"] = "00010101";
    $char["v"] = "00010110";
    $char["w"] = "00010111";
    $char["x"] = "00011000";
    $char["y"] = "00011001";
    $char["z"] = "00011010";
    
    $char["A"] = "00100001";
    $char["B"] = "00100010";
    $char["C"] = "00100011";
    $char["D"] = "00100100";
    $char["E"] = "00100101";
    $char["F"] = "00100110";
    $char["G"] = "00100111";
    $char["H"] = "00101000";
    $char["I"] = "00101001";
    $char["J"] = "00101010";
    $char["K"] = "00101011";
    $char["L"] = "00101100";
    $char["M"] = "00101101";
    $char["N"] = "00101110";
    $char["O"] = "00101111";
    $char["P"] = "00110000";
    $char["Q"] = "00110001";
    $char["R"] = "00110010";
    $char["S"] = "00110011";
    $char["T"] = "00110100";
    $char["U"] = "00110101";
    $char["V"] = "00110110";
    $char["W"] = "00110111";
    $char["X"] = "00111000";
    $char["Y"] = "00111001";
    $char["Z"] = "00111010";
    
    $char[""] = "00000000";
    $char[" "] = "11111111";
    $char["!"] = "10111111";
    $char["?"] = "01111111";
    $char[","] = "01111110";
    $char["."] = "01111101";
    


    $binary = "";
    foreach ($message_split as $key => $data ) {
        
        $binary .= $char[$data]."-";
        
    }

    $im = imagecreatefromjpeg("butterfly.jpg");
    
    #$im = imagecreatetruecolor(2048, 1280);


    $white = imagecolorallocate($im, 255, 255, 255);
    $grey = imagecolorallocate($im, 128, 128, 128);
    $black = imagecolorallocate($im, 0, 0, 0);


    $output_text = explode("-",$binary);

    $direction = 1;#1=top, 2=down, 3=bottom, 4=up
    $curpos = 0;
    
    foreach ( $output_text as $key => $data ) {

        $binary_char = "";
        $binary_char = str_split($data);
        #print_r($binary_char);

        foreach ( $binary_char as $key => $data) {
            if ( $curpos == 2048 ) {
                $direction++;
                $curpos = 8;
            }
            
            
            
            if ( $direction == 1 ) {
                if ( $data == "1" ) {
                    imagefilledrectangle($im, $curpos, 0, ($curpos+8), 1, $white);
                }
                $curpos = $curpos + 8;
            }
            if ( $direction == 2 ) {
                if ( $curpos > 1280 ) {
                    $direction++;
                    $curpos = 8;
                }
                if ( $data == "1" ) {
                    imagefilledrectangle($im, 2047, $curpos, 2048, ($curpos+8), $white);
                }
                $curpos = $curpos + 8;
                #print $curpos."<br>";
            }
            if ( $direction == 3 ) {
                if ( $curpos > 1280 ) {
                    $direction++;
                    $curpos = 8;
                }
                if ( $data == "1" ) {
                    imagefilledrectangle($im, 0, $curpos, 1, ($curpos+8), $white);
                }
                $curpos = $curpos + 8;
                #print $curpos."<br>";
            }
            if ( $direction == 4 ) {
                if ( $data == "1" ) {
                    imagefilledrectangle($im, $curpos, 1279, ($curpos+8), 1280, $white);
                }
                $curpos = $curpos + 8;
                #print $curpos."<br>";
            }
        }
    }

    imagejpeg($im);
    imagedestroy($im);
}

?>







