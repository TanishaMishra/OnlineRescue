<?php

require './config.php';

$id = $_GET['q']; //for getting the hospital name
try {

$db = new PDO("mysql:host=$host", $user, $password, $options);
$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

$stmt1 = $db->prepare("SELECT * FROM active where hos_name=?");
$stmt1->execute([$id]);
$rows1 = $stmt1->fetchAll();

// Loop through each row and extract the data
foreach ($rows1 as $row) {
  $status[] = array(
    "driver_name" => $row['driver'],
    "ambulance_Registration" => $row['vehicle'],
    "PatientName" => $row['pat_name'],
    "PatientMob" => $row['mobile_no'],
    "Type" => $row['typee']
  );
}

echo json_encode($status);

} catch (Exception $e) {
echo $e->getMessage();
}

?>