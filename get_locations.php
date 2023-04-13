<?php

require 'config.php';

try {
$db = new PDO("mysql:host=$host", $user, $password, $options);
$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

$sth = $db->query("SELECT * FROM locations");
$locations = $sth->fetchAll();

echo json_encode( $locations );

} catch (Exception $e) {
echo $e->getMessage();
}

?>