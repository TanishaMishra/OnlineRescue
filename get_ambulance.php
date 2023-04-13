<?php

require './config.php';

$id = $_GET['q']; //for getting the hospital name selected by user
$id1 = $_GET['r']; // getting userid

try {

$db = new PDO("mysql:host=$host", $user, $password, $options);
$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

$stmt = $db->prepare("SELECT * FROM AmbulanceDetails where amb_hos_id = (SELECT hos_id from hospitalService where hos_name=?) and amb_status ='on_duty' LIMIT 1");
$stmt->execute([$id]);
$rows = $stmt->fetch();
$stmt1 = $db->prepare("SELECT * FROM patients where pat_id=?");
$stmt1->execute([$id1]);
$rows1 = $stmt1->fetch();
$ambulance = array("driver_name" => $rows['amb_driver'], "ambulance_Registration" => $rows['amb_reg_no'], "PatientName" => $rows1['pat_name'], "PatientMob" => $rows1['pat_ph_no']);

//$stmt2 = $db->prepare("UPDATE AmbulanceDetails SET amb_status = 'off_duty' WHERE amb_hos_id = (SELECT hos_id FROM hospitalService WHERE hos_name = ?) AND amb_status = 'on_duty' LIMIT 1");
//$stmt2->execute([$id]);

echo json_encode($ambulance);

} catch (Exception $e) {
echo $e->getMessage();
}

?>