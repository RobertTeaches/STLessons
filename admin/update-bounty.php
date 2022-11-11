<?php
include "./setup-db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $title = $_POST["title"];
    $description = $_POST["description"];
    $reward = $_POST["reward"];
    $category = $_POST["category"];
    $bountyId = $_POST["bountyId"];
}
//If not posted, grab from cookie
$password = $_COOKIE["password"];
if (!isset($password))
{
    echo 200;
    return;
}
if (!$title)
{
    echo 201;
    return;
}
if (!isset($description))
{
    echo 202;
    return;
}
if (!isset($reward))
{
    echo 203;
    return;
}
if (!isset($category))
{
    echo 204;
    return;
}


//Confirm Password Has Access
$sql = "SELECT `acess` FROM `passwords` WHERE `password` = ?";
$prepared = $dba->prepare($sql);
$prepared->bind_param("s", $password);
$prepared->execute();
$res = $prepared->get_result();
if ($res->num_rows > 0)
{
    $access = $res->fetch_row()[0];
}

if (!$access)
{
    echo 205;
    echo $password;
    return;
}
if ($access !== 1)
{
    echo 206;
    echo $access;
    return;
}
//We have access to update bounties

//Make SQL Query to Update DB
$sql = "UPDATE `bounties` SET `title`= ?, `description`= ?, `reward` = ?, `category` = ? WHERE `id` = ?";
$prepared = $db->prepare($sql);
if ($prepared)
{
    $reward = intval($reward);
    $prepared->bind_param("ssiss", $title, $description, $reward, $category, $bountyId);
    $prepared->execute();
    echo 100;

}
else
{
    echo 300;
    return;
}
