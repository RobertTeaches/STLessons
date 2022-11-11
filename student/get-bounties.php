<?php
require_once('../db_credentials.php');
$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

$sql = "SELECT * FROM `bounties` WHERE 1";
$res = $db->query($sql);

if($res)
{
    $items = [];
    while($row = $res->fetch_row())
    {
        array_push($items, [
            "title"=>$row[0],
            "reward"=>$row[1],
            "description"=>$row[2],
            "longId"=>$row[3],
            "category"=>$row[4],
        ]);
    }
    echo json_encode($items);
}