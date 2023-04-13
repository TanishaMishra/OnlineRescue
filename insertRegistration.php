<?php

//Test Sessions variable with hardcoding
// $_SESSION["uid"]="Karups";
// $_SESSION["pwd"]="1234";

include './config.php';

if (isset($_SESSION["uid"])) {
    $uid = $_SESSION["uid"];
}


$pname = $_POST["pname"];
$page = $_POST["page"];
$pgender = $_POST["pgender"];

$pemail = $_POST["pemail"];
$pphone = $_POST["pphone"];
$puser = $_POST["puser"];
$ppwd = $_POST["ppwd"];
$ppwd1 = $_POST["ppwd1"];



if ($connection == "local") {
    $t_patients = "patients";
} else {
    $t_patients = "$database.patients";
}

try {
    $db = new PDO("mysql:host=$host", $user, $password, $options);
    //echo "Database connected successfully <BR>";

    $sql_insert = "INSERT INTO $t_patients (pat_name,pat_pwd,pat_email_id,pat_ph_no,pat_age,pat_gender,pat_username)  
        VALUES ('$pname', '$ppwd' , '$pemail','$pphone', $page , '$pgender' ,'$puser' )";
    echo "$sql_insert <br>";
    echo "query insertion begins here<br>";
    //echo "SQL Statement $sql_insert";
    $stmt = $db->prepare($sql_insert);
    $rows = $stmt->execute();

    echo "query has been executed<br>";


    //echo "Rows  $rows <BR>";

    if ($rows > 0) {

        echo "query has been inserted<br>";
        //echo   $rows['PatName'];
        //echo '<script>alert("Login Successful")</script>';
        $_SESSION["valid"] = TRUE;
        $_SESSION["uid"] = $_POST["puser"];
        $_SESSION["pwd"] = $_POST["ppwd"];

        if (isset($_SESSION["uid"])) {
            $uid = $_SESSION["uid"];
            echo "session uid is $uid<br>";
        }
        header("Refresh: 1; URL = index.php");
        exit();
    } else {
        echo '<script>alert("Insert Appropriate values")</script>';
    }
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

?>