<?php
require_once("../db_credentials.php");

$pass;
$user;
$newUser;
if($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $pass = $_POST["password"];
    $user = $_POST["user"];
    $newUser = $_POST["newUser"];
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
if(!$newUser){
    echo "203";
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
    echo "204";
    return;
}
$userSecret = $userRes->fetch_row()[5];
if(!$userSecret)
{
    echo "205";
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
        $org_user = $row[1];
        $sql = "SELECT * FROM `licenses` WHERE `organization_name` = \"$org_user\"";
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
    //Can access
    //Confirm username has not been taken
    $checksql = "SELECT * FROM `student_login` WHERE `user_name` = \"$user\"";
    $res = $dbl->query($checksql);
    if($res->num_rows > 0)
    {
        echo "300";
        return;
    }
    $id;
    $idsql = "SELECT `moode_id` FROM `student_logins` WHERE `user_name` = \"$user\";";
    $res = $dbl->query($idsql);
    if($res->num_rows > 0)
    {
        $id = $res->fetch_row()[0];
    }
    if(!$id) 
    {
        echo "206";
        return;
    }
    
    $updateSql = "UPDATE `student_logins` SET `user_name`='$newUser' WHERE `user_name` = '$user'";
    $dbl->query($updateSql);

    $web_function = "core_user_update_users";
    $base_url = "https://sigmateaches.moodlecloud.com/webservice/rest/server.php?";
    $moodle_token = "f90311194f5a225e78fd3b9c060a6dde";

    //URL for Crate Users call
    $final_url = $base_url."moodlewsrestformat=json"."&wsfunction=".$web_function."&wstoken=".$moodle_token;
    $params = "&users[0][id]=$id&users[0][username]=$newUser";
    $final_url = $final_url.$params;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $final_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //1 Means that we want the response returnd into the variable, not echoed

    $result = curl_exec($ch);

    echo $result;
    echo "100";
}