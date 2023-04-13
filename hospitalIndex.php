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
    <section class="main">
      <h1 class="empty">No active requests</h1>
    </section>
    <script>
      // Create a new WebSocket.
      console.log('about to establish web socket connection')

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

      // Define the

      function transmitMessage() {
        //socket.send(JSON.stringify(message))
      }

      socket.onmessage = function(e) {
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
      }

      document.getElementById('myButton').onclick = function() {
        location.href = 'http://3007f8e97f51.ngrok.io/MapsBackUp.html'
      }
    </script>
  </body>

  </html>
<?php
}
?>