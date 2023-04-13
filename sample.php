<?php
$address = $_POST['location'];
$city = $_POST['city'];
$state = $_POST['state'];
$hos = $_POST['hos'];

echo $hos;  
/*$id = "300/A, 5th Cross Rd, 2nd Phase, Ramesh Nagar, Vimanapura";
$id1 = "Bengaluru";
$id2 = "Karnataka 560037";

//$id = "RameshNagar";
//$id1 = "Bangalore";
//$id2 = "Karnataka";
require './config.php';
$db = new PDO("mysql:host=$host", $user, $password, $options);
$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );


function get_coordinates($city, $street, $province)
{
    $address = urlencode($city . ',' . $street . ',' . $province);
    $url = "https://maps.google.com/maps/api/geocode/json?address=" . $address . "&key=AIzaSyBwfZLkThCCQWYptWELcrp5d9uXtgvywcc";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($ch);
    curl_close($ch);
    $response_a = json_decode($response);
    $status = $response_a->status;

    if ($status == 'ZERO_RESULTS') {
        return FALSE;
    } else {
        $return = array('lat' => $response_a->results[0]->geometry->location->lat, 'long' => $long = $response_a->results[0]->geometry->location->lng);
        return $return;
    }
}

function GetDrivingDistance($lat1, $lat2, $long1, $long2)
{
    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=driving&key=AIzaSyBwfZLkThCCQWYptWELcrp5d9uXtgvywcc";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($ch);
    curl_close($ch);
    $response_a = json_decode($response, true);
    $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
    $time = $response_a['rows'][0]['elements'][0]['duration']['text'];

    return array('distance' => $dist, 'time' => $time);
}

//get address from the database
$stmt = $db->prepare("SELECT * FROM locations where city = ?");
$stmt->execute([$id1]);
$result = $stmt->fetchAll();
$distance = [];
$time = [];
$hospital = [];
foreach ($result as $row) {
    $stmt1 = $db->prepare("SELECT hos_name, hos_username FROM hospitalService where location_id = ?");
    $stmt1->execute([$row['id']]);
    $result1 = $stmt1->fetch();
    $coordinates2 = get_coordinates($row['city'], $row['street'], $row['state']);
    $coordinates1 = get_coordinates($id1, $id, $id2);
    if (!$coordinates1 || !$coordinates2) {
        echo 'Bad address.';
    } else {
        $dist = GetDrivingDistance($coordinates1['lat'], $coordinates2['lat'], $coordinates1['long'], $coordinates2['long']);

        $distance[$result1['hos_username']] = $dist['distance'];
        $time[$result1['hos_username']] = $dist['time'];
        $hospital[$result1['hos_username']] = $result1['hos_name'];
    }
}
$cred = array("distance" => $distance,"time" => $time, "hospital" =>$hospital);

echo json_encode($cred);
//$coordinates1 = get_coordinates( $id1 , $id , $id2);
/*if ( !$coordinates1 || !$coordinates2 )
{
    echo 'Bad address.';
}
else
{
    $dist = GetDrivingDistance($coordinates1['lat'], $coordinates2['lat'], $coordinates1['long'], $coordinates2['long']);
    
    $cred = array("distance" => $dist['distance'],"time"=> $dist['time']);

    echo json_encode($cred);
    
}*/
