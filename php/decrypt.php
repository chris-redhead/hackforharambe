<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST'); 
    header('Content-Type: image/jpeg');
    $imgcount = 1;
    
    $file_list = explode(";",$_GET['images']);
    

    $binnum["0001"] = 1;
    $binnum["0010"] = 2;
    $binnum["0011"] = 3;
    $binnum["0100"] = 4;
    $binnum["0101"] = 5;
    $binnum["0110"] = 6;
    $binnum["0111"] = 7;
    $binnum["1000"] = 8;
    $binnum["1001"] = 9;
    $binnum["1010"] = 10;
    $binnum["1011"] = 11;
    $binnum["1100"] = 12;
    $binnum["1101"] = 13;
    
    
    foreach( $file_list as $key => $data ) {
        
        $im = imagecreatefromjpeg($data);
        
        $count = 4;
        while ($count < 2048) {
            $rgb = imagecolorat($im, $count, 0);
            $r[$count] = ($rgb >> 16) & 0xFF;
            $g[$count] = ($rgb >> 8) & 0xFF;
            $b[$count] = $rgb & 0xFF;

            $pix[$count] = $r[$count] + $b[$count] + $g[$count];
            $count = $count + 8;
        }
        $totalcount = $count;
        $count = 11;
        while ($count < 1280) {
            $rgb = imagecolorat($im, 2047, $count);
            $r[$count] = ($rgb >> 16) & 0xFF;
            $g[$count] = ($rgb >> 8) & 0xFF;
            $b[$count] = $rgb & 0xFF;

            $pix[$totalcount+$count] = $r[$count] + $b[$count] + $g[$count];
            $count = $count + 8;
        }
        $totalcount = $totalcount+$count;
        $count = 11;
        while ($count < 1280) {
            $rgb = imagecolorat($im, 1, $count);
            $r[$count] = ($rgb >> 16) & 0xFF;
            $g[$count] = ($rgb >> 8) & 0xFF;
            $b[$count] = $rgb & 0xFF;

            $pix[$totalcount+$count] = $r[$count] + $b[$count] + $g[$count];
            $count = $count + 8;
        }
        $totalcount = $totalcount+$count;
        $count = 11;
        while ($count < 2048) {
            $rgb = imagecolorat($im, $count, 1279);
            $r[$count] = ($rgb >> 16) & 0xFF;
            $g[$count] = ($rgb >> 8) & 0xFF;
            $b[$count] = $rgb & 0xFF;

            $pix[$totalcount+$count] = $r[$count] + $b[$count] + $g[$count];

            $count = $count + 8;
        }

        $output = "";

        foreach ( $pix as $key => $data ) {
            if ( $data > 700 ) {
                $output .= "1";
            } else {
                $output .= "0";
            }
        }

        $id = substr($output,0,4);
        $output = substr($output,4);
        
        
        $outdata = ( str_split($output,100));
        
        $outputlist[$binnum[$id]] .= $outdata[0].$outdata[1].$outdata[2].$outdata[3].$outdata[4].$outdata[5].$outdata[6].$outdata[7];
        

        #$out = explode(".",chunk_split($output,8,"."));

        $imgcount++;
    }
    #echo $outputdata;
    
    $imgcount = 1;
    while ( $imgcount <= 13 ) {
        if ($outputlist[$imgcount] == null) {
            $outputdata .= str_repeat("0",800);
            
        } else {
            $outputdata .= $outputlist[$imgcount];
            
        }
        $imgcount++;
    }
    
    $im = imagecreatetruecolor(100, 100);

    $white = imagecolorallocate($im, 255, 255, 255);
    $grey = imagecolorallocate($im, 128, 128, 128);
    $black = imagecolorallocate($im, 0, 0, 0);
    
    $data = str_split($outputdata);
    
    $count = 0;
    $row = 0;
    foreach ( $data as $pixel) {
        if ( $count == 100 ) {
            $row++;
            $count = 0;
        }
        #echo $pixel;
        if ( $pixel == 1 ) {
            imagefilledrectangle($im, $count, $row, $count+1, $row+1, $black);
        }
        if ( $pixel == 0 ) {
            imagefilledrectangle($im, $count, $row, $count+1, $row+1, $white);
        }
        $count++;
    }
    
    imagejpeg($im);
    imagedestroy($im);
    

    
?>

