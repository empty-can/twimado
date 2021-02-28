<?php
require_once ("init.php");

$response = array();

$mydb = new MyDB();

try {
    $mydb->begin_transaction();

    $sql = "SELECT screen_name FROM new_creator WHERE crawled=0 ORDER BY id ASC LIMIT 1;";
    $screen_name = $mydb->query($sql)->fetch_array(MYSQLI_NUM)[0];

    $response['screen_name'] = $screen_name;

    $sql = "UPDATE new_creator SET crawled=1 WHERE screen_name='$screen_name';";
    $screen_name = $mydb->query($sql);

    $mydb->commit();
} catch (mysqli_sql_exception $exception) {
    $mydb->rollback();
    $response['error'] = 'Transaction rollbacked.';
} finally {
    $mydb->close();
}

echo json_encode($response);
