<?php
require_once("../db_credentials.php");
$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
$dba = mysqli_connect(DB_SERVER, ADMIN_DB_USER, DB_PASS, ADMIN_DB_NAME);
$password = $_GET["password"];

if(!$password) if(isset($_COOKIE["password"])) $password = $_COOKIE["password"];

if (!$password)
{
    echo 200;
    return;
}
//Grab org phrase via password
$sql = "SELECT `phrase`,`acess` FROM `passwords` WHERE `password` = ?";
$prepared = $dba->prepare($sql);
if (!$prepared)
{
    echo "Failed";
    return;
}
$prepared->bind_param("s", $password);
$prepared->execute();
$result = $prepared->get_result();
if ($result)
{
    $result = $result->fetch_row();
    $phrase = $result[0];
    if (!$phrase)
    {
        echo 201;
        return;
    }
    $access = $result[1];
    if (!$access)
    {
        echo 204;
        return;
    }
}

$submissions = [];
$bounties = include("get-bounties.php");
if (!$bounties)
{
    echo 202;
    return;
}

//Grab all students with phrase OR all students if access level is 1 (ST admin)
if ($access != 1)
{
    $sql = "SELECT `user_name` FROM `student_logins` WHERE `secret_phrase` = ?";
    $prepared = $db->prepare($sql);
    $prepared->bind_param("s", $phrase);
    echo "<script>console.log('$access')</script>";
}
else
{
    $sql = "SELECT `user_name` FROM `student_logins`";
    $prepared = $db->prepare($sql);
}

$prepared->execute();
$result = $prepared->get_result();
if ($result)
//Grab all student submissions and load them into dynamic object
{
    $user;
    //Preparing Submission Sql
    $sql = "SELECT * FROM `bounty_submissions` WHERE `status` = 'pending' AND `user_name` = ? ";
    //echo $sql;
    $prepared = $db->prepare($sql);
    $prepared->bind_param("s", $user);

    while ($data = $result->fetch_row())
    {
        $user = $data[0];
        $prepared->execute();
        $sResult = $prepared->get_result();
        if ($sResult->num_rows != 0)
        {
            // echo $user;
            while ($row = $sResult->fetch_row())
            {
                $bId = $row[1];
                $sId = $row[2];
                $subm = $row[3];
                $submissions[$sId] = [
                    "username" => $user,
                    "bounty_id" => $bId,
                    "submission_id" => $sId,
                    "submission" => $subm
                ];
            }
        }
    }
}
//echo json_encode($bounties);
//echo json_encode($submissions);
//return;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../main.css" rel="stylesheet">
    <link href="grade.css" rel="stylesheet">
    <script lang="javascript" src="admin.js"></script>
    <script lang="javascript" src="grade-bounties.js?v=1<?php echo md5_file("grade-bounties.js"); ?>"></script>
    <script>
        logged_password = '<?php echo $password; ?>';
        let submissions = <?php echo json_encode($submissions) ?>;
        let bounties = <?php echo json_encode($bounties) ?>;
    </script>
    <title>Grade Bounties</title>
</head>

<body>
    <div class="container card">
        <dialog id="submission_view">
            <div>
                <h5 class="user"></h5>
                <h1 class="title">Submission</h1>
            </div>
            <p class="submission"></p>
            <button style="background-color: var(--green-button-color);" class="approve">Approve</button>
            <button style="background-color: var(--red-button-color);" class="needs_work">Needs Work</button>
        </dialog>
        <dialog id="add_feedback">
            <div>
                <h1 class="title">Title</h1>
                <hr style=margin-bottom:0px;>
                <p class="submission">This is where the submission will go</p>
                <hr>
                <h3>Would you to give feedback?</h3>
                <textarea style="width: 100%; resize: none; margin-bottom: 2em;" name="feedback" id="feedback_text" cols="50" rows="20"></textarea>
                <button style="background-color: var(--red-button-color);" id="feedback_button">No</button>
            </div>
        </dialog>
        <table>
            <tbody>
                <?php
                //Create a <tr> that contains the username, bounty title, and button to view submission
                foreach ($submissions as $s)
                {
                    $user = $s['username'];
                    $title = $bounties[$s['bounty_id']]["title"];
                    $submission_id = $s["submission_id"];
                    echo "<tr><td>$user</td><td>$title</td><td><button onclick='viewSubmission($submission_id)'>View</button></td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</body>

</html>