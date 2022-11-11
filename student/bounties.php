<?php
//Replace with cookie interaction
$username = "Stranger";
if (isset($_COOKIE["username"]))
{
    $username = $_COOKIE["username"];
}
include "../admin/setup-db.php";
//Use cookie data to grab student metadata, importantly list of completed bounty id's
if ($username)
{
    $sql = "SELECT * FROM `bounty_submissions` WHERE `user_name` = ?";
    $prepared = $db->prepare($sql);
    $prepared->bind_param("s", $username);
    $prepared->execute();
    $result = $prepared->get_result();
    if ($result)
    {
        $submissions = [];
        while ($row = $result->fetch_row())
        {
            $submissions[$row[2]] = [
                "submissionId" => $row[2],
                "submission" => $row[3],
                "status" => $row[4],
                "feedback" => $row[5],
                "bountyId" => $row[1]
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <link href="../main.css?v=1<?php echo md5_file("../main.css"); ?>" rel="stylesheet">
    <link href="student.css?v=1<?php echo md5_file("student.css"); ?>" rel="stylesheet">
    <link href="bountyformat.css?v=1<?php echo md5_file("bountyformat.css"); ?>" rel="stylesheet">
    <script type="text/javascript">
        let submissions<?php if ($submissions) echo "=".json_encode($submissions);?>;
    </script>
    <script src="bounties.js?v=1<?php echo md5_file("bounties.js"); ?>"></script>
    <script src="student.js?v=1<?php echo md5_file("student.js"); ?>"></script>
    <title>Bounties</title>
</head>

<body>
    <nav style="padding-top: 1%;">
        <button onclick="location.href='./';" style="margin-right: 65%; background-color: var(--green-button-color);">Home</button>
        <h3>Hello there, <span id="student_name"><?php echo $username; ?></span>!</h3>
        <button onclick="logOut()">Log Out</button>
    </nav>
    <dialog id="submit_bounty" class="container">
        <h1 class="title">Bounty Title</h1>
        <hr>
        <p class="description">This is the bounty's main meat</p>
        <hr>
        <h4>Your Submission:</h4>
        <textarea name="submission" class="submission" id="submission" cols="75" rows="10"></textarea>
        <button onclick="submitBounty()" style="background-color: var(--yellow-button-color); margin-left:5%;">Submit</button>
    </dialog>
    <dialog id="view_submission">
        <h1 class="title">Bounty Title</h1>
        <div class="">
            <p class="description" id="submitted_bounty_description" style="font-size: .75vw;">
                Bounty Description
            </p>
            <hr>
            <h4>Submission</h4>
            <p class="description" id="submission_description">
                Submission
            </p>
        </div>
        <hr>
        <div id="feedback_container" hidden>
            <h4>Feedback</h4>
            <p class="description">
                Feedback
            </p>
            <button style="background-color: var(--yellow-button-color);">Retry</button>
        </div>
    </dialog>
    <div class="container">
        <h1>All Uncompleted Bounties</h1>
        <div class="horizontal" style="justify-content: center;">
            <div style="display: flex; flex-direction: column;">
                <input type="text" placeholder="Search" id="searchbar" style="margin-bottom: 1em;">
                <div>
                    <input type="checkbox" id="use_title" checked="checked" onchange="filterBounties()"><label for="use_title">Titles</label>
                    <input type="checkbox" id="use_descrip" checked="checked" onchange="filterBounties()"><label for="use_descrip">Description</label>
                    <input type="checkbox" id="use_category" checked="checked" onchange="filterBounties()"><label for="use_category">Category</label>
                    <input type="checkbox" id="use_reward" checked="checked" onchange="filterBounties()"><label for="use_reward">Reward</label>
                </div>
            </div>
            <select name="category" id="category_filter" style="text-transform: capitalize;">
                <option value="None">--Filter by Type--</option>
            </select>
        </div>
        <hr>
        <div class="horizontal" style="align-items: center; justify-content: flex-end; margin-right: 12vw">
            <p style="margin-right: 2%; font-weight: bold;" id="list-view-text">List View</p>
            <label class="switch">
                <input type="checkbox" id="view_style" onchange="updateView()">
                <span class="slider round"></span>
            </label>
            <p style="margin-left: 2%;" id="grid-view-text">Grid View</p>
        </div>
        <div class="sidebar">
            <h1>Submissions</h1>
            <div class="list" id="bounty-submissions">
                <div class="card">
                    <h3 class="title">Ice House, But this Time really Long?</h3>
                    <p class="status">pending</p>
                </div>
            </div>
        </div>
        <div>
            <div id="bounties" class="card-list">
                <div onclick="toggleBounty(this)" class="card bounties" id="bounty_orig">
                    <div class="horizontal">
                        <img class="icon" src="">
                        <h1 class="title"></h1>
                        <h3 class="reward"></h3>
                    </div>
                    <p class="description" hidden></p>
                    <button onclick="startSubmission(this)">Attempt</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>