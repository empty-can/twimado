<?php
require_once ("init.php");
error_reporting(-1);

$count = (int)0;
$wait = 60*15+10;

$mydb = new MyDB();
$creators = $mydb->select("SELECT screen_name FROM creator ORDER BY id ASC;");
$mydb->close();

$creatorsNum = count($creators);

$api = "lists/members/create";
$list_id="1347033014086209536";
$tokens = getTwitterTokens();

$results =  getTwitterConnection($tokens->token, $tokens->secret)->get("lists/members", ['list_id'=>$list_id, 'count'=>5000]);

// echo "<html><body>";
$members = array();
foreach($results->users as $member) {
    $members[] = $member->screen_name;
    
    /**
    $mydb = new MyDB();
    $creators = $mydb->select("SELECT id, screen_name FROM creator WHERE id='$member->id_str' ORDER BY id ASC;");
    $mydb->close();
    
    if(empty($creators)) {
        echo "$member->id_str@".$member->name."@".$member->screen_name."\r\n";
    }
    **/
}
// echo "</body></html>";
// exit();

foreach($creators as $user) {
    $screen_name = $user['screen_name'];
	$count++;
	
	echo "$count/$creatorsNum\r\n";
    
    $params = array();
    $params['list_id'] = $list_id;
    $params['screen_name'] = $screen_name;

    if(in_array($screen_name, $members)) {
        echo "==============================\r\n";
        echo "登録済み：screen_name: $screen_name\r\n";
        echo "==============================\r\n";
        continue;
    }
    
    retry:
    
    try {
        $results = getTwitterConnection($tokens->token, $tokens->secret)->post($api, $params);
        
        if(isset($results->errors)) {
            $code = $results->errors[0]->code;
            
            if($code==104) {
                // 登録失敗
                
                $target_user =  getTwitterConnection($tokens->token, $tokens->secret)->get("users/show", ['screen_name'=>$screen_name]);
                
                if(isset($target_user->protected) && $target_user->protected==true) {
                    echo "==============================\r\n";
                    echo "プライベートアカウント：screen_name: $screen_name\r\n";
                    echo "==============================\r\n";
                    continue;
                } else {
                    echo "==============================\r\n";
                    echo "レート上限到達：screen_name: $screen_name\r\n";
                    echo "==============================\r\n";
                    sleep($wait);
                    goto retry;
                }
            } else if($code==108) {
                // 存在しないユーザ
                continue;
            } else {
                // 想定していないエラー
                echo "==============================\r\n";
                echo "想定していないエラー：screen_name: $screen_name\r\n";
                echo "==============================\r\n";
                var_dump($results);
                exit();
            }
        }
    } catch (Exception $e) {
        echo "==============================\r\n";
        var_dump($e);
        echo "==============================\r\n";
        sleep(10);
        goto retry;
    }
    
    echo "==============================\r\n";
    echo "登録成功：screen_name: $screen_name\r\n";
    echo "==============================\r\n";
	
	sleep((int)(60+rand(5,30)));
}

exit();