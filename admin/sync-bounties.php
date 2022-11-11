<?php
require_once('../db_credentials.php');
$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.sigmateaches.com/_functions/allbounties/250");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPGET, true);
$bounties = curl_exec($ch);
if(!$bounties)
{

}
if ($bounties) {
    $bounties = json_decode($bounties, true);
    if (!$bounties) {
        echo "Failed to Fetch, Retry";
        return;
    }
    foreach ($bounties["items"] as $bounty) {
        $title = $bounty["name"];
        $description = $bounty["bountyDescription"];
        $title = str_replace("'", "\'", $title);
        $description = str_replace('"', '\"', $description);
        $description = str_replace('"', '\"', $description);
        $description = str_replace("`", "\`", $description);
        $id = $bounty["_id"];
        $bountyImage = $bounty["bountyImage"];
        $category = $bounty["bountyTheme"];
        $reward = $bounty["rewardAmount"];
        //SQL Query to see if it exists
        $exists = "SELECT * FROM `bounties` WHERE `id` = '$id'";
        $res = $db->query($exists);
        if ($res) {
            //Do not add bounty, maybe update
            $update = "UPDATE `bounties` SET `description`= '$description',`category`='$category' WHERE `id`='$id'";
            $db->query($update);
        } else {
            $insert = "INSERT INTO `bounties`(`title`, `reward`, `description`, `id`, `category`) VALUES ('$title','$reward','$description','$id', '$category')";
            echo $insert;
            $db->query($insert);
        }
    }
    echo "Success";
} else {
    echo "Failed";
}
