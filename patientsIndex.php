<?php

/*session_start();
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
}*/

?>

<?php
session_start();
if (isset($_SESSION["uid"])) {
$address = $_POST['location'];
$city = $_POST['city'];
$state = $_POST['state'];
$hos = $_POST['hos'];
$hospital = $_POST['destination'];
?>
<html>

<head>
    <title>Patients Dashboard</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="./assets/css/patients.css" />
</head>

<body>
    <nav>
        <h3>Webulance</h3>
        <a href="./logout.php">
            <img src="./assets/vectors/logout.svg" alt="Logout" height="20" />
            Logout
        </a>
    </nav>
    <main class="main">
        <section class="left-pane">
            <div class="info">
                <div>
                    ① Select the <strong>Type of Emergency</strong> and the
                    <strong>Hospital</strong>.
                </div>
                <div>② Send your request.</div>
            </div>
        </section>
        <section class="right-pane">
            <div class="message-card">
                <h3>Request Ambulance</h3>
                <select name="EmergencyType" id="EmergencyType">
                    <option value="Serious Injuries">Serious Injuries</option>
                    <option value="Cardiac Arrests">Cardiac arrests</option>
                    <option value="Respiratory">Respiratory</option>
                    <option value="Diabetics">Diabetics</option>
                    <option value="Unconsciousness">Unconsciousness</option>
                    <option value="Animal Bites">Animal bites</option>
                    <option value="Infections">Infections</option>
                </select>
                <input type="text" name="patName" id="pat-name" style="padding: 8px" placeholder="Enter Patient Name"  />
                <button onclick="transmitMessage()">Send</button>

                <script>
                    // Create a new WebSocket.
                    console.log("about to establish web socket connection");

                    var socket = new WebSocket('ws://http://localhost/Webulance');

                    socket.onopen = function(e) {
                        console.log("Connection established!");
                        alert("ghgh");
                    };

                    // Define the 
                    //var HospitalName = '<?php echo $hospital; ?>';
                    //var Username = '<?php echo $_SESSION["uid"]; ?>';
                    //var Location = '<?php echo $address; ?>';
                    //var hos = '<?php echo $hos; ?>';

                    var HospitalName = 'Chettinad Health Centre';
                    var Username = 1;
                    var Location = 'Vellore Institute of Technology';
                    var hos = 1;
                    

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

                    function transmitMessage() {
                        const patName = document.querySelector('#pat-name').value;
                        
                        const tvalue = document.querySelector('#EmergencyType').value;
                        if(patName.split(/\W+/).length !== 2 || patName.length == 0) {
                            //window.alert("Please enter the full name of the patient to be admitted");
                            window.alert(hos);
                            return;
                        }
                        makeRequest("get_ambulance.php?q=" + HospitalName + "&r=" + Username + "&s=" + tvalue , function(data) {
                            var data = JSON.parse(data.responseText);
                            const emptyHeader = document.querySelector('.info')
                            if (emptyHeader !== null) emptyHeader.remove()
                            const docElem = document.querySelector('.left-pane')
                            docElem.insertAdjacentHTML(
                                'beforeend',
                                `   <div class="card">
                                        <div class="bottom-row">
                                            <div class="field">
                                            <span class="bold">Driver Assigned:</span>
                                            <span>${data.driver_name}</span>
                                            </div>
                                            <div class="field">
                                            <span class="bold">Vehicle Registration:</span>
                                            <span>${data.ambulance_Registration}</span>
                                            </div>
                                        </div>
                                        <div class="bottom-row">
                                            <div class="field">
                                            <span class="bold">User Name:</span>
                                            <span>${data.PatientName}</span>
                                            </div>
                                            <div class="field">
                                            <span class="bold">Mobile Number:</span>
                                            <span>${data.PatientMob}</span>
                                            </div>
                                        </div>
                                        <div class="bottom-row">
                                            <div class="field">
                                            <span class="bold">Admitted Patient:</span>
                                            <span>${document.getElementById('pat-name').value}</span>
                                            </div>
                                            <div class="field">
                                            <span class="bold">Hospital Name:</span>
                                            <span>${HospitalName}</span>
                                            </div>
                                        </div>
                                        </div>`
                            )
                            docElem.classList.add('modify')

                            var message = {
                                type: "message",
                                text: document.getElementById('EmergencyType').value,
                                text1: hos,
                                text2: data.driver_name,
                                text3: data.ambulance_Registration,
                                text4: data.PatientName,
                                text5: data.PatientMob,
                                text6: Username,
                                text7: "Patient",
                                text8: Location,
                                text9: document.getElementById('pat-name').value,
                                text10: '<?php echo $hospital; ?>',
                                date: Date.now()
                            };
                            socket.send(JSON.stringify(message));
                        });
                    }

                    socket.onmessage = function(e) {
                        //alert(e.data);
                    }
                </script>
</body>

</html>
<?php } else {
    unset($_SESSION["uid"]);
    unset($_SESSION["pwd"]);
    unset($_SESSION["valid"]);

    //echo 'You have cleaned session';
    //header('Refresh: 2; URL = login.php');
    //header("Refresh: 2; Loocation: ./index2.php"); 
    header("Refresh: 1; URL = index.php");
    exit();
}  ?>