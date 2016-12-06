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
 * @param $arr
 * @return string
 * convert PHP array to JSON (for older PHP versions)
 */
function array2json($arr) {
    if(function_exists('json_encode')) return json_encode($arr); //Lastest versions of PHP already has this functionality.
    $parts = array();
    $is_list = false;

    //Find out if the given array is a numerical array
    $keys = array_keys($arr);
    $max_length = count($arr)-1;
    if(($keys[0] == 0) and ($keys[$max_length] == $max_length)) {//See if the first key is 0 and last key is length - 1
        $is_list = true;
        for($i=0; $i<count($keys); $i++) { //See if each key correspondes to its position
            if($i != $keys[$i]) { //A key fails at position check.
                $is_list = false; //It is an associative array.
                break;
            }
        }
    }

    foreach($arr as $key=>$value) {
        if(is_array($value)) { //Custom handling for arrays
            if($is_list) $parts[] = array2json($value); /* :RECURSION: */
            else $parts[] = '"' . $key . '":' . array2json($value); /* :RECURSION: */
        } else {
            $str = '';
            if(!$is_list) $str = '"' . $key . '":';

            //Custom handling for multiple data types
            if(is_numeric($value)) $str .= $value; //Numbers
            elseif($value === false) $str .= 'false'; //The booleans
            elseif($value === true) $str .= 'true';
            else $str .= '"' . addslashes($value) . '"'; //All other things
            // :TODO: Is there any more datatype we should be in the lookout for? (Object?)

            $parts[] = $str;
        }
    }
    $json = implode(',',$parts);

    if($is_list) return '[' . $json . ']';//Return numerical JSON
    return '{' . $json . '}';//Return associative JSON
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

?>