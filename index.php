<?php
//header("Content-Type: image/png");

$path = "/home/mrjustreborn/Dev/progress";
$cur_time = time()+3600;


$imagecontainer = imagecreatetruecolor(1920, 1080);
imagesavealpha ($imagecontainer, true);

$bg = imagecolorallocatealpha($imagecontainer, 130,130,130,0);
$blue = imagecolorallocatealpha($imagecontainer, 0,0,255,0);
$black = imagecolorallocatealpha($imagecontainer, 0,0,0,0);
$black_alpha1 = imagecolorallocatealpha($imagecontainer, 0,0,0,30);
$black_alpha2 = imagecolorallocatealpha($imagecontainer, 0,0,0,60);
$red = imagecolorallocatealpha($imagecontainer, 255,0,0,0);
$white = imagecolorallocatealpha($imagecontainer, 255,255,255,0);
$bg_gimp = imagecolorallocate($imagecontainer, 222,236,243);

imagefill($imagecontainer,0,0,$bg);



$bar = imagecreatefrompng($path.'/images/progress/bar.png');
$bar_klein = imagecreatefrompng($path.'/images/progress/bar_klein_alpha.png');
$bar_klein_green = imagecreatefrompng($path.'/images/progress/bar_klein_green_alpha.png');
$bar_klein_lila = imagecreatefrompng($path.'/images/progress/bar_klein_lila_alpha.png');
$wookie = imagecreatefrompng($path.'/images/wookie.png');

$wookie_back = imagecreatefromjpeg($path.'/images/wookie.jpg');
//imagecopyresampled($imagecontainer, $wookie_back, 0, 0, 0, 0, 1920, 1080, 1920, 1080);

$rbtv = json_decode(CallAPI('GET','www.rocketbeans.tv/?next5Shows=true'));

//var_dump($rbtv);

$length = strtotime($rbtv[0]->time)+$rbtv[0]->length-$cur_time;
if ($length <= 0) {
	$length = 0;
}
$timeleft = date("H:i",$length);

$live = imagecreatefrompng($path.'/images/progress/live.png');
$PREMIERE = imagecreatefrompng($path.'/images/progress/PREMIERE.png');

$percent = ($cur_time - strtotime($rbtv[0]->time)) / $rbtv[0]->length;

if ($percent >= 1) {
	$percent = 1;
}

imagecopyresampled($imagecontainer, $bar_klein, 1300, 1000, 0, 0, 500*$percent, 21, 1, 21);
imagecopyresampled($imagecontainer, $wookie, 0, 0, 0, 0, 1920, 1080, 1920, 1080);


//current
if ($rbtv[0]->isLive == 1) {
	imagecopyresampled($imagecontainer, $live, 1300, 970, 0, 0, 383/7, 223/7, 383, 223);
	ImageTTFText($imagecontainer, 15, 0, 1355, 990, $black, "/usr/share/fonts/TTF/Norasi-BoldOblique.ttf",
		$rbtv[0]->title .' - '. $rbtv[0]->topic);
} elseif ($rbtv[0]->isNew == 1) {
	imagecopyresampled($imagecontainer, $PREMIERE, 1300, 973, 0, 0, 692/7, 173/7, 692, 173);
	ImageTTFText($imagecontainer, 15, 0, 1400, 990, $black, "/usr/share/fonts/TTF/Norasi-BoldOblique.ttf",
		$rbtv[0]->title .' - '. $rbtv[0]->topic);
} else {
	ImageTTFText($imagecontainer, 15, 0, 1300, 990, $black, "/usr/share/fonts/TTF/Norasi-BoldOblique.ttf",
		$rbtv[0]->title .' - '. $rbtv[0]->topic);
}
ImageTTFText($imagecontainer, 15, 0, 1800, 1016, $black, "/usr/share/fonts/TTF/Norasi-BoldOblique.ttf",
		$timeleft);

//next
for ($i = 1; $i < 5; $i++) {
	if ($rbtv[$i]->isLive == 1) {
		imagecopyresampled($imagecontainer, $live, 1300, 1010 + $i*12, 0, 0, 383/14, 223/14, 383, 223);
		ImageTTFText($imagecontainer, 10, 0, 1330, 1022 + $i*12, $black, "/usr/share/fonts/TTF/Norasi-Italic.ttf",
		date("H:i",strtotime($rbtv[$i]->time)) . ": " . $rbtv[$i]->title .' - '. $rbtv[$i]->topic);
	} elseif ($rbtv[$i]->isNew == 1) {
		imagecopyresampled($imagecontainer, $PREMIERE, 1300, 1010 + $i*12, 0, 0, 692/14, 173/14, 692, 173);
		ImageTTFText($imagecontainer, 10, 0, 1350, 1022 + $i*12, $black, "/usr/share/fonts/TTF/Norasi-Italic.ttf",
		date("H:i",strtotime($rbtv[$i]->time)) . ": " . $rbtv[$i]->title .' - '. $rbtv[$i]->topic);
	} else {
		ImageTTFText($imagecontainer, 10, 0, 1300, 1022 + $i*12, $black, "/usr/share/fonts/TTF/Norasi-Italic.ttf",
		date("H:i",strtotime($rbtv[$i]->time)) . ": " . $rbtv[$i]->title .' - '. $rbtv[$i]->topic);
	}
}

//ImageTTFText($imagecontainer, 10, 0, 1300, 1055, $black_alpha1, "/usr/share/fonts/TTF/Norasi-Italic.ttf",
//	date("H:i",strtotime($rbtv[2]->time)) . ": " . $rbtv[2]->title .' - '. $rbtv[2]->topic);
//ImageTTFText($imagecontainer, 10, 0, 1300, 1070, $black_alpha2, "/usr/share/fonts/TTF/Norasi-Italic.ttf",
//	date("H:i",strtotime($rbtv[3]->time)) . ": " . $rbtv[3]->title .' - '. $rbtv[3]->topic);



ImageTTFText($imagecontainer, 60, 0, 1650, 100, $black, "/usr/share/fonts/TTF/Norasi-BoldOblique.ttf", date("H:i",$cur_time));

$data = shell_exec('uptime');
$uptime = explode(' up ', $data);
$uptime = explode(',', $uptime[1]);
$uptime = $uptime[0].$uptime[1];

ImageTTFText($imagecontainer, 20, 0, 1655, 130, $black, "/usr/share/fonts/TTF/Norasi-BoldOblique.ttf", $uptime);


imagepng($imagecontainer, $path.'/bg.png');
imagedestroy($imagecontainer);


function CallAPI($method, $url, $data = false)
{
    $curl = curl_init();

    switch ($method)
    {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // Optional Authentication:
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, "username:password");

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
}

?>
