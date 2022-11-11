<?php
include "./setup-db.php";
$password = $_COOKIE["password"];


if (!$password)
{
    echo " <h1>You must be logged in to edit bounties</h1>";
    return;
}

#region SELECT `acess` FROM `passwords` WHERE `password` = $password
$checkPass = "SELECT `acess` FROM `passwords` WHERE `password` = ?";
$prepared = $dba->prepare($checkPass);
$prepared->bind_param("s", $password);
$prepared->execute();
#endregion
$result = $prepared->get_result();

if ($result)
{
    $access = $result->fetch_row()[0];
}

if ($access !== 1)
{
    echo "<h1>You must be a SigmaTeaches administrator to access this page</h1>";
    return;
}

$bounties = include "./get-bounties.php";

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../main.css" rel="stylesheet">
    <link href="./edit-bounties.css" rel="stylesheet">

    <script lang="javascript" src="admin.js?v=1<?php echo md5_file("admin.js"); ?>"></script>
    <script lang="javascript" src="edit-bounties.js?v=1<?php echo md5_file("edit-bounties.js"); ?>"></script>
    <script type="text/javascript">
        let bounties = <?php echo json_encode($bounties) ?>;
    </script>
    <title>Edit Bounties</title>
</head>

<body>
    <nav onclick="logout()"><button>Logout</button></nav>
    <div class="top">
        <h2>Select Bounty</h2>
        <input onfocus="resetList(this)" type="text" list="bounties" name="select_bounty" size="50" id="select_bounty">
        <datalist id="bounties">
        </datalist>
        </input>
    </div>

    <div class="container card" id="edit-container">
        <h1>Title</h1>
        <input type="text" id="title" style="width: 80%;"></input>
        <h3>Description</h3>
        <!-- <div contenteditable="true" id="description"></div> -->
        <div id="description">
            <p>No Bounty Loaded</p>
        </div>
        <script src="https://cdn.ckeditor.com/4.13.0/standard/ckeditor.js"></script>
        <script>
            CKEDITOR.replace('description');
        </script>
        <div class="horizontal" style="margin:auto;">
            <div>
                <h3>Reward</h3>
                <input type="text" name="reward" id="reward" style="width: 33%;">
            </div>
            <div>
                <h3>Category</h3>
                <input onfocus="resetList(this)" type="text" name="category" id="category" class="category" list="categories">
                <datalist id="categories">
                </datalist>
            </div>
        </div>
        <button onclick="updateBounty()">Change Bounty</button>
    </div>
</body>

</html>