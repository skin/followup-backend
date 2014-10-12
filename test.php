<?php 

include "phpqrcode/qrlib.php";
header("Content-Type: image/png");
QRcode::png('PHP QR Code :)');

?>