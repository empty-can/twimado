<?php
require_once ("init.php");

$account = getPostParam('account', '');
$id = getPostParam('id', '');
$list_id = getPostParam('list_id', '');
$limit = getPostParam('limit', 40);
$max_id = getPostParam('max_id', '');
$mo = getPostParam('mo', 'true');

if(!empty($account)) {
    $pair = get_access_tokens($account, 'pawoo');
    $access_token = $pair['access_token'];
} else if(!empty($id)){
    $access_token = getPassengerTokens($id, 'pawoo')['access_token'];
} else {
    $access_token = PawooAccessToken;
}

if ($limit > 40)
    $limit = 40;

$params = array(
    "limit" => $limit,
    , "only_media" => ($mo=='true') ? true : false
);

if (! empty($max_id)) {
    $params['max_id'] = $max_id;
}

$response = array();
$response['mutters'] = array();
$oldests = array();

$oldest = "";

$connection = getMastodonConnection(PawooDomain, $access_token);

$api = "api/v1/lists/$id/accounts";
$members = $connection->executeGetAPI($api);
$ids = array();

foreach($members as $member) {
    $ids[]  = $member["id"];
}

foreach ($ids as $id) {
    $api = "api/v1/accounts/$list_id/statuses";

    $toots = $connection->executeGetAPI($api . '?' . http_build_query($params));

    if (! empty($toots)) {
        $mutters = array();

        foreach ($toots as $toot) {
            $tmp = new Pawoo($toot);

            $oldest = $tmp;
            $originalId = $tmp->originalId();

            if($mo=='false') {
                $mutters[$originalId] = $tmp;
            } else if ($tmp->hasMedia() && ! isset($mutters[$originalId])) {
                $mutters[$originalId] = $tmp;
            }
        }

        $oldests[] = obj_to_array($oldest);

        $response['mutters'] = array_merge($response['mutters'], $mutters);
    }
}

usort($oldests, "sort_mutter");

$response['oldest_mutter'] = $oldests[count($oldests)-1];

echo json_encode($response);