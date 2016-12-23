<?php
/**
* useful functions used on the site
*
*/

/**
 * @param $string
 * @return string
 * safe html output
 */
function safe($string) {
    return stripslashes(htmlspecialchars($string, ENT_NOQUOTES, "UTF-8"));
}

/**
 * @param int $length
 * @return string
 * generate random string - default 10 chars
 */
function random_string($length = 10) {
	$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$randomString = '';
	
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $chars[rand(0, strlen($chars) - 1)];
	}
	
	return $randomString;
}

/**
 * @param $txt
 * @param int $len
 * @return string
 * limited output - default 100 chars
 */
function text_shorthand($txt, $len = 100){
    return (strlen($txt) > $len ? substr($txt, 0, $len - 1).'...' : $txt);
}

/**
 * @param $string
 * @return string
 * safe name, no croatian letters
 */
function safe_name($string){
    $trans = array("š" => "s", "ć" => "c", "č" => "c", "đ" => "d", "ž" => "z", " " => "_");
    return strtr(mb_strtolower($string, "UTF-8"), $trans);
}


/**
 * @param $path
 * image resizer
 */
function imgResize($path) {
    $x = getimagesize($path);
    $width  = $x['0'];
    $height = $x['1'];

    if($width > 960)
        $rs_width = 960;
    if($height > 690)
        $rs_height = 690;

    switch ($x['mime']) {
        case "image/gif":
            $img = imagecreatefromgif($path);
            break;
        case "image/jpeg":
            $img = imagecreatefromjpeg($path);
            break;
        case "image/jpg":
            $img = imagecreatefromjpeg($path);
            break;
        case "image/png":
            $img = imagecreatefrompng($path);
            break;
    }

    $img_base = imagecreatetruecolor($rs_width, $rs_height);
    imagecopyresized($img_base, $img, 0, 0, 0, 0, $rs_width, $rs_height, $width, $height);

    $path_info = pathinfo($path);
    switch ($path_info['extension']) {
        case "gif":
            imagegif($img_base, $path);
            break;
        case "jpeg":
            imagejpeg($img_base, $path);
            break;
        case "jpg":
            imagejpeg($img_base, $path);
            break;
        case "png":
            imagepng($img_base, $path);
            break;
    }

}

/**
 * @param $dir
 * remove directory and all it's content
 */
function rrmdir($dir){
    foreach(glob($dir . '/*') as $file)
    {
        if(is_dir($file))
        {
            rrmdir($file);
        }
        else
        {
            unlink($file);
        }
    }

    rmdir($dir);
}

/**
 * @param $image_name
 * @return string
 * return image name without extension for alt attribute of HTML <img> tag
 */
function imageAlt($image_name){
    return substr($image_name, 0, -4);
}

/**
 * @param $variable
 * @return NULL or empty string
 * empty a variable
 */
function drop_empty($variable){
  return ($var === '') ? NULL : $var;
}

/**
 * @param $string
 * @return escaped string
 * escape string from control chars for javascript parser
 */
function js_string_escape($string){
    $patterns = array("/\\\\/", '/\n/', '/\r/', '/\t/', '/\v/', '/\f/');
    $replacements = array('\\\\\\', '\n', '\r', '\t', '\v', '\f');
    $escaped_string = preg_replace($patterns, $replacements, $string);

    return $escaped_string;
}

?>