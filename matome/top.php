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
// if (empty($users)) {
    $user_ids = "";
    $mydb = new MyDB();

    $sql = "SELECT id FROM creator ORDER BY id ASC";

    $results = $mydb->select($sql);

    foreach ($results as $row) {
        $user_ids .= $row['id'] . ',';
    }

    $mydb->close();

    if (! empty($user_ids)) {
        $user_ids = substr($user_ids, 0, - 1);
    }
// }

$api = 'users/lookup'; // アクセスするAPI

$param = new Parameters();
$param->setParam('user_id', $user_ids);

$account = Account;

$tokens = getTwitterTokens($account, "", true);
if ($tokens->isEmpty()) {
    echo "認証情報が取得できませんでした。";
}

// APIアクセス
$users = getTwitterConnection($tokens->token, $tokens->secret)->get($api, $param->parameters);

// myVarDump($users);
$creators = array();
foreach ($users as $users) {
    $creators[$users->id_str]['screen_name'] = $users->screen_name;
    $creators[$users->id_str]['description'] = $users->description;
    $creators[$users->id_str]['name'] = $users->name;
    $creators[$users->id_str]['profile_image_url'] = $users->profile_image_url;
}
// myVarDump($users);
// myVarDump($matomeList);
$creatorList = getAllCreators();

foreach ($creatorList as $creator) {
    $creator_id = $creator['id'];
    $creators[$creator_id]['user_id'] = $creator_id;
//     var_dump($creator_id);
    $matomeList = getMatomeList($creator_id, 'twitter');

    foreach ($matomeList as $matome) {
        $creators[$creator_id]['matome'][] = $matome;
    }
//     var_dump($matomeList);
}
// myVarDump($creators);

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