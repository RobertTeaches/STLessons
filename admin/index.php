<!DOCTYPE html>
<html>
    <head>
        <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
        <link href="../main.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js" integrity="sha512-ElRFoEQdI5Ht6kZvyzXhYG9NqjtkmlkfYk0wr6wHxU9JEHakS7UJZNeml5ALk+8IKlU6jDgMabC3vkumRokgJA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script lang="javascript" src="admin.js?v=1<?php echo md5_file("admin.js"); ?>"></script>
    </head>
    <title></title>
    <body>
        <nav><button onclick="logout()" id="logout">Logout</button></nav>
        <div class="modal-background container" id="modal-original">
            <div class="modal-content card" id="modal">
                <span class="close" id="modal-close">&times;</span>
                <div class="card" id="modal-content">
                    Modal Content
                </div>
            </div>
        </div>
        <div class="container" id="admin-container">
            <h1>Admin Access</h1>
            <form action="javascript:;" onsubmit="adminLogin(this)">
                <p>Password</p>
                <input type="password" name="admin_password" required>
            </form>
        </div>
        <div class="container" id="dashboard-container" hidden>
            <p>Empty until Login!</p>
            <!--This will be held in the php page 'admin-dashboard.php'-->
        </div>
    </body>




</html>