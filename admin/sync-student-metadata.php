<?php
require_once("../db_credentials.php");
$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);


$web_function = "core_completion_get_activities_completion_status";
$base_url = "https://sigmateaches.moodlecloud.com/webservice/rest/server.php?";
$moodle_token = "f90311194f5a225e78fd3b9c060a6dde";

$allSql = "SELECT `moode_id`, `user_name` FROM `student_logins` WHERE 1";
$res = $db->query($allSql);

if ($res->num_rows > 0) {
    while ($col = $res->fetch_row()) {
        $id = $col[0];
        $user = $col[1];
        
        //URL for Crate Users call
        $final_url = $base_url . "moodlewsrestformat=json" . "&wsfunction=" . $web_function . "&wstoken=" . $moodle_token;
        $params = "&courseid=5&userid=$id";
        $final_url = $final_url . $params;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $final_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //1 Means that we want the response returnd into the variable, not echoed

        $result = curl_exec($ch);
        $json = json_decode($result);
        $completedActivities = array();
        if(!$json->statuses) {
            continue;
        }
        if($user == "no") echo $result;
        foreach ($json->statuses as $node) {
            if ($node->modname === "lesson" && $node->state == 1) {
                //$a = array($node->cmid, $node->state);
                array_push($completedActivities, $node->cmid);
            }
        }
        $json = json_encode($completedActivities);
        $iOrUSQL = "insert into `student_simple_data` values (\"$user\", \"$json\") "."ON DUPLICATE KEY UPDATE `completed_activities`=\"$json\"";
        $db->query($iOrUSQL);
        echo "Database Query Ran";
    }
}