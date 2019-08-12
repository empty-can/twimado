<?php
require_once ("init.php");

$submit_data = getPostParam('jsons', '');

if(!empty($submit_data)) {
    $mydb = new MyDB();
    $creators = array();

    $results = $mydb->select("SELECT * FROM creator;");

    foreach ($results as $row) {
        if(isset($creators[$row['screen_name']])) {
            $creators[$row['screen_name']] = $row['id'];
        }
    }

    // $results = $mydb->query("DELETE FROM mutter");

    $jsons = str_replace(array("\r\n","\r","\n"), "\n", $submit_data);
    $jsons = explode("\n", $jsons);

    foreach ($jsons as $json) {

//         myVarDump($json);
        $decoded_json = json_decode($json);
//         myVarDump($decoded_json);

        if(!empty($decoded_json->retweet_id) || !empty($decoded_json->rt_user))
            continue;

//         var_dump($decoded_json);
        $tweet_id = $mydb->escape($decoded_json->id);

        if(!isset($creators[$mydb->escape($decoded_json->screen_name)])) {
            continue;
        }

        $user_id = $creators[$mydb->escape($decoded_json->screen_name)];
//         var_dump($decoded_json);

        if(!empty($decoded_json->created_at_date)) {
            $created_at = str_replace('/', '-', $decoded_json->created_at_date);
        } else if(!empty($decoded_json->created_at)) {
            $created_at = date("Y-m-d H:i:s", $decoded_json->created_at);
        } else if(!empty($decoded_json->time)) {
            $created_at = date("Y-m-d H:i:s", strtotime($decoded_json->time));
        }
//         var_dump($created_at);

        $sql = "INSERT INTO mutter (id, domain, user_id, created_at) VALUE ($tweet_id, 'twitter', $user_id, '$created_at')";
//         myVarDump($sql);
        $results = $mydb->insert($sql);
        var_dump($results);
        echo " ";
    }


    $mydb->close();
}


?>
<html>
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />

<meta content="http://www.suki.pics/favicon.png" itemprop="image">
<link rel="shortcut icon" href="http://www.suki.pics/favicon.ico" type="image/vnd.microsoft.icon">
<link rel="icon" href="http://www.suki.pics/favicon.ico" type="image/vnd.microsoft.icon">
<link rel="apple-touch-icon" href="http://www.suki.pics/favicon.ico" type="image/vnd.microsoft.icon">
<title>json解析</title>
<link rel="stylesheet" type="text/css" href="http://www.suki.pics/css/common.css?2019-08-04_14:04:14" />
<link rel="stylesheet" type="text/css" href="http://www.suki.pics/css/top.css?2019-08-04_14:04:14" />
<link rel="stylesheet" type="text/css" href="http://www.suki.pics/css/top_m.css?2019-08-04_14:04:14" />
<link rel="stylesheet" type="text/css" href="http://www.suki.pics/css/top_pc.css?2019-08-04_14:04:14" />
</head>
<body>
	<form action="./edit_json.php" method="POST">
		<textarea name="jsons" rows="25" cols="200"><?php echo $submit_data;?></textarea>
  		<button name="submit" value="true">送信</button>
	</form>
</body>
</html>