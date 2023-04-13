<?php

require './config.php';

$id = $_GET['q']; //for getting the hospital name selected by user

try {

$db = new PDO("mysql:host=$host", $user, $password, $options);
$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

$stmt2 = $db->prepare("UPDATE AmbulanceDetails SET amb_status = 'on_duty' WHERE amb_driver = ? AND amb_status = 'off_duty' LIMIT 1");
$stmt2->execute([$id]);

$stmt = $db->prepare("DELETE FROM active WHERE driver = ?");
$stmt->execute([$id]);

echo json_encode($ambulance);

} catch (Exception $e) {
echo $e->getMessage();
}

?>