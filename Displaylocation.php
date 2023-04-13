<?php

session_start();
if (isset($_SESSION["uid"])) {
    $uid = $_SESSION["uid"];
}

if (isset($_SESSION['errorMessage'])) {
    echo "<script type='text/javascript'>
            alert('" . $_SESSION['errorMessage'] . "');
          </script>";
    //to not make the error message appear again after refresh:
    unset($_SESSION['errorMessage']);
}

if (
    isset($_POST["login"]) && !empty($_POST["uid"])
    && !empty($_POST["pwd"])
) {
    $uid = $_POST['uid'];
    $pwd = $_POST['pwd'];

    include 'config.php';
    //set table name based on local or remote connection
    if ($connection == "local") {
        $t_patients = "patients";
    } else {
        $t_patients = "$database.patients";
    }

    try {
        $db = new PDO("mysql:host=$host", $user, $password, $options);

        $sql_select = "Select * from $t_patients where pat_username = '$uid' and pat_pwd = '$pwd'";
        //echo "SQL Statement is : $sql_select <BR>";
        //echo "SQL Connection is : $host <BR>";


        $stmt = $db->prepare($sql_select);
        $stmt->execute();

        if ($rows = $stmt->fetch()) {
            //echo "SQL Statement is : $rows <BR>";
            $_SESSION['valid'] = TRUE;
            $_SESSION['uid'] = $_POST["uid"];
            $_SESSION["pwd"] = $_POST["pwd"];
        } else {
            echo '<script> alert("Invalid PatName or Password. Try again");</script>';
            header('refresh:0; url=./index.php');
            exit();
        }
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
    }
}
?>

<?php
if (isset($_SESSION["uid"])) {
?>

    <!DOCTYPE html>

    <html lang="en">

    <head>
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=&callback=myMap"></script>
        <meta charset="utf-8" />
        <title>Location Fetch</title>
        <link rel="stylesheet" href="./assets/css/location.css">
        <script type="text/javascript">
            var hospitals;

            function makeRequest(url, callback) {
                var request;
                if (window.XMLHttpRequest) {
                    request = new XMLHttpRequest(); // IE7+, Firefox, Chrome, Opera, Safari
                } else {
                    request = new ActiveXObject("Microsoft.XMLHTTP"); // IE6, IE5
                }
                request.onreadystatechange = function() {
                    if (request.readyState == 4 && request.status == 200) {
                        callback(request);
                    }
                }
                request.open("GET", url, true);
                request.send();
            }

            var map;

            var center = new google.maps.LatLng(12.971458, 79.154816);

            var geocoder = new google.maps.Geocoder();
            var infowindow = new google.maps.InfoWindow();

            var directionsService = new google.maps.DirectionsService();
            var directionsDisplay = new google.maps.DirectionsRenderer();

            function init() {

                var mapOptions = {
                    zoom: 13,
                    center: center,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                }

                map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);

                directionsDisplay.setMap(map);
                directionsDisplay.setPanel(document.getElementById('directions_panel'));

                // Detect user location
                if (navigator.geolocation) {

                    navigator.geolocation.getCurrentPosition(function(position) {

                        //var userLocation = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);

                        var userLocation = new google.maps.LatLng(12.9698, 79.1559);
                        //alert(userLocation);

                        geocoder.geocode({
                            'latLng': userLocation
                        }, function(results, status) {
                            //alert(status)
                            
                                document.getElementById('start').value = results[0].formatted_address;
                                displayLocation(results[0].formatted_address);
                                var resaddress = results[0].formatted_address.split(",");
                                var count = 0,
                                    state, city, street = "";

                                for (i = (resaddress.length - 2); i >= 0; i--) {
                                    count += 1;
                                    if (count == 1) {
                                        state = resaddress[i];
                                    } else if (count == 2) {
                                        city = resaddress[i];
                                    } else {
                                        street = street + resaddress[i];
                                    }
                                }
                                document.getElementById('city').value = city;
                                document.getElementById('state').value = state;
                                makeRequest("get_disttime.php?q=" + street.trim() + "&r=" + city.trim() + "&s=" + state.trim(), function(data) {
                                    var data = JSON.parse(data.responseText);
                                    let distances = [];
                                    let times = [];
                                    let names = [];
                                    hospitals = data.hospital;

                                    for (const [key, value] of Object.entries(data.distance)) {
                                        distances.push(value);
                                    }
                                    for (const [key, value] of Object.entries(data.time)) {
                                        times.push(value);
                                    }
                                    for (const [key, value] of Object.entries(data.hospital)) {
                                        names.push(value);
                                    }

                                    var selectBox = document.getElementById('destination');
                                    let optionString = '';
                                    for (let i = 0; i < names.length; i++) {
                                        optionString = `${names[i]} - ${distances[i]} - ${times[i]}`;
                                        addOption(selectBox, optionString, names[i]);
                                        optionString = '';
                                    }
                                });
                                // alert(city);
                                // alert(state);
                                // alert(street);
                            

                            function makeRequest1(url, callback) {
                                var request;
                                if (window.XMLHttpRequest) {
                                    request = new XMLHttpRequest(); // IE7+, Firefox, Chrome, Opera, Safari
                                } else {
                                    request = new ActiveXObject("Microsoft.XMLHTTP"); // IE6, IE5
                                }
                                request.onreadystatechange = function() {
                                    if (request.readyState == 4 && request.status == 200) {
                                        callback(request);
                                    }
                                }
                                request.open("GET", url, true);
                                request.send();
                            }

                        });


                    }, function() {
                        alert('Geolocation is supported, but it failed');
                    });
                }

                makeRequest('get_locations.php', function(data) {

                    var data = JSON.parse(data.responseText);
                    var selectBox = document.getElementById('destination');

                    for (var i = 0; i < data.length; i++) {
                        displayLocationHospital(data[i]);
                    }
                });
            }

            function addOption(selectBox, text, value) {
                var option = document.createElement("OPTION");
                option.text = text;
                option.value = value;
                selectBox.options.add(option);
            }

            function displayLocation(location) {

                var content = '<div class="infoWindow"><strong>' + "your position" + '</strong>' +
                    '<br/>' + location + '</div>';

                console.log(location)
                geocoder.geocode({
                    'address': location
                }, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        var marker = new google.maps.Marker({
                            map: map,
                            position: results[0].geometry.location,
                            title: "My location"
                        });

                        google.maps.event.addListener(marker, 'click', function() {
                            infowindow.setContent(content);
                            infowindow.open(map, marker);
                        });
                    }
                });
            }

            function displayLocationHospital(location) {

                var content = '<div class="infoWindow"><strong>' + location.name + '</strong>' +
                    '<br/>' + location.address +
                    '<br/>' + location.description + '</div>';

                if (parseInt(location.lat) == 0) {
                    geocoder.geocode({
                        'address': location.address
                    }, function(results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            var marker = new google.maps.Marker({
                                map: map,
                                position: results[0].geometry.location,
                                title: location.name
                            });

                            google.maps.event.addListener(marker, 'click', function() {
                                infowindow.setContent(content);
                                infowindow.open(map, marker);
                            });
                        }
                    });
                } else {
                    var position = new google.maps.LatLng(parseFloat(location.lat), parseFloat(location.lon));
                    var marker = new google.maps.Marker({
                        map: map,
                        position: position,
                        title: location.name
                    });

                    google.maps.event.addListener(marker, 'click', function() {
                        infowindow.setContent(content);
                        infowindow.open(map, marker);
                    });
                }
            }

            function gethos() {
                var hospital = document.getElementById("destination").value;
                console.log(hospital);
                for (const [key, value] of Object.entries(hospitals)) {
                    if (value == hospital) {
                        document.getElementById("hos").value = key;
                    }
                }
            }

            // function calculateRoute() {

            //     var start = document.getElementById('start').value;
            //     var destination = document.getElementById('destination').value;

            //     if (start == '') {
            //         start = center;
            //     }

            //     var request = {
            //         origin: start,
            //         destination: destination,
            //         travelMode: google.maps.DirectionsTravelMode.DRIVING
            //     };
            //     directionsService.route(request, function (response, status) {
            //         if (status == google.maps.DirectionsStatus.OK) {
            //             directionsDisplay.setDirections(response);
            //         }
            //     });
            // }
        </script>
    </head>

    <body onload="init();">
        <form id="services" method="POST" action="patientsIndex.php">
            <h3>Updating Your Location</h3>
            <span>Location:</span><input name="location" type="text" id="start" value="SJT, Vellore Institute of Technology, Tiruvalam Rd, Katpadi" readonly/>
            <span>City:</span><input name="city" type="text" id="city" value="Katpadi" readonly/>
            <span>State:</span><input name="state" type="text" id="state" value="Tamil Nadu" readonly/>
            <span>Destination:</span><select name="destination" id="destination" onchange="return gethos()">
            <option>Chettinad Health Centre-1.1 km - 5 mins</option>
            <option>Naruvi Hospital-7.6 km - 16 mins</option>
        </select>
            <input type="text" style="display:none" readonly id="hos" name="hos" />
            <button type="submit">Enter Patient Details</button>
        </form>
        <section id="sidebar">
            <div id="directions_panel"></div>
        </section>
        <section id="main">
            <div id="map_canvas" style="width: 70%; height: 500px;"></div>
        </section>
    </body>

    </html>
<?php } ?>