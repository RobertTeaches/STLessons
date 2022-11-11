<?php
    ini_set('display_errors', 1);
    require_once('../db_credentials.php');

    if($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        $pass = $_POST["password"];
    }

    $db = mysqli_connect(DB_SERVER, ADMIN_DB_USER, DB_PASS, ADMIN_DB_NAME);
    $sql = "SELECT acess FROM `passwords` WHERE password ='".$pass."' LIMIT 1";
    $res = $db->query($sql);
    $access;
    if ($res)
    {
        $row = $res->fetch_row();
        $access = $row[0];
        if(!$access)
        {
            echo "300";
            return;
        }
    }

    
    //HTML for SigmaTeaches Admin Dashboard
    $st_admin_page = '
    ';
    //HTML for Organization's Admin Dashboard (including teachers/facilitators)
    $org_admin_page = '
    
    ';

    $org_parent_page = '
    
    ';
    
    //echo "Access Level: $access";
    if (strcmp($access, "1") === 0) 
    {
        $page = file_get_contents("./private/level_1_admin.html");
        $js = file_get_contents("./private/level_1_admin.js");
        $ar = array(
            "page"=>$page,
            "js"=>$js
        );
        $ret = json_encode($ar);
        echo $ret;
    }
    else if(strcmp($access, "2") === 0)
    {
        $page = file_get_contents("./private/level_2_admin.html");
        $js = file_get_contents("./private/level_2_admin.js");
        $ar = array(
            "page"=>$page,
            "js"=>$js
        );
        $ret = json_encode($ar);
        echo $ret;
    }