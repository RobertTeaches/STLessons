<?php
$user;
$bountyId;
$submission;
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $user = $_POST["username"];
    $bountyId = $_POST["id"];
    $submission = $_POST["submission"];
}


if(!$user)
{
    echo "200";
    return;
}
if(!$bountyId)
{
    echo "201";
    return;
}
if(!$submission)
{
    echo "202";
    return;
}

//$submission = str_replace('"', '\"', $submission);
//$submission = str_replace("'", "\'", $submission);

require_once('../db_credentials.php');
$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
$countsql = "SELECT COUNT(*) FROM `bounty_submissions`";
$count = $db->query($countsql);
$count = intval($count->fetch_row()[0]) + 1;

$sql = "INSERT INTO `bounty_submissions`(`user_name`, `bounty_id`, `submission_id`, `submission`) VALUES ('$user','$bountyId','$count',?)";
$statement = $db->prepare($sql);
$statement->bind_param("s", $submission);
$statement->execute();
echo 100;