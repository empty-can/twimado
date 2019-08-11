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

// myVarDump($matomeList);

// assignメソッドを使ってテンプレートに渡す値を設定
$smarty->assign("title", "まとめトップ");

$smarty->assign("matomeList", $matomeList);
$csss=["top"];
$smarty->assign("csss", $csss);

$smarty->assign("jss", array());

// テンプレートを表示する
$smarty->display("matome_top.tpl");