<?php
$uploads_dir = '/home/pi/www/upload';
echo "<a href='encodeImage.php'>Encode</a> - <a href='decodeImage.php'>Decode</a><br><br>";
if ( $_POST['submit'] == "" ) {
    echo "<form action='' method='post' enctype='multipart/form-data'>
    <p><label>Image to encode </label><input type='file' name='evil'></p>
    <p><label>Carrier file </label><input type='file' name='targ'></p>
    <p><input type='submit' name='submit'></p>
    </form>";
} else {
    $tmp_name = $_FILES["evil"]["tmp_name"];
    move_uploaded_file($tmp_name, "$uploads_dir/evil.png");

    $tmp_name = $_FILES["targ"]["tmp_name"];
    move_uploaded_file($tmp_name, "$uploads_dir/targ.png");

    $process = exec("/home/pi/www/python/./image2image.py -e /home/pi/www/upload/evil.png -t /home/pi/www/upload/targ.png -o /home/pi/www/upload/out.png");
    echo "Processing...<br><br>";
    flush();
    sleep(1);
    echo $process + "<br>";
    echo "<img src='../upload/out.png'>";
}
?>
