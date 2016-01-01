<?php
//
// Author: TracKer
// E-Mail: tracker2k@gmail.com
// Version 0.2 (Major.Minor.Revision)
//
// Copyright (C) 2006-2007  TracKer
// 
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
//
// See LICENSE for details
//
//===========================================================================
// Info
//===========================================================================
//
// [IN]
//   userid - ID of user in TorrentPier forum
//
// [OUT]
//   PNG file
//
// [Example]
//   (Without .htaccess)
//     http://127.0.0.1/torrentbar/torrentbar.php/USERID.png
//   or
//     http://127.0.0.1/torrentbar/torrentbar.php?id=USERID
//   
//   (With .htaccess)
//     http://127.0.0.1/torrentbar/USERID.png
//
//===========================================================================
// Presets
//===========================================================================

// Database Presets
$torrentpier_config_path = "/home/localhost/www/torrentpier/forum/config.php";

// Template Presrts
$template_background = "./template/bg.png";
$template_reflection = "./template/ref.png";

// Output Presets
$rating_x = 37;
$rating_y = 6;

$upload_x = 104;
$upload_y = 6;

$download_x = 198;
$download_y = 6;


$use_binary_prefixes = true;
// true - Use Binary prefixes (KiB, MiB, etc)
// false - Use SI prefixes (KB, MB, etc)

$binary_i_with_point = true;
// true - Use "i" letter with point on top
// false - Use standard "i" letter

$space_width = 0;
// 0 - Standard
// 1, 2, 3 ... - In Pixels

//===========================================================================
// Functions
//===========================================================================

function getParam() {
  $res = 0;
  if (array_key_exists("id", $_REQUEST)) {
    $res = intval($_REQUEST["id"]);
  } else {
    $res = preg_replace("#(.*)\/(.*)\.png#i", "$2", $_SERVER['REQUEST_URI']);
    $res = intval(trim(substr(trim($res), 0, 10)));
    //if (! is_numeric($res)) { $res=0; }
  }
  return $res;
}

function mysql_init() {
  global $dbhost, $dbname, $dbuser, $dbpasswd;
  if ($dbpasswd!='') {
    $link = @mysql_connect($dbhost, $dbuser, $dbpasswd) or die("Cannot connect to database!");
  } else {
    $link = @mysql_connect($dbhost, $dbuser) or die("Cannot connect to database!");
  }
  mysql_select_db($dbname) or die("Cannot select database!");
  return $link;
}

function ifthen($ifcondition, $iftrue, $iffalse) {
  if ($ifcondition) {
    return $iftrue;
  } else {
    return $iffalse;
  }
}

function getPostfix($val) {
  global $use_binary_prefixes;

  $postfix = "B";
  if ($use_binary_prefixes) {
    if ($val>=1024)             { $postfix = "KiB"; }
    if ($val>=1048576)          { $postfix = "MiB"; }
    if ($val>=1073741824)       { $postfix = "GiB"; }
    if ($val>=1099511627776)    { $postfix = "TiB"; }
    if ($val>=1125899906842624) { $postfix = "PiB"; }
    if ($val>=1152921504606846976)       { $postfix = "EiB"; }
    if ($val>=1180591620717411303424)    { $postfix = "ZiB"; }
    if ($val>=1208925819614629174706176) { $postfix = "YiB"; }
  } else {
    if ($val>=1000)             { $postfix = "KB"; }
    if ($val>=1000000)          { $postfix = "MB"; }
    if ($val>=1000000000)       { $postfix = "GB"; }
    if ($val>=1000000000000)    { $postfix = "TB"; }
    if ($val>=1000000000000000) { $postfix = "PB"; }
    if ($val>=1000000000000000000)       { $postfix = "EB"; }
    if ($val>=1000000000000000000000)    { $postfix = "ZB"; }
    if ($val>=1000000000000000000000000) { $postfix = "YB"; }
  }

  return $postfix;
}

function roundCounter($value, $postfix) {
  $val = $value;
  $p = strtolower($postfix);
  switch ($p) {
  case "kib": $val=$val / 1024;
    break;
  case "mib": $val=$val / 1048576;
    break;
  case "gib": $val=$val / 1073741824;
    break;
  case "tib": $val=$val / 1099511627776;
    break;
  case "pib": $val=$val / 1125899906842624;
    break;
  case "eib": $val=$val / 1152921504606846976;
    break;
  case "zib": $val=$val / 1180591620717411303424;
    break;
  case "yib": $val=$val / 1208925819614629174706176;
    break;

  case "kb": $val=$val / 1000;
    break;
  case "mb": $val=$val / 1000000;
    break;
  case "gb": $val=$val / 1000000000;
    break;
  case "tb": $val=$val / 1000000000000;
    break;
  case "pb": $val=$val / 1000000000000000;
    break;
  case "eb": $val=$val / 1000000000000000000;
    break;
  case "zb": $val=$val / 1000000000000000000000;
    break;
  case "yb": $val=$val / 1000000000000000000000000;
    break;

  default:
    break;
  }
  return $val;
}

function iPostfix($change, $postfix) {
  if ($change) {
    return str_replace("i", chr(127), $postfix);
  }
}

function trck_BF_Load($name) {
  $res = 0;

  $ini = @parse_ini_file($name.".ini");
  $font = @imagecreatefrompng($name.".png");

  if (($ini) && ($font)) {
    $res = array();
    $res["font"] = $font;
    $res["params"] = $ini;
  }

  return $res;
}

function trck_BF_Unload($font) {
  @imagedestroy($font["font"]);
  unset($font["font"]);
  unset($font["params"]);
}

function trck_BF_LetterWidth($font, $letter) {
  $res = 0;
  if (strlen($letter) == 0) {
    return $res;
  }
  if (strlen($letter) == 1) {
    $ch = $letter;
  } else {
    $ch = $letter[0];
  }

  $ascii = ord($ch);
  $res = $font["params"][$ascii."_w"];
  return $res;
}

function trck_BF_setLetterWidth(&$font, $letter, $width) {
  //$res = 0;
  if (strlen($letter) == 0) {
    return 0;
  }
  if (strlen($letter) == 1) {
    $ch = $letter;
  } else {
    $ch = $letter[0];
  }

  $ascii = ord($ch);
  $font["params"][$ascii."_w"] = $width;
  //return $res;
}

function trck_BF_TextWidth($font, $text) {
  $res = 0;

  if (strlen($text) > 0) {
    for ($i = 0; $i < strlen($text); $i++) {
      $res += trck_BF_LetterWidth($font, $text[$i]);
    }
  }

  return $res;
}

function trck_BF_TextHeight($font) {
  return imagesy($font["font"]);
}

function trck_BF_DrawText($im, $x, $y, $font, $text) {
  if (strlen($text) == 0) {
    return true;
  }

  $xc = $x;
  $yc = $y;

  for ($i = 0; $i < strlen($text); $i++) {
    $ascii = ord($text[$i]);
    $sx = $font["params"][$ascii."_x"];
    imagecopy($im, $font["font"], $xc, $yc, $sx, 0, trck_BF_LetterWidth($font, $text[$i]), trck_BF_TextHeight($font));
    $xc += trck_BF_LetterWidth($font, $text[$i]);
  }
}

function trck_BF_DrawStroke($im, $text_color, $stroke_color) {
  for($y=0; $y<imagesy($im); $y++) {
    for($x=0; $x<imagesx($im); $x++) {
      $color = imagecolorat($im, $x, $y);

      if ($color==$text_color) {
        __trck_BF_drawstroke($im, $x, $y, $text_color, $stroke_color);
      }
    }
  }
}

function __trck_BF_drawstroke($im, $x, $y, $ct, $cs) {
  $im_x2 = imagesx($im)-1;
  $im_y2 = imagesy($im)-1;

  $res = false;

  $sy = $y-1; if ($sy<0) { $sy=0; }
  while (($sy<=$y+1) && (! $res)) {
    $sx = $x-1; if ($sx<0) { $sx=0; }
    while (($sx<=$x+1) && (! $res)) {
      if ((($sy>=0) && ($sy<=$im_y2)) && (($sx>=0) && ($sx<=$im_x2))) {
        $color = imagecolorat($im, $sx, $sy);
        if (($color!=$ct) && ($color!=$cs)) {
          imagesetpixel($im, $sx, $sy, $cs);
        }
      }
      $sx++;
    }
    $sy++;
  }
}

function trck_BF_CreateTextWithStroke($font, $text) {
  $im = @imagecreatetruecolor(trck_BF_TextWidth($font, $text)+2, trck_BF_TextHeight($font)+2);
  if (! $im) {
    return 0;
  }

  imagesavealpha($im, true);
  imagealphablending($im, false);
  $bgalpha_color = imagecolorallocatealpha($im, 0, 0, 0, 127);
  $text_color = imagecolorallocate($im, 255, 255, 255);
  $stroke_color = imagecolorallocate($im, 0, 0, 0);
  imagefilledrectangle($im, 0, 0, imagesx($im)-1, imagesy($im)-1, $bgalpha_color);

  trck_BF_DrawText($im, 1, 1, $font, $text);
  trck_BF_DrawStroke($im, $text_color, $stroke_color);

  return $im;
}

//===========================================================================
// Main body
//===========================================================================

// Template initialization - begin
$im_tpl_bg = @imagecreatefrompng($template_background) or die("Background Template not found!");
$im_tpl_ref = @imagecreatefrompng($template_reflection) or die("Reflection Template not found!");
// Template initialization - end

$font = trck_BF_Load("./font/visitor_rus");
if ($space_width > 0) {
  trck_BF_setLetterWidth($font, " ", $space_width);
}


$download_counter = 0;
$upload_counter = 0;
$rating_counter = 0;



$im = @imagecreatetruecolor(350, 19) or die("Cannot Initialize new GD image stream");
imagecopy($im, $im_tpl_bg, 0, 0, 0, 0, imagesx($im_tpl_bg), imagesy($im_tpl_bg));
imagedestroy($im_tpl_bg);

$userid = getParam();
if ($userid!="") {
  include($torrentpier_config_path);
  mysql_init();

  $query = "SELECT count(user_id) FROM phpbb_users WHERE user_id = '".$userid."'";
  $result = @mysql_query($query);// or die("Could not select data!");
  $counter = mysql_result($result, 0);
  mysql_free_result($result);
    
  if ($counter>0) {
    $query = "SELECT u_up_total, u_down_total FROM phpbb_bt_users WHERE user_id = ".$userid;
    $result = mysql_query($query);// or die("Could not select data!");

    while ($data = mysql_fetch_array($result))
    {
      $upload_counter = $data['u_up_total'];
      $download_counter = $data['u_down_total'];
      if ($download_counter>0) {
        $rating_counter = $upload_counter / $download_counter;
      }
    }
  }
}

$dot_pos = strpos((string) $rating_counter, ".");
if ($dot_pos>0) {
  $rating_counter = (string) round(substr((string) $rating_counter, 0, $dot_pos+1+2), 2);
} else {
  $rating_counter = (string) $rating_counter;
}

$rating_counter = strval($rating_counter);
$im_rating = trck_BF_CreateTextWithStroke($font, $rating_counter);
imagecopy($im, $im_rating, $rating_x, $rating_y-2, 0, 0, imagesx($im_rating), imagesy($im_rating));



$postfix = getPostfix($upload_counter);
$upload_counter = roundCounter($upload_counter, $postfix);
$dot_pos = strpos((string) $upload_counter, ".");
if ($dot_pos>0) {
  $upload_counter = (string) round(substr((string) $upload_counter, 0, $dot_pos+1+2), 2);
} else {
  $upload_counter = (string) $upload_counter;
}

$upload_counter = strval($upload_counter)." ".iPostfix($binary_i_with_point, $postfix); 
$im_upload = trck_BF_CreateTextWithStroke($font, $upload_counter);
imagecopy($im, $im_upload, $upload_x, $upload_y-2, 0, 0, imagesx($im_upload), imagesy($im_upload));



$postfix = getPostfix($download_counter);
$download_counter = roundCounter($download_counter, $postfix);
$dot_pos = strpos((string) $download_counter, ".");
if ($dot_pos>0) {
  $download_counter = (string) round(substr((string) $download_counter, 0, $dot_pos+1+2), 2);
} else {
  $download_counter = (string) $download_counter;
}

$download_counter = strval($download_counter)." ".iPostfix($binary_i_with_point, $postfix);
$im_download = trck_BF_CreateTextWithStroke($font, $download_counter);
imagecopy($im, $im_download, $download_x, $download_y-2, 0, 0, imagesx($im_download), imagesy($im_download));

trck_BF_Unload($font);

imagecopy($im, $im_tpl_ref, 0, 0, 0, 0, imagesx($im_tpl_ref), imagesy($im_tpl_ref));
imagedestroy($im_tpl_ref);

header("Content-type: image/png");
imagepng($im);
imagedestroy($im);
