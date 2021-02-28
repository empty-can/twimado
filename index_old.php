<?php
require_once ("init.php");

$fushianasan = getGetParam('fushianasan', 'false');
$account = array();
$pawooLogin = array();
$trends = array();
$message = "";
$lists = getSessionParam("myLists", array());
$twitterLoginAccount = getSessionParam('twitterLoginAccount', "");
$tokens = getTwitterTokens();
$twitterLogin = !empty($twitterLoginAccount);

// $mo = getSessionParam('mo', 'true');
$mo = true;
setSessionParam('mo', $mo);
$hs = getSessionParam('hs', 'true');
$thumb = getSessionParam('thumb', 'true');
$t = getGetParam('t', 'w');
setGetParam('thumb', $thumb);

$from_date = getGetParam('from', "");
$to_date = getGetParam('to', "");
$smarty->assign("from_date", $from_date);
$smarty->assign("to_date", $to_date);

if(!empty($twitterLoginAccount)) {
    $hs=false;
    setSessionParam("hs", $hs);

    if (empty($lists)) {
        $api = "lists/list";
        $param = array(
            "user_id" => $twitterLoginAccount['screen_name'],
            "reverse" => true
        );

        // APIアクセス
        $lists = getTwitterConnection($tokens->token, $tokens->secret)->get($api, $param);

        setSessionParam("myLists", $lists);
    }
}

$smarty->assign("mo", $mo);
$smarty->assign("hs", $hs);
$smarty->assign("thumb", $thumb);

$smarty->assign("lists", $lists);

/**
 if($twitterLogin) {

 // トレンドを取得
 $lastGetTrendTime = getSessionParam('lastGetTrendTime', '0');

 $trends = getSessionParam('trends', array());

 if(
 ($lastGetTrendTime + 60) < time()
 || empty($trends)
 ) {
 $trends = getTrendByWords('Tokyo');

 if(isset($trends[0]) && !empty($trends[0]->message)) {
 $message = $trends[0]->message;
 } else {
 setSessionParam('lastGetTrendTime', time());
 setSessionParam('trends', $trends);
 }
 }
 }
 **/


$api = AppURL . '/api/twitter/lookup.php';
$tweets = array();

if(!empty($from_date) && !empty($to_date)) {
    $from_date = date('Y-m-d 00:00:00', strtotime($from_date));
    $to_date = date('Y-m-d 23:59:59', strtotime($to_date));
    $tweets = getFavRanking($from_date, $to_date);
    $smarty->assign("title", date('Y/m/d', strtotime($from_date))."～".date('Y/m/d', strtotime($to_date))."の画像");

} else if($t=='n') {
    $tweets = getNewTweet();
    $smarty->assign("title", "新着画像");

} else {
    if($t=='t') {
        $from_date = date('Y-m-d H:i:s',strtotime('-1 day'));
        $to_date = date('Y-m-d H:i:s');
        $smarty->assign("title", "今日の画像");

    } else if($t=='w') {
        $from_date = date('Y-m-d H:i:s',strtotime('-7 day'));
        $to_date = date('Y-m-d H:i:s',strtotime('-1 day'));
        $smarty->assign("title", "今週の画像");

    } else if($t=='m') {
        $from_date = date('Y-m-01 00:00:00');
        $to_date = date('Y-m-d H:i:s');
        $smarty->assign("title", "今月の画像");

    } else if($t=='y') {
        $from_date = date('Y-01-01 00:00:00');
        $to_date = date('Y-m-d H:i:s',strtotime('-1 day'));
        $smarty->assign("title", "今年の画像");

    } else {
        $from_date = $t.'-01-01 00:00:00';
        $to_date = $t.'-12-31 23:59:59';
        $smarty->assign("title", $t."年の画像");
    }

    $tweets = getFavRanking($from_date, $to_date);
}

$tweeIds = array();

foreach ($tweets as $tweet) {
    $tweeIds[] = $tweet["id"];
}

if(!empty($tweeIds)) {
    $param = new Parameters();
    $param->setParam('hs', getSessionParam('hs', 'true'));
    $param->setParam('thumb', getSessionParam('thumb', 'true'));
    $param->setParam('mo', getSessionParam('mo', 'false'));
    $param->setParam('count', '100');
    $param->setParam('ids', implode(",", $tweeIds));

    $tweets = getMutters($api, $param->parameters, 0)["mutters"];


    if(!empty($from_date) && !empty($to_date)) {
        usort($tweets, "sort_mutter_fav_desc");
    } else if($t=='n') {
        usort($tweets, "sort_mutter_created_at_desc");
    } else {
        usort($tweets, "sort_mutter_fav_desc");
    }
}

$smarty->assign("tweets", $tweets);

$image_file_name = "";
for ($i = 0; $i < count($tweets); $i ++) {
    if (isset($tweets[$i])) {
        if (isset($tweets[$i]["media"]) && isset($tweets[$i]["media"][0])) {
            var_dump($tweets[$i]["media"][0]);
            // echo "<br>";
            if (isset($tweets[$i]["media"][0]["raw"]) && !empty($tweets[$i]["media"][0]["raw"])) {
                $image_file_name = $tweets[$i]["media"][0]["raw"];
                break;
            } else if (isset($tweets[$i]["media"][0]["media_url_https"]) && !empty($tweets[$i]["media"][0]["media_url_https"])) {
                $image_file_name = $tweets[$i]["media"][0]["media_url_https"];
                break;
            }
        }
    }
}

// exit();

$img = file_get_contents($image_file_name);
$img_tkn = explode('/', $image_file_name);
$img_file_name = $img_tkn[count($img_tkn)-1];
$file_path='C:/xampp/htdocs/sukipic/media/'.$img_file_name;
file_put_contents($file_path, $img);


function sort_mutter_created_at_desc(array $a, array $b)
{
    if ($a['time'] == $b['time']) {
        return 0;
    }
    return ($a['time'] > $b['time']) ? -1 : 1;
}

function sort_mutter_fav_desc(array $a, array $b)
{
    $favA = convertManToNum($a['favCount']);
    $favB = convertManToNum($b['favCount']);

    if ($favA == $favB) {
        return 0;
    }
    return ($favA > $favB) ? -1 : 1;
}

function convertManToNum(string $val) {
    if(strpos($val,'万')!==false) {
        $tmp=(double)(explode("万", $val)[0]);
        $val = $tmp*10000;
    }

    return $val;
}

// 不要になったcountを削除

$csss=["top"];
$smarty->assign("csss", $csss);

$smarty->assign("jss", array());

if(!empty($image_file_name)) {
    $smarty->assign("og_image", $img_file_name);
    $smarty->assign("twitter_card", "summary_large_image");
}
// $smarty->assign("title", AppName);
// $smarty->assign("userInfo", $userInfo);
$smarty->assign("account", $account);
$smarty->assign("pawooLoginAccount", $pawooLoginAccount);
$smarty->assign("twitterLoginAccount", $twitterLoginAccount);
$smarty->assign("twitterLogin", $twitterLogin);
$smarty->assign("pawooLogin", $pawooLogin);
$smarty->assign("trends", $trends);
$smarty->assign("twitterMyLists", $twitterMyList);
$smarty->assign("pawooMyLists", $pawooMyLists);
$smarty->assign("twitterMyFriends", $twitterMyFriends);
$smarty->assign("pawooMyFriends", $pawooMyFriends);
$smarty->assign("message", $message);
$smarty->assign("t", $t);
setSessionParam("message", "");

// テンプレートを表示する
$smarty->display("index_old.tpl");