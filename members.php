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
    $result = mysqli_query($con, "SELECT * FROM clan");
    while ($row = mysqli_fetch_array($result)) {
        echo "<a href='amember.php?id=" . $row['rbrClan'] . "'>" . $row['ime'] . " " . $row['prezime'] . "</a>, broj iskaznice: " . $row['rbrClan'];
        echo '<a href="brisi.php?id=' . $row['rbrClan'] . '"><img src="icons/trash.png" /></a>';
        echo '<a href="mandate.php?id=' . $row['rbrClan'] . '"><img src="icons/add.png" /></a>';
        echo "<br />";
    }
  ?>
<br />
  <footer>
   <a href="home.php" class="btn btn-large btn-block btn-danger">Natrag</a> 
   <a href="logout.php" class="btn btn-large btn-block btn-danger">Izlaz iz programa</a>
  </footer>
 </body>
</html>