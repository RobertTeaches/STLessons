<?php 

function random_str(
    $length,
    $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') 
    {
        $str = '';
        $max = mb_strlen($keyspace, '8bit') - 1;
        if ($max < 1) {
            throw new Exception('$keyspace must be at least two characters long');
        }
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[random_int(0, $max)];
        }
        return $str;
}

require_once('../db_credentials.php');
$pass;
$org_name;
$num_of_licenses;
$desired_phrase;
$email;

if($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $pass = $_POST["password"];
    $org_name = $_POST["name"];
    $num_of_licenses = $_POST["number"];
    $email = $_POST["email"];
    $desired_phrase = $_POST["phrase"];
}
$db_admin = mysqli_connect(DB_SERVER, ADMIN_DB_USER, DB_PASS, ADMIN_DB_NAME);
$db_license = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
//Query Phrase DB for an unused phrase, and then set it to used
if (!$desired_phrase)
{

}


//Validation
if(!$pass)
{
    echo "300";//Acess denied
    return;
}
//Query pass db, and see if access level is 1
else
{
    $sql = "SELECT acess FROM `passwords` WHERE password ='".$pass."' LIMIT 1";
    $res = $db_admin->query($sql);
    if ($res)
    {
        $row = $res->fetch_row();
        $access = $row[0];
        if($access !== "1")
        {
            echo "300";
            return;
        }
    }
}

if(!$org_name)
{
    echo "400\nNeed an Organization Name";
    return;
}
else
{
    $sql = "SELECT * FROM `licenses` WHERE `organization_name` ='$org_name';";
    $res = $db_license->query($sql);
    if($res->num_rows >0)
    {
        echo "400\nOrganization Exists";
        return;
    }
}

if(!$num_of_licenses)
{
    echo "450\nNeed License Number";
    return;
}
if(!is_numeric($num_of_licenses))
{
    echo "450\nKicense Must Be Number";
    return;
}
$num_of_licenses = intval($num_of_licenses);
if(!$email){
    echo "475\nNo Email";
    return;
}

//Generate new password that does not exist in db
$new_pass = random_str(10);


$admin_sql = "INSERT INTO `passwords` (`password`, `user`, `acess`) VALUES ('$new_pass', '$org_name', '2')";
$license_sql = "INSERT INTO `licenses` (`organization_name`, `license_limit`, `current_usage`, `secret_phrase`, `license_date`, `email`) VALUES ('$org_name', '$num_of_licenses', '0', '$desired_phrase', CURRENT_DATE, '$email')";

$a_success = $db_admin->query($admin_sql);
$l_success = $db_license->query($license_sql);

if(!$a_success){
    echo "200\nDatabase Failure";
    return;
}
if(!$l_success){
    echo "250\n Database Failure";
    return;
}


//Emailing New License Holder
$subject = "Your License Account for ST Lessons";
$headers = "From: contact@sigmateaches.com"."\r\n"."CC: contact@sigmateaches.com";
$text = "Thank you for signing up for a Licensing Account for ST Lessons! Your students can now begin creating user accounts and accessing their learning content at https://www.stlessons.com \nTo create an account, they will need to use your Secret Phrase: $desired_phrase. You will have Administration tools available to you, at https://www.stlessons.com/admin To access those tools, you will need to provde the following password: $new_pass";

//$text = wordwrap($text, 175);
mail($email, $subject, $text, $headers);

echo "100";