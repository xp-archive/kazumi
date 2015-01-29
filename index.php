<?php
if(@$_FILES['pic']['size']) {

$c = $_FILES['pic']['tmp_name'];
$url_bg = (!get_magic_quotes_gpc())? addslashes($c): $c;

$url_fg = "kazumi.png";

function imagecreatefromfile($img_path) {
	$wimage_data = GetImageSize($img_path); 
	switch($wimage_data[2]) { 
	case 1: return @ImageCreateFromGIF ($img_path);
	case 2: return @ImageCreateFromJPEG($img_path);
	case 3: return @ImageCreateFromPNG ($img_path);
	case 6: return @ImageCreateFromWBMP($img_path);
	}
}

$img_bg = imagecreatefromfile($url_bg);
$img_fg = imagecreatefromfile($url_fg);

function imagecreatetransparent($width, $height) {
	$img = imagecreatetruecolor($width, $height); //创建一的真彩色图像
	$transparent = imagecolorallocatealpha($img, 255, 255, 255, 127); //透明背景
	imagefill($img, 0, 0, $transparent);
	return $img;
}

$img = imagecreatetransparent(imagesx($img_bg), imagesy($img_bg));

imagecopy($img, $img_bg, 0, 0, 0, 0, imagesx($img_bg), imagesy($img_bg));

$percentage = 0.8; // 高度占比
$scale = (imagesy($img_bg) * $percentage) / imagesy($img_fg); // 高度改变后的等比缩放率

$src_sx = imagesx($img_fg) * $scale;
$src_sy = imagesy($img_bg) * $percentage;

$img_fg2 = imagecreatetransparent($src_sx, $src_sy);
imagecopyresampled($img_fg2, $img_fg, 0, 0, 0, 0, $src_sx, $src_sy, imagesx($img_fg), imagesy($img_fg));

$dst_px = imagesx($img_bg) - $src_sx + 1;
$dst_py = imagesy($img_bg) - $src_sy + 1;

imagecopy($img, $img_fg2, $dst_px, $dst_py, 0, 0, $src_sx, $src_sy);

$dpi = 96;

$text = $_POST['txt'];
$font = './STZHONGS.TTF';
$fontsize = (imagesy($img) * 0.08) * 72 / $dpi;
$fontpx = $fontsize * $dpi / 72;
// if ($fontsize > 36) $fontsize = 36;
$color_fg = imagecolorallocate($img, 255, 255, 255);
$color_bg = imagecolorallocate($img, 0, 0, 0);
$txt_px = (imagesx($img) - ($fontpx * mb_strlen($text, 'utf8'))) / 2;
$txt_py = imagesy($img) - $fontpx - 10;

for ($i = 2; $i > 0; $i--) { 
	imagettftext($img, $fontsize, 0, $txt_px-$i, $txt_py   , $color_bg, $font, $text);
	imagettftext($img, $fontsize, 0, $txt_px   , $txt_py-$i, $color_bg, $font, $text);
	imagettftext($img, $fontsize, 0, $txt_px-$i, $txt_py-$i, $color_bg, $font, $text);
	imagettftext($img, $fontsize, 0, $txt_px+$i, $txt_py   , $color_bg, $font, $text);
	imagettftext($img, $fontsize, 0, $txt_px   , $txt_py+$i, $color_bg, $font, $text);
	imagettftext($img, $fontsize, 0, $txt_px+$i, $txt_py+$i, $color_bg, $font, $text);
}
imagettftext($img, $fontsize, 0, $txt_px, $txt_py, $color_fg, $font, $text);

header("content-type:image/png");
imagepng($img);

imagedestroy($img);
imagedestroy($img_fg);
imagedestroy($img_fg2);
imagedestroy($img_bg);

return ;
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>和美生成画</title>
</head>
<body>
<form role="form" method="POST" enctype="multipart/form-data">
     <input type="file" name="pic" >
     <input type="text" name="txt" value="我说，那边的人都是处男吗">
	<button type="submit">提交</button>
</form>
</body>
</html>