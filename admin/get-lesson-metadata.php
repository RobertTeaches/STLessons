<?php
require_once("../db_credentials.php");
$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

$sql ="SELECT * FROM `lesson_simple_data` WHERE 1";
$res= $db->query($sql);
if($res->num_rows > 0)
{
    $data = array();
    while($row = $res->fetch_row())
    {
        $col = [
            "cmid"=>$row[0],
            "linesOfCode"=>$row[1],
            "terminalCommands"=>$row[2],
            "difficulty"=>$row[3],
            "category"=>$row[4],
            "completionTime"=>$row[5],
            "numOfSteps"=>$row[6]
        ];
        $data[$row[0]] = $col;
    }
    if(($_SERVER['REQUEST_METHOD'] === 'GET'))
        echo json_encode($data);
    else
        return $data; 
}