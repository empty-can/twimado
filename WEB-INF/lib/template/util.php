<?php
require_once ("init.php");

function getMutters(string $api, array $parameters, $original_oldest_id) {

	$result = array();
	$result['mutters'] = array();
	$result['errors'] = array();
	$oldest_id = -1;

	do {
	    $tmp = getRequest($api, $parameters);
	    $response = my_json_decode($tmp, true);
        
        if (!is_array($response))
            break;
        
        if (isset($response['error'])) {
            $errors[] = array("pawoo"=>$response['error']);
        }

        $result['mutters'] = array_merge($result['mutters'], $response['mutters']);

        if (isset($response['oldest_mutter']) && isset($response['oldest_mutter']['originalId']))
        	$oldest_id = $response['oldest_mutter']['originalId'];
        else
            $oldest_id = - 1;
            
        $parameters['max_id'] = $oldest_id;

    } while (count($response['mutters']) < 1 && $oldest_id > 0);
    
    if($oldest_id != $original_oldest_id)
        $result['oldest_id'] = $oldest_id;
    else
        $result['oldest_id'] = -1;
        
	return $result;
}