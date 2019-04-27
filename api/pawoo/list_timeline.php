<?php
require_once ("init.php");

$id = getGetParam('id', '');
$limit = getGetParam('limit', 40);
$max_id = getGetParam('max_id', '');

if ($limit > 40)
    $limit = 40;

$params = array(
    "limit" => $limit,
    "only_media" => true
);

if (! empty($max_id)) {
    $params['max_id'] = $max_id;
}

$response = array();
$response['mutters'] = array();
$oldests = array();

$oldest = "";

$connection = getMastodonConnection(PawooDomain);

$api = "api/v1/lists/$id/accounts";
$members = $connection->executeGetAPI($api);
$ids = array();

foreach($members as $member) {
    $ids[]  = $member["id"];
}

foreach ($ids as $id) {
    $api = "api/v1/accounts/$id/statuses";

    $toots = $connection->executeGetAPI($api . '?' . http_build_query($params));

    if (! empty($toots)) {
        $mutters = array();

        foreach ($toots as $toot) {
            $tmp = new Pawoo($toot);

            $oldest = $tmp;
            $originalId = $tmp->originalId();

            if ($tmp->hasMedia() && ! isset($mutters[$originalId]))
                $mutters[$originalId] = $tmp;
        }

        $oldests[] = obj_to_array($oldest);

        $response['mutters'] = array_merge($response['mutters'], $mutters);
    }
}

usort($oldests, "sort_mutter_by_time");

$response['oldest_mutter'] = $oldests[count($oldests)-1];

echo json_encode($response);