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
    
    $con=mysqli_connect("127.0.0.1","root","root","baza");
    mysqli_query($con, "SET NAMES 'utf8'");
    $result = mysqli_query($con, "SELECT * FROM clan WHERE rbrClan = " . $_GET["id"]);
    if ($row = mysqli_fetch_array($result)) {
        echo "<a href='amember.php?id=" . $row['rbrClan'] . "'>" . $row['ime'] . " " . $row['prezime'] . "</a>, broj iskaznice: " . $row['rbrClan'];
        echo '<a href="brisi.php?id=' . $row['rbrClan'] . '"><img src="icons/trash.png" /></a>';
        echo '<a href="mandate.php?id=' . $row['rbrClan'] . '"><img src="icons/add.png" /></a>';
        echo "<br /><br />";
        $date = explode("-",$row['rodjen']);
        $datum = $date[2] . "." . $date[1] . "." . $date[0] . ".";
        echo 'Rođen: ' . $datum;
        echo "<br /><br />";
        echo 'Funkcije: ';
        echo "<br /><br />";
        $result2 = mysqli_query($con, "SELECT * FROM clanfunkcija WHERE rbrClan = " . $_GET["id"]);
        while ($row2 = mysqli_fetch_array($result2)) {
            $date1 = explode("-",$row2['datumPocetka']);
            $datum1 = $date1[2] . "." . $date1[1] . "." . $date1[0] . ".";
            $date2 = explode("-",$row2['datumKraja']);
            $datum2 = $date2[2] . "." . $date2[1] . "." . $date2[0] . ".";
            $result3 = mysqli_query($con, "SELECT ime FROM funkcija WHERE rbrFunkcija = " . $row2["rbrFunkcija"]);
            $row3 = mysqli_fetch_array($result3);
            echo "" . $row3["ime"] . " od " . $datum1 . " do " . $datum2 . "";
            echo "";
            echo "<br />";
        }
    }
  ?>
  <br />
  <a href="home.php" class="btn btn-large btn-block btn-danger">Natrag</a>
  <footer>
   <a href="logout.php" class="btn btn-large btn-block btn-danger">Izlaz iz programa</a>
  </footer>
 </body>
</html>