<?php
require_once ("init.php");

/**
 *
 * @param int $date
 */
function date4timeline(int $date) {
    $diff = time() - $date;
    $result = "";

    if($diff<60) {
        $result = "１分以内";
    } else if($diff<3600) {
        $result = floor($diff/60)."分前";
    } else if($diff<86400) {
        $result = floor($diff/3600)."時間前";
    } else if($diff<172800) {
        $result = "昨日";
    } else if($diff<604800) {
        $result = floor($diff/86400)."日前";
    } else {
        $result = date("Y/m/d", $date);
        if(!$result)
            $result = "不明";
    }

    return $result;
}

function sort_mutter(array $a, array $b)
{
    if ($a['sortValue'] == $b['sortValue']) {
        return 0;
    }
    return ($a['sortValue'] > $b['sortValue']) ? -1 : 1;
}

function sort_mutter_asc(array $a, array $b)
{
    if ($a['sortValue'] == $b['sortValue']) {
        return 0;
    }
    return ($a['sortValue'] < $b['sortValue']) ? -1 : 1;
}

function isImg(string $url) {
    return (getMediaType($url)=="img");
}

function isVideo(string $url) {
    return (getMediaType($url)=="video");
}

function isObject(string $url) {
    return (getMediaType($url)=="obj");
}

/**
 * URLが指すメディアのタイプを返す
 *
 * @param string $url
 * @return string
 */
function getMediaType(string $url) {
    $type = "";

    if(contains($url, "png")) {
        $type = "img";
    } else if(contains($url, "jpg")) {
        $type = "img";
    } else if(contains($url, "mp4")) {
        $type = "video";
    } else if(contains($url, "m3u8")) {
        $type = "video";
    } else if(contains($url, "gif")) {
        $type = "img";
    } else if(contains($url, "jpeg")) {
        $type = "img";
    } else if(contains($url, "ping")) {
        $type = "img";
    } else {
        $type = "obj";
    }

    return $type;
}

/**
 * URLの拡張子に従ってタグを生成。
 *
 * @param string $url
 */
// function generateMediaLinkTag(string $url) {
//     $result = "";
//     $type = "";

//     if(contains($url, "png")) {
//         $type = "img";
//     } else if(contains($url, "jpg")) {
//         $type = "img";
//     } else if(contains($url, "mp4")) {
//         $type = "video";
//     } else if(contains($url, "m3u8")) {
//         $type = "video";
//     } else if(contains($url, "gif")) {
//         $type = "img";
//     } else if(contains($url, "jpeg")) {
//         $type = "img";
//     } else if(contains($url, "ping")) {
//         $type = "img";
//     } else {
//         $type = "obj";
//     }

//     if ($type=="img"){
//         $result = '<img src="'.$url.'" />';
//     } else if ($type=="video"){
//         $result = '<video src="'.$url.'" preload="metadata" controls />';
//     } else if ($type=="obj"){
//         $result = '<object data="'.$url.'" type="image/png"></object>';
//     }

//     return $result;
// }