<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');  
$postdata = print_r($_POST,True);
file_put_contents("/home/pi/www/upload/something.txt",$postdata);
    #header('Content-Type: image/jpeg');
    
    
    #echo $_FILES['userfile']['name'][0];
    
    
    #echo $_FILES['userfile']['tmp_name'][0];
    move_uploaded_file($_FILES['userfile']['tmp_name'][0], "/home/pi/www/upload/evil.gif");
    
    move_uploaded_file($_FILES['userfile']['tmp_name'][1], "/home/pi/www/upload/target1.jpg");
    move_uploaded_file($_FILES['userfile']['tmp_name'][2], "/home/pi/www/upload/target2.jpg");
    move_uploaded_file($_FILES['userfile']['tmp_name'][3], "/home/pi/www/upload/target3.jpg");
    move_uploaded_file($_FILES['userfile']['tmp_name'][4], "/home/pi/www/upload/target4.jpg");
    move_uploaded_file($_FILES['userfile']['tmp_name'][5], "/home/pi/www/upload/target5.jpg");
    move_uploaded_file($_FILES['userfile']['tmp_name'][6], "/home/pi/www/upload/target6.jpg");
    move_uploaded_file($_FILES['userfile']['tmp_name'][7], "/home/pi/www/upload/target7.jpg");
    move_uploaded_file($_FILES['userfile']['tmp_name'][8], "/home/pi/www/upload/target8.jpg");
    move_uploaded_file($_FILES['userfile']['tmp_name'][9], "/home/pi/www/upload/target9.jpg");
    move_uploaded_file($_FILES['userfile']['tmp_name'][10], "/home/pi/www/upload/target10.jpg");
    move_uploaded_file($_FILES['userfile']['tmp_name'][11], "/home/pi/www/upload/target11.jpg");
    move_uploaded_file($_FILES['userfile']['tmp_name'][12], "/home/pi/www/upload/target12.jpg");
    move_uploaded_file($_FILES['userfile']['tmp_name'][13], "/home/pi/www/upload/target13.jpg");
    
    
    
    $im = imagecreatefromgif("/home/pi/www/upload/evil.gif");
    
    $count = 0;
    $row = 0;
    
    while ( $row < 100 ) {
        while ( $count < 100 ) {
            $rgb = imagecolorat($im, $count, $row);
            $r[$count] = ($rgb >> 16) & 0xFF;
            $g[$count] = ($rgb >> 8) & 0xFF;
            $b[$count] = $rgb & 0xFF;

            $pix[$count] = $r[$count] + $b[$count] + $g[$count];
            $data[$row] .= $r[$count] + $b[$count] + $g[$count];
            
            $count++;
        }
        $count = 0;
        $row++;
    }
    
    
    $msg[1] = "0001".$data[0].$data[1].$data[2].$data[3].$data[4].$data[5].$data[6].$data[7];
    $msg[2] = "0010".$data[8].$data[9].$data[10].$data[11].$data[12].$data[13].$data[14].$data[15];
    $msg[3] = "0011".$data[16].$data[17].$data[18].$data[19].$data[20].$data[13].$data[14].$data[15];
    $msg[4] = "0100".$data[24].$data[25].$data[26].$data[27].$data[28].$data[29].$data[30].$data[31];
    $msg[5] = "0101".$data[32].$data[33].$data[34].$data[35].$data[36].$data[37].$data[38].$data[39];
    $msg[6] = "0110".$data[40].$data[41].$data[42].$data[43].$data[44].$data[45].$data[46].$data[47];
    $msg[7] = "0111".$data[48].$data[49].$data[50].$data[51].$data[52].$data[53].$data[54].$data[55];
    $msg[8] = "1000".$data[56].$data[57].$data[58].$data[59].$data[60].$data[61].$data[62].$data[63];
    $msg[9] = "1001".$data[64].$data[65].$data[66].$data[67].$data[68].$data[69].$data[70].$data[71];
    $msg[10] = "1010".$data[72].$data[73].$data[74].$data[75].$data[76].$data[77].$data[78].$data[79];
    $msg[11] = "1011".$data[80].$data[81].$data[82].$data[83].$data[84].$data[85].$data[86].$data[87];
    $msg[12] = "1100".$data[88].$data[89].$data[90].$data[91].$data[92].$data[93].$data[94].$data[95];
    $msg[13] = "1101".$data[96].$data[97].$data[98].$data[99].$data[100];
       
    $imgcount = 1;
    
    while ( $imgcount <= 13 ) {
        
        $im = imagecreatefromjpeg("/home/pi/www/upload/target".$imgcount.".jpg");
    
        $white = imagecolorallocate($im, 255, 255, 255);
        $black = imagecolorallocate($im, 0, 0, 0);

        $direction = 1;#1=top, 2=down, 3=bottom, 4=up
        $curpos = 0;
        
        

        $binary_char = "";
        $binary_char = str_split($msg[$imgcount]);
        #print_r($binary_char);

        foreach ( $binary_char as $key => $data) {
            if ( $curpos == 2048 ) {
                $direction++;
                $curpos = 8;
            }



            if ( $direction == 1 ) {
                if ( $data == "1" ) {
                    imagefilledrectangle($im, $curpos, 0, ($curpos+8), 1, $white);
                } else {
                    imagefilledrectangle($im, $curpos, 0, ($curpos+8), 1, $black);
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
                } else {
                    imagefilledrectangle($im, 2047, $curpos, 2048, ($curpos+8), $black);
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
                } else {
                    imagefilledrectangle($im, 0, $curpos, 1, ($curpos+8), $black);
                }
                $curpos = $curpos + 8;
                #print $curpos."<br>";
            }
            if ( $direction == 4 ) {
                if ( $data == "1" ) {
                    imagefilledrectangle($im, $curpos, 1279, ($curpos+8), 1280, $white);
                } else {
                    imagefilledrectangle($im, $curpos, 1279, ($curpos+8), 1280, $black);
                }
                $curpos = $curpos + 8;
                #print $curpos."<br>";
            }
        }
        

        imagejpeg($im,"/home/pi/www/upload/out".$imgcount.".jpg");
        
        
        echo "http://".$_SERVER['SERVER_ADDR']."/upload/out".$imgcount.".jpg\n";
        
        
        
        
        $imgcount++;
    }
    
    
    
    

   












/*

        
   

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

    imagejpeg($im,"output.jpg");
    #imagedestroy($im);
}
*/
?>







