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

	    if (! is_array($response)) {
	        $errorMutter = new ErrorMutter();
	        $errorMutter->addMessage("レスポンスの形式が不正でした。:"+var_export($tmp));
	        $result['mutters'][] = obj_to_array($errorMutter);
	        $result['oldest_mutter'] = new EmptyMutter("system");
	        return $result;
        }

        if (isset($response['error']) && ! empty($response['error'])) {
            $errorMutter = new ErrorMutter();
            $errorMutter->addError($response['error']);
            $result['mutters'][] = obj_to_array($errorMutter);
            $result['oldest_mutter'] = new EmptyMutter("system");
            return $result;
        }

        $result['mutters'] = array_merge($result['mutters'], $response['mutters']);

        if (isset($response['oldest_mutter']) && isset($response['oldest_mutter']['id'])) {
            $oldest_id = $response['oldest_mutter']['id'];
        } else {
            $oldest_id = - 1;
        }

        if (isset($response['latest_mutter']) && isset($response['latest_mutter']['id'])) {
            $latest_id = $response['latest_mutter']['id'];
        } else {
            $latest_id = - 1;
        }

        $parameters['max_id'] = $oldest_id;

	} while (count($response['mutters']) < 1 && $oldest_id > 0);

	if($oldest_id != $original_oldest_id) {
	    $result['oldest_id'] = $oldest_id;
	} else {
	    $result['oldest_id'] = -1;
	}

	$result['latest_id'] = $latest_id;

	return $result;
}