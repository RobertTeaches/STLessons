<?php
$dataurl = "https://docs.google.com/spreadsheets/d/e/2PACX-1vRNZM8N89-9vD17VE2HFrdyQ2ibq3Fhu26bvyyLBj9qvo-zh30FY8vFKu5x-8Dya4jDSv0tKp43xl7K/pub?gid=0&single=true&output=tsv";
require_once("../db_credentials.php");
$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

if (($handle = fopen($dataurl, "r")) !== FALSE) {
    $skip = true; //Skip the first row, just headers
    while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
        if($skip) {$skip = false; continue;}//skipping
        //The data we care about
        if ($data[0]) {
            $spreadsheet_data[] = array(
                $data[0],//name -            0
                $data[1],//cmid-             1
                $data[3],//cateogory-        2
                $data[13],//concepts-        3
                $data[14],//completionTime - 4
                $data[15],//numberOfSteps -  5
                $data[17],//linesOfCode   -  6
                $data[18],//terminalCommands -7
                $data[16]//Difficulty
            );
        }
    }
    fclose($handle);

    foreach($spreadsheet_data as $d)
    {
        $sql = "insert into `lesson_simple_data` values (\"$d[1]\",$d[6],$d[7], \"$d[8]\",\"$d[2]\",$d[4], $d[5]) "
        ."ON DUPLICATE KEY UPDATE `lines_of_code`=$d[6], `terminal_commands`=$d[7], `difficulty`=\"$d[8]\", `category`=\"$d[2]\", `completion_time`=$d[4],`number_of_steps`=$d[5];";
        echo($sql);
        $db->query($sql);
    }
    echo "Success";
} else
    die("Problem reading csv");
//echo json_encode($spreadsheet_data);

//TODO: Check for a provided password/move file to private folder and have a running script?