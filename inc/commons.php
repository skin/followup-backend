<?php 

function getDateFromTimestamp($timestamp){
	return date("Y-m-d H:i:s",$timestamp);
}

function getEventURL($eventID){
	return FRONTEND_EVENT_URL.$eventID;
}

function generateEventQrCode($eventID,$eventURL){
	$qrCodeTmpURL = QRCODE_FOLDER.$eventID."_temp.png";
	$qrCodeURL = QRCODE_FOLDER.$eventID.".png";
	QRcode::png($eventURL, $qrCodeTmpURL, QR_ECLEVEL_L, 50);
	// Get QR Code image from Google Chart API
	// http://code.google.com/apis/chart/infographics/docs/qr_codes.html
	$QR = imagecreatefrompng($qrCodeTmpURL);
	$logo = imagecreatefrompng(DOCUMENT_ROOT."logo_qr.png");
	$QR_width = imagesx($QR);
	$QR_height = imagesy($QR);

	$logo_width = imagesx($logo);
	$logo_height = imagesy($logo);

	// Scale logo to fit in the QR Code
	$logo_qr_width = $QR_width/5;
	$scale = $logo_width/$logo_qr_width;
	$logo_qr_height = $logo_height/$scale;

	imagecopyresampled($QR, $logo, $QR_width/2.5, $QR_height/2.3, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
	$index = imagecolorclosest ( $QR,  0,0,0 ); // get White COlor
	imagecolorset($QR,$index,42,130,242); // SET NEW COLOR
	imagepng($QR,$qrCodeURL);
	return $qrCodeURL;
}

function replaceBackslashes($text){
	return preg_replace('/\\\"/',"\"", $text);
}

?>