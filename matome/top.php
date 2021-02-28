<?php
require_once ("init.php");

$param = new Parameters();
$param->constructFromGetParameters();

$param->setInitialValue('hs', getSessionParam('hs', 'true'));
$param->setInitialValue('thumb', getSessionParam('thumb', 'true'));
$param->setInitialValue('mo', getSessionParam('mo', 'true'));

$param->setInitialValue('domain', 'pawoo');
$param->setInitialValue('count', '20');

$param->setParam('account', Account);
$param->setParam('pawoo_id', PawooAccountID);

$target_id = $param->getValue('target_id', '');
$user_ids = array();
// if (empty($users)) {
    $mydb = new MyDB();

    $sql = "SELECT user_id FROM `matome` GROUP BY user_id ORDER BY `user_id` ASC";

    $results = $mydb->select($sql);

    foreach ($results as $row) {
        $user_ids[] = $row['user_id'];
    }

    $mydb->close();
// }

$api = 'users/lookup'; // アクセスするAPI

$param = new Parameters();
$param->setParam('user_id', implode(",",$user_ids));

$account = Account;

$tokens = getTwitterTokens($account, "", true);
if ($tokens->isEmpty()) {
    echo "認証情報が取得できませんでした。";
}

// APIアクセス
$users = getTwitterConnection($tokens->token, $tokens->secret)->get($api, $param->parameters);

$creators = array();
foreach ($users as $user) {
    $creators[$user->id_str]['screen_name'] = $user->screen_name;
    $creators[$user->id_str]['description'] = $user->description;
    $creators[$user->id_str]['name'] = $user->name;
    $creators[$user->id_str]['profile_image_url'] = $user->profile_image_url;
    // myVarDump($creators);
}
// myVarDump($users);
// myVarDump($matomeList);
$creatorList = getAllCreators();

foreach ($creatorList as $creator) {
    $creator_id = $creator['id'];
    if(isset($creators[$creator_id])) {
	    $creators[$creator_id]['user_id'] = $creator_id;
	//     var_dump($creator_id);
	    $matomeList = getMatomeList($creator_id, 'twitter');

	    foreach ($matomeList as $matome) {
	        $creators[$creator_id]['matome'][] = $matome;
	    }
	//     var_dump($matomeList);
    }
}
// myVarDump($creators);

$smarty->assign("AppURL", AppURL);
// assignメソッドを使ってテンプレートに渡す値を設定
$smarty->assign("title", "まとめトップ");

$smarty->assign("creators", $creators);
$smarty->assign("users", $users);
// myVarDump($creators);
$csss=["top"];
$smarty->assign("csss", $csss);

$smarty->assign("jss", array());

// テンプレートを表示する
$smarty->display("matome_top.tpl");