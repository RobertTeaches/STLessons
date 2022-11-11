<?php
    $bounties = include "./get-bounties.php";
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" integrity="sha512-XMVd28F1oH/O71fzwBnV7HucLxVwtxf26XV8P4wPk26EDxuGZ91N8bsOttmnomcCD3CS5ZMRL50H0GgOHvegtg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="./bountyprintouts.js?v=<?echo md5_file("./bountyprintouts.js") ?>"></script>
    <script type="text/javascript">
        let bounties <?php if($bounties)echo "=".json_encode($bounties) ?>
    </script>
    <title>Get Printouts</title>
</head>
<body>
    <h1>Hi</h1>
    <input onchange="resetList(this)" type="text" id="select_bounty" list="bounties">
    <datalist id="bounties">

    </datalist>
    <button onclick="getPrintout()">Get Printout(s)</button>
    <div>
        
    </div>
</body>
</html>