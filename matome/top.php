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

$matomeList = getMatomeList($target_id);
$creatorList = getAllCreators();
$creators = array();

foreach ($creatorList as $creator) {
    $creators[$creator['id']]['user_info'] = $creator;
}

foreach ($matomeList as $matome) {
    $creators[$matome['user_id']]['matome'][] = $matome;
}

// myVarDump($creators);

// assignメソッドを使ってテンプレートに渡す値を設定
$smarty->assign("title", "まとめトップ");

$smarty->assign("matomeInfo", $creators);
$csss=["top"];
$smarty->assign("csss", $csss);

$smarty->assign("jss", array());

// テンプレートを表示する
$smarty->display("matome_top.tpl");