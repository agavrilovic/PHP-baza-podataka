<!DOCTYPE html>
<html lang="hr">
 <head>
	<meta http-equiv="Content-type" content="text/html; charset="utf-8">
        <meta name="viewport"
              content="width=device-width">
        <meta name="keywords" content="SDP" >
        <meta name="author" content="Aleksandar Gavrilovic" >
        <meta name="description" content="Baza Gradske organizacije SDP Zagreb" >
	<link rel="stylesheet" href="css/style.css" />
    <title>Baza Gradske organizacije SDP Zagreb</title>
    <script>
    function validateForm() {
     var x=document.forms["new"]["datum"].value;
     var y=document.forms["new"]["vrijeme"].value;
     if (x.match(/^(0[1-9]|[1-2][0-9]|3[0-1])\.(0[1-9]|1[0-2])\.[0-9]{4}\.$/)==null) {
      alert("Datum mora biti u obliku poput ovog: 01.02.2013.");
      return false;
     }
     var dateFields = x.split(".");
     var date = new Date(dateFields[2],dateFields[1]-1,dateFields[0]);
     if (date<Date.now()) {
        alert("Datum ne smije biti u prošlosti.");
        return false;
     }
     if (y.match(/^([0-1][0-9]|[2][0-3]):([0-5][0-9])$/)==null) {
      alert("Vrijeme mora biti u obliku poput ovog: 08:30");
      return false;
     }
    }    
    </script>
 </head>
<body><div id="login-box"><div id="box1"><div id="crveni-box">
  
   <h1>Baza Gradske organizacije SDP Zagreb</h1>
  <div id="box2" />
  <?php
    session_start();
    include("passwords.php");
    check_logged();
    echo "<div id='korisnik'>Korisnik: " . $_SESSION["logged"] . "</div>";
    
    check_pravo();
  ?>

  <form name="new" action="add.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
  <label for="ime">Naziv:</label>
  <input type="text" name="ime" style="width:261px;" id="ime" placeholder="150. plenarna sjednica" required /><br />
  <br />
  <label for="vrsta">Vrsta:</label>
  <select type="text" style="width:272px;" id="vrsta" name="vrsta" class="select-block mbl btn-block" ><br /><br />
  <?php
  
    // PRETRAGA ZA VRSTAMA SASTANAKA KOJE SMIJEŠ NAPRAVITI
        $con=mysqli_connect("127.0.0.1","root","root","baza");
    mysqli_query($con, "SET NAMES 'utf8'");
    foreach($pravoSaziv as $p) {
        $result = mysqli_query($con,"SELECT * FROM vrsta WHERE rbrVrsta = " . $p);
        $row = mysqli_fetch_array($result);
        echo '<option value=' . $row['rbrVrsta'] . '>';
        echo $row['ime'];
        echo '</option>';
        }
  ?>
  </select>
  <br />
  <label for="datum">Datum održavanja:</label>
  <input type="date" name="datum" id="datum" placeholder="01.02.2013." required /><br /><br />
  <label for="vrijeme">Vrijeme održavanja:</label>
  <input type="time" name="vrijeme" id="vrijeme" placeholder="08:30" required /><br /><br />
  <br />
  <label for="mjesto">Mjesto održavanja:</label>
  <input type="text" name="mjesto" id="mjesto" style="width:181px;" placeholder="Praška 2" required /><br /><br />
  <label for="opis">Dnevni red ili kratki opis akcije: </label>
  <br />
  <textarea name="opis" id="opis" placeholder="1. Prva točka dnevnog reda sastanka 2. Druga točka..." style="width:310px;height:310px;"required></textarea>
  <br />
  <input type="submit" value="Spremi" class="btn btn-large btn-block btn-danger" style="width:320px;"/>
  </form>
  <br />
  <footer>
  <a href="home.php" class="btn btn-large btn-block btn-danger">Natrag</a> 
  <a href="logout.php" class="btn btn-large btn-block btn-danger">Izlaz iz programa</a><br /><br /><br />
  </footer>
 </body>
</html>