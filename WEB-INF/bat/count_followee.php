<?php
require_once ("init.php");

error_reporting(-1);

// exit();

$mydb = new MyDB();
$results = $mydb->select("SELECT id FROM new_creator WHERE screen_name='' OR name='';");
$mydb->close();
$ids = array();
foreach($results as $user) {
    $tokens = getTwitterTokens();

    $api = "users/show";

    $user_id=$user['id'];
    // API�A�N�Z�X
    $account = getTwitterConnection($tokens->token, $tokens->secret)->get($api, ['user_id' => $user_id]);

    $mydb = new MyDB();
    $sql = "UPDATE new_creator SET screen_name='$account->screen_name', name='$account->name', crawled=0 WHERE id='$user_id';";
//     myVarDump($sql);
    $results = $mydb->query($sql);
    $mydb->close();
}

exit();

// �A�N�Z�X�g�[�N���̎擾
$tokens = getTwitterTokens();

$api = 'users/show';  // �A�N�Z�X����API
    $params = array();
    $params['screen_name'] = 'orenoyome';
    $results = getTwitterConnection($tokens->token, $tokens->secret)->get($api, $params);
    // myVarDump($results);

$api = 'statuses/user_timeline';  // �A�N�Z�X����API

$friends= getFollowee('2656042465');
usort($friends, "sort_friends");
foreach ($friends as $friend) {
	echo $friend->name.'@'.$friend->screen_name.'('.$friend->id.') followers:'.$friend->followers_count."\r\n";
}

myVarDump($followees);
$idNum = count($followee_ids);
$idCount = (int)0;
$api = 'friends/list';  // �A�N�Z�X����API
$followees[] = array();
foreach ($followee_ids as $user_id) {
	$idCount++;

	if($user_id=="58166411")
		continue;

    $params = array();
    $params['user_id'] = $user_id;
    //$params['user_id'] = "16603819";
    $params['count'] = 5000;
    $results = getTwitterConnection($tokens->token, $tokens->secret)->get($api, $params);

    echo $user_id."\r\n";

    $followees = array();

	foreach ($results->users as $followee) {
		if(isset($followees[$followee->id_str])) {
			$followees[$followee->id_str]['count']++;
		} else {
			$tmp = array();
			$tmp['id'] = $followee->id_str;
			$tmp['name'] = $followee->name;
			$tmp['screen_name'] = $followee->screen_name;
			$tmp['followers_count'] = $followee->followers_count;
			$tmp['screen_name'] = $followee->screen_name;
			$tmp['count'] = 0;
			$followees[$followee->id_str] = $tmp;
		}
	}

    myVarDump($followees);

    echo "--------------------------------------------------------------\r\n";

}

/**
 *
 * @param array $a
 * @param array $b
 * @return number
 */
function sort_tweets(array $a, array $b) {
    $al = strtotime($a["created_at"]);
    $bl = strtotime($b["created_at"]);

    if ($al == $bl) {
        return 0;
    }
    return ($al < $bl) ? +1 : -1;
}

/**
 *
 * @param array $a
 * @param array $b
 * @return number
 */
function sort_friends($a, $b) {
    $al = $a->followers_count;
    $bl = $b->followers_count;

    if ($al == $bl) {
        return 0;
    }
    return ($al < $bl) ? +1 : -1;
}