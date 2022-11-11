<?php
    require_once('../db_credentials.php');
    $dba = mysqli_connect(DB_SERVER, ADMIN_DB_USER, DB_PASS, ADMIN_DB_NAME);
    $db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);