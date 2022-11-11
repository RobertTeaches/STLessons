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

    $sql = "SELECT `completed_activities` FROM `student_simple_data` WHERE `user_name`=\"$user\"";
    $res = $dbl->query($sql);
    if($res->num_rows>0)
    {
        echo $res->fetch_row()[0];
        return;
    }

    echo "500";
}