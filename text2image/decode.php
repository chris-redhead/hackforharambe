<?php
echo "<a href='encode.php'>Encode</a> - <a href='decode.php'>Decode</a><br><br>";
if ( $_POST['submit'] == "" ) {
    echo "<form action='' method='post' enctype='multipart/form-data'>
    <p><label>Carrier file </label><input type='file' name='file'></p>
    <p><input type='submit' name='submit'></p>
    </form>";
} else {
    move_uploaded_file($_FILES['file']['tmp_name'], "/home/pi/www/upload/upload.png");
    $process = exec("/home/pi/www/python/./text2image.py -d /home/pi/www/upload/upload.png");
    echo "Processing...<br><br>";
    flush();
    sleep(1);
    
    echo "The secret message was: $process";
    
}




?>
