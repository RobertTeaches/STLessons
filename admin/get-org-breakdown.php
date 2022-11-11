<?php
require_once("../db_credentials.php");


if($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $pass = $_POST["password"];
    $phrase = $_POST["phrase"];
}

$db = mysqli_connect(DB_SERVER, ADMIN_DB_USER, DB_PASS, ADMIN_DB_NAME);
$dbs = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

if(!$pass)
{
    echo "200";
    return;
}
if(!$phrase)
{
    $phrasesql = "SELECT `phrase` FROM `passwords` WHERE `password` = \"$pass\"";
    $r = $db->query($phrasesql);
    if($r->num_rows > 0)
    {
        $phrase = $r->fetch_row()[0];
    }
}
if(!$phrase)
{
    echo "201";
    return;
}

//Select all student usernames by phrase
$sql = "SELECT `user_name` FROM `student_logins` WHERE `secret_phrase` = \"$phrase\"";
$students = $dbs->query($sql);

$lessonData = include("get-lesson-metadata.php");
$lessonJson = json_encode($lessonData);

if($students->num_rows > 0){
    $allStudents = [];
    while($data = $students->fetch_row())
    {
        $datasql = "SELECT `completed_activities` FROM `student_simple_data` WHERE `user_name`=\"$data[0]\"";
        $datares = $dbs->query($datasql);
        if($datares->num_rows > 0)
        {
            $studentCompletion = $datares->fetch_row()[0];
            $completionArray = json_decode($studentCompletion);
            
            //Get Student Breakdown comparing to lesson data
            $totalLines = 0;
            $totalTerminal = 0;
            $concepts = array(); // Dict-Style object  where key is concept and value is `student completion rating` totalled
            $difficulties = array();
            foreach($completionArray as $cmid)
            {
                $lesson = $lessonData[$cmid];
                $totalLines += $lesson["linesOfCode"];
                $totalTerminal += $lesson["terminalCommands"];
                $dif = $lesson["difficulty"];
                if(!array_key_exists($dif, $difficulties)) $difficulties[$dif] = 1;
                else $difficulties[$dif] += 1;
            }


            array_push($allStudents, [
                "user"=>$data[0],
                "completedActivites"=>$studentCompletion,
                "totalLinesOfCode"=>$totalLines,
                "totalTerminalCommands"=>$totalTerminal,
                "difficulties"=>$difficulties
            ]);
        }
    }
    $orgLines = 0;
    $orgTerminals = 0;
    $orgActivityCompletion = 0;
    foreach($allStudents as $student)
    {
        $orgLines += $student["totalLinesOfCode"];
        $orgTerminals += $student["totalTerminalCommands"];
        //Org activity completion = (SUM(studentSteps/totalSteps))/numOfStudents
    }

    $allData = array(
        "students"=>$allStudents,
        "lessonData"=>$lessonData,
        "totalLines"=>$orgLines,
        "totalTerminals"=>$orgTerminals
    );
    echo json_encode($allData);
    return;
}
else {
    echo json_encode("no students");
}
