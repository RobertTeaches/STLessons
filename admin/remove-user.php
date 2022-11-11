<?php
require_once("../db_credentials.php");

$pass;
$user;
if($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $pass = $_POST["password"];
    $user = $_POST["user"];
}
if(!$pass)
{
    echo "200";
    return;
}
if(!$user)
{
    echo "201";
    return;
}


$db = mysqli_connect(DB_SERVER, ADMIN_DB_USER, DB_PASS, ADMIN_DB_NAME);
$dbl = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

$sql = "SELECT acess,user FROM `passwords` WHERE password ='".$pass."' LIMIT 1";
$res = $db->query($sql);

$usersql = "SELECT * FROM `student_logins` WHERE `user_name` = \"$user\"";
$userRes = $dbl->query($usersql);

if(!$userRes) 
{
    echo "202";
    return;
}
$userSecret = $userRes->fetch_row()[5];
if(!$userSecret)
{
    echo "203";
    return;
}

$access;
if ($res)
{
    $row = $res->fetch_row();
    $access = $row[0];
    //3xx means access error
    if(!$access)
    {
        echo "300";
        return;
    }
    $canAccess;
    //Top level admin can always access
    if($access === "1")
    {
        $canAccess = TRUE;
    }
    if($access === "2")
    {
        $user = $row[1];
        $sql = "SELECT * FROM `licenses` WHERE organization_name = $user";
        $lResult = $dbl->query($sql);
        if($lResult)
        {
            $org_phrase = $lResult->fetch_row()[3];
            //Organization Has Access to this Student
            if($org_phrase === $userSecret)
            {
                $canAccess = TRUE;
            }
        }
    }

    if(!$canAccess) 
    {
        echo "305";
        return;
    }

    //echo "Can Access User";

    //Get user Moodle ID from student_logins DB
    $idSql = "SELECT moode_id FROM `student_logins` WHERE `user_name` = \"$user\";";;
    $idR = $dbl->query($idSql);
    $id;
    if($idR)
    {
        $id = $idR->fetch_row()[0];
    }
    if(!$id)
    {
        echo "500";
        return;
    }

    //Remove User from student_logins DB
    $removeSql = "DELETE FROM `student_logins` WHERE `user_name` = \"$user\"";
    $delR = $dbl->query($removeSql);

    //Lower current_usage of Organization
    $lowerSql = "UPDATE `licenses` SET `current_usage`= CASE WHEN (current_usage > 0) THEN (current_usage - 1) ELSE 0 WHERE secret_phrase = \"$userSecret\"";
    $dbl->query($lowerSql);

    //Call Moodle REST to remove Student from Moodle
    //MyMoodle/webservice/rest/server.php?wstoken=MyToken&wsfunction=core_user_delete_users&moodlewsrestformat=json&userids[0]=272
    $final_url = "https://sigmateaches.moodlecloud.com/webservice/rest/server.php?wstoken=f90311194f5a225e78fd3b9c060a6dde&wsfunction=core_user_delete_users&moodlewsrestformat=json&userids[0]=$id";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $final_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    if(empty($result))
    {
        echo "100";
        return;    
    }
    $rJson = json_decode($result, true);
    echo $rJson;
}
