<?php
require_once ("init.php");

$param = new Parameters();
$param->constructFromGetParameters();

$id = $param->parameters['id'];
$domain = $param->parameters['domain'];
$time = $param->parameters['time'];
$screen_name = $param->parameters['screen_name'];

if($screen_name!="undefined") {
    $mydb = new MyDB();

    $results = $mydb->select("SELECT id FROM creator WHERE screen_name = '$screen_name';");

    if(empty($results)){
        $tokens = getTwitterTokens();

        $param->moveValue('ids', 'id');
        $api = "users/show";

        // APIアクセス
        $account = getTwitterConnection($tokens->token, $tokens->secret)
                        ->get($api, ['screen_name' => $screen_name]);

        $user_id = $account->id_str;
        $sql = "INSERT INTO creator (id, domain, screen_name, name) VALUES ('$user_id', 'twitter', '$account->screen_name', '$account->name');";
        $results = $mydb->insert($sql);
    } else {
        $results = $mydb->select("SELECT id FROM creator WHERE screen_name = '$screen_name';")[0];
        $user_id=$results['id'];
    }

    if(!empty($user_id)) {
        $result = $mydb->select("SELECT id FROM mutter WHERE id='$id'");

        if(empty($result)) {
            $sql = "INSERT INTO mutter (id, domain, user_id, created_at, media) VALUES ('$id', '$domain', '$user_id', '$time', 1);";

            $results = $mydb->query($sql);
            echo $sql;
        } else {
            echo "Already registered.";
        }
    }


    $mydb->close();
}
