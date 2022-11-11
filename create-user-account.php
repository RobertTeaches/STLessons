<?php
    require_once('db_credentials.php');
    $secret;
    $user;
    $pass;
    $email;
    $firstName;
    $lastName;


    if($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        $secret = $_POST["secret"];
        $user = $_POST["username"];
        $pass = $_POST["password"];
        $email = $_POST["email"];
        $firstName = $_POST["firstname"];
        $lastName = $_POST["lastname"];
    }
    $db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
    $org;
    //Validation
    if(!$secret) 
    {
        echo "no secret";
        return;
    }
    else
    {
        $sql = 'SELECT * FROM `licenses` WHERE secret_phrase = "'.$secret.'"';
        $sRes = $db->query($sql);
        if($sRes)
        {
            $row = $sRes->fetch_row();
            $org = $row[0];
        }
        else
        {
            echo "secret failed: ".$secret;
            return;
        }
    } 

    if(!$user)
    {
        echo "no user";
        return;
    }
    //Make Sure that User does not exist in the database
    else
    {
        $sql = 'SELECT * FROM `student_logins` WHERE user_name ="'. $user.'"';
        $res = $db->query($sql);
        if($res->num_rows > 0)
        {//Username exists already
            echo "user exists";
            return;
        }
    }
    if(!$pass)
    {
        echo "no pass";
        return;
    }
    if(!$email)
    {
        echo "no email";
        return;
    }
    if(!$firstName)
    {
        echo "no first name";
        return;
    }
    if(!$lastName)
    {
        echo "no last name";
        return;
    }
    //Moodle POST Variables
    $moodle_token;
    $course_id;
    $web_function = "core_user_create_users";
    $base_url = "https://sigmateaches.moodlecloud.com/webservice/rest/server.php?";
    $moodle_token = "f90311194f5a225e78fd3b9c060a6dde";
    $course_id = "5";

    //URL for Crate Users call
    $final_url = $base_url."moodlewsrestformat=json"."&wsfunction=".$web_function."&wstoken=".$moodle_token;
    $params = "&users[0][username]=$user&users[0][password]=$pass&users[0][firstname]=$firstName&users[0][lastname]=$lastName&users[0][email]=$email";
    $final_url = $final_url.$params;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $final_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //1 Means that we want the response returnd into the variable, not echoed
  

    $result = curl_exec($ch);
    $rJson = json_decode($result, true);
    if($rJson[0] && $rJson[0]['id'])
    {
        //Moodle POST variables
        $id = $rJson[0]['id'];
        $web_function = "enrol_manual_enrol_users";
        $params = "&enrolments[0][roleid]=5&enrolments[0][userid]=".$id."&enrolments[0][courseid]=".$course_id;
        
        //Moodle final REST url
        $final_url = $base_url."moodlewsrestformat=json"."&wsfunction=".$web_function."&wstoken=".$moodle_token.$params;
        curl_setopt($ch, CURLOPT_URL, $final_url);
        curl_setopt($ch, CURLOPT_POST, true);
        $result = curl_exec($ch);
        if ($result == "null"){
            $sql = 'INSERT INTO `student_logins` (`full_name`, `user_name`, `password`, `moode_id`, `governing_body`, `secret_phrase`) VALUES'. 
                                                '("'.$firstName.' '.$lastName.'","'.$user.'","'.$pass.'","'. $id.'","'.$org.'","'. $secret.'")';
            if($db->query($sql) === TRUE)
            {
                $sql = "UPDATE `licenses` SET `current_usage`= (current_usage + 1) WHERE secret_phrase = \"$secret\"";
                if($db->query($sql) === TRUE)
                {
                    echo "success";
                }
            }
            else
            {
                echo "Error w/ ". $sql."/n".$db->error;
            }
        }
    }
    else{
        echo "id failed to dejson";
    }
?>