<?php

session_start();
if (isset($_SESSION["uid"])) {
  $uid = $_SESSION["uid"];
}

if (
  isset($_POST["login"]) && !empty($_POST["uid"])
  && !empty($_POST["pwd"])
) {
  $uid = $_POST['uid'];
  $pwd = $_POST['pwd'];

  include './config.php';
  //set table name based on local or remote connection
  if ($connection == "local") {
    $t_hospital = "hospitalservice";
  } else {
    $t_hospital = "$database.hospitalservice";
  }

  try {
    $db = new PDO("mysql:host=$host", $user, $password, $options);
    //echo "Database connected successfully <BR>";

    $sql_select = "Select * from $t_hospital where hos_username = '$uid' and hos_pwd = '$pwd'";

    $stmt = $db->prepare($sql_select);
    $stmt->execute();

    if ($rows = $stmt->fetch()) {
      $_SESSION['valid'] = TRUE;
      $_SESSION['uid'] = $uid;
      $_SESSION["pwd"] = $pwd;
      $_SESSION["isadmin"] = TRUE;
    } else {
      //echo '<script>alert("Invalid PatName or Password. Try again")</script>';
      echo '<script>alert("Invalid PatName or Password. TTTTTry again")</script>';

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
  <html>

  <head>
    <title>Register</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="./assets/css/hospital.css" />
  </head>

  <body>
    <nav>
      <h3>Webulance</h3>
      <a href="./logout.php">
        <img src="./assets/vectors/logout.svg" alt="Logout" height="20" />
        Logout
      </a>
    </nav>
    <!--<section class="main">
      <h1 class="empty">No active requests</h1>
    </section>-->
    <section class="main">
            <div class="info">
            <h1 class="empty">No active requests</h1>
            </div>
        </section>

    <script>

      /*window.onload = function() {
                    var hos = 'Chettinad Health Centre';

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
                    makeRequest("get_status.php?q=" + hos , function(data) {
                            var data = JSON.parse(data.responseText);
                            const emptyHeader = document.querySelector('.info')
                            if (data !== null) emptyHeader.remove()
                            const docElem = document.querySelector('.main')
                            data.forEach(function(row) {
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
                                            <span class="bold">Patient Name:</span>
                                            <span>${data.PatientName}</span>
                                            </div>
                                            <div class="field">
                                            <span class="bold">Mobile Number:</span>
                                            <span>${data.PatientMob}</span>
                                            </div>
                                        </div>
                                        <div class="bottom-row">
                                            <div class="field">
                                            <span class="bold">Emergency:</span>
                                            <span>${data.Type}</span>
                                            </div>
                                            <div class="field">
                                            <button id="executeQueryButton">Delete</button>
                                            </div>
                                        </div>
                                        </div>`
                            )
                            docElem.classList.add('modify')
                            })
                            
                        });
                        
                            
    };*/
    window.onload = function() {
    var hos = 'Chettinad Health Centre';

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

    function deleteCard(cardId) {
        var request;
        if (window.XMLHttpRequest) {
            request = new XMLHttpRequest(); // IE7+, Firefox, Chrome, Opera, Safari
        } else {
            request = new ActiveXObject("Microsoft.XMLHTTP"); // IE6, IE5
        }
        request.onreadystatechange = function() {
            if (request.readyState == 4 && request.status == 200) {
                var card = document.getElementById(cardId);
                card.parentNode.removeChild(card);
            }
        }
        request.open("GET", "delete_card.php?q=" + cardId, true);
        request.send();
    }

    makeRequest("get_status.php?q=" + hos , function(data) {
        var data = JSON.parse(data.responseText);
        const emptyHeader = document.querySelector('.info')
        if (data !== null) emptyHeader.remove()
        const docElem = document.querySelector('.main')
        data.forEach(function(row) {
            docElem.insertAdjacentHTML(
                'beforeend',
                `   <div class="card" id="card-${row.driver_name}">
                        <div class="bottom-row">
                            <div class="field">
                                <span class="bold">Driver Assigned:</span>
                                <span>${row.driver_name}</span>
                            </div>
                            <div class="field">
                                <span class="bold">Vehicle Registration:</span>
                                <span>${row.ambulance_Registration}</span>
                            </div>
                        </div>
                        <div class="bottom-row">
                            <div class="field">
                                <span class="bold">Patient Name:</span>
                                <span>${row.PatientName}</span>
                            </div>
                            <div class="field">
                                <span class="bold">Mobile Number:</span>
                                <span>${row.PatientMob}</span>
                            </div>
                        </div>
                        <div class="bottom-row">
                            <div class="field">
                                <span class="bold">Emergency:</span>
                                <span>${row.Type}</span>
                            </div>
                            <div class="field">
                                <button class="delete-button" data-card-id="${row.driver_name}">Delete</button>
                            </div>
                        </div>
                    </div>`
            )
            docElem.classList.add('modify')
        })

        // Add event listeners to delete buttons
        var deleteButtons = document.querySelectorAll('.delete-button');
        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var cardId = button.getAttribute('data-card-id');
                deleteCard(cardId);
            });
        });
    });
};

    
    </script>
    <!--<script>
      // Create a new WebSocket.
      /*console.log('about to establish web socket connection')

      var socket = new WebSocket('ws://localhost:8080')

      var Username = '<?php echo $_SESSION["uid"]; ?>';
      var message = {
        type: "message",
        text6: Username,
        text7: "Hospital"
      };

      socket.onopen = function(e) {
        console.log('Connection established!')
        socket.send(JSON.stringify(message))
      }

      // Define the*/
      

      /*socket.onmessage = function(e) {
        console.log(e.data)
        var object = JSON.parse(e.data)
        const emptyHeader = document.querySelector('.empty')
        if (emptyHeader !== null)
          emptyHeader.remove()
        const docElem = document.querySelector('.main')
        docElem.insertAdjacentHTML('beforeend', `<div class="card">
        <div class="top-row">
          <div class="field">
            <span class="bold">Type of Emergency:</span>
            <span>${object.text}</span>
          </div>
          <div class="field">
            <span class="bold">Hospital Name:</span>
            <span>${object.text10}</span>
          </div>
        </div>
        <div class="bottom-row">
          <div class="field">
            <span class="bold">Driver Assigned:</span>
            <span>${object.text2}</span>
          </div>
          <div class="field">
            <span class="bold">Vehicle Registration:</span>
            <span>${object.text3}</span>
          </div>
        </div>
        <div class="bottom-row">
          <div class="field">
            <span class="bold">User Name:</span>
            <span>${object.text4}</span>
          </div>
          <div class="field">
            <span class="bold">Mobile Number:</span>
            <span>${object.text5}</span>
          </div>
        </div>
        <div class="bottom-row">
          <div class="field">
            <span class="bold">Patient Name:</span>
            <span>${object.text9}</span>
          </div>
          <div class="field">
            <span class="bold">Location:</span>
            <span>${object.text8}</span>
          </div>
        </div>
      </div>`)
      }*/

      document.getElementById('myButton').onclick = function() {
        location.href = 'http://3007f8e97f51.ngrok.io/MapsBackUp.html'
      }
    </script>-->
  </body>

  </html>
<?php
}
?>