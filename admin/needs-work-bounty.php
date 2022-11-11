<?php
include "./setup-db.php";

$password = $_COOKIE["password"];

if (!$password) {
    echo 200;
    return;
}

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $feedback = $_POST["feedback"];
    $submissionId = $_POST["submissionId"];
    $submissionId = intval($submissionId);
}

if (!$feedback) {
    echo 201;
    return;
}
if (!$submissionId) {
    echo 202;
    return;
}
//Run Query to confirm 'password' has access to 'submission'

//Update Submission with feedback
$sql = "UPDATE `bounty_submissions` SET `feedback` = ?, `status` = 'needs work' WHERE `submission_id` = ?";
$prepared = $db->prepare($sql);
$bindSuccess = $prepared->bind_param("si", $feedback, $submissionId);
if ($bindSuccess) {
    $prepared->execute();
    echo 100;
} 
else 
{
    //DB Error
    echo 300;
}
