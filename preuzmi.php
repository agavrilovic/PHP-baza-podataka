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
    
    //Nađi file koji odgovara rbrSastanak
    $files = scandir(getcwd() . "\\zapisnici");
    $k = 0;
    $found = false;
    foreach ($files as $f) {
        $k++;
        if ($k>2) {
            $naziv = explode(".", $f);
            if ($_GET['id'] == $naziv[0]) {
                $nastavak = $naziv[1];
                header('Content-type: application/' . $nastavak);
                $name = "zapisnik" . "." . $nastavak;
                header('Content-Disposition: attachment; filename=' . $name);
                readfile($f);
                $found = true;
            }
        }
    }
    if ($found == false) {
        header("Location: view.php?q=1&id=" . $_GET['id']);
    }
    ?>
    <a href="home.php" class="btn btn-large btn-block btn-danger">Natrag</a>
 </body>
</html>