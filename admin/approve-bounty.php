<?php
include "./setup-db.php";

if ($_SERVER["REQUEST_METHOD"] === 'POST')
{
    $password = $_POST["password"];
    $submissionId = $_POST["submissionId"];
}
//If not posted, grab from cookie
if(!$password) if(isset($_COOKIE["password"])) $password = $_COOKIE["password"];

if (!$password)
{
    echo 200;
    return;
}

//Check our password, and grab our phrase
$sql = "SELECT `phrase`,`acess` FROM `passwords` WHERE `password` = ?";
$prepared = $dba->prepare($sql);
$prepared->bind_param("s", $password);
$prepared->execute();
$result = $prepared->get_result();
if ($result)
{
    $row = $result->fetch_row();
    $phrase = $row[0];
    $access = $row[1];
}
if ($access !== 1)
{
    //grab username from submissions, and then grab the associated phrase

    //Make sure we have access to this submission id
}

//Update submission status from pending to 'complete'
$sql = "UPDATE `bounty_submissions` SET `status` = 'complete' WHERE `submission_id` = ?";
$prepared = $db->prepare($sql);
$submissionId = intval($submissionId);
$prepared->bind_param("i", $submissionId);
$prepared->execute();

echo 100;