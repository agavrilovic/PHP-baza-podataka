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

    // NOVI SASTANAK
    if ($Admin == true) {
        echo '<a href="members.php" class="btn btn-large btn-block btn-danger">Popis članova</a><br /><br /><br />';        
        echo '<a href="member.php" class="btn btn-large btn-block btn-danger">Novi član</a><br /><br /><br />';
        //echo '<a href="import.php" class="btn btn-large btn-block btn-danger">Dodaj iz CSV-a</a><br /><br /><br />';        
    }
    
    // NOVI SASTANAK
    if (!empty($pravoSaziv)) {
        echo '<a href="new.php" class="btn btn-large btn-block btn-danger">Novi sastanak / akcija</a><br /><br /><br />';
    }
    
    // UPITI
    $Upiti = array();
    foreach ($pravoOdgovor as $p) {
        $r = mysqli_query($con,"SELECT rbrSastanak FROM sastanak WHERE rbrVrsta = " . $p . " AND pitanje IS NOT NULL AND odgovor IS NULL LIMIT 5");
        while ($row = mysqli_fetch_array($r)) {
            $Upiti[] = $row['rbrSastanak'];
        }
    }   
    if (empty($Upiti)) {
        echo "";
    }
    else {
    
    echo "<h3>Gotovi sastanci koji imaju upite za vas:</h3>";
     
        echo "<ol>";
        foreach ($Upiti as $u) {
            $r = mysqli_query($con,"SELECT * FROM sastanak WHERE rbrSastanak = " . $u);
            $row = mysqli_fetch_array($r);
            echo "<li>";
            echo '<a href="view.php?id=' . $u . '">';
            echo $row['ime'] . '</a>';
            $time = explode(":",$row['vrijeme']);
            $vrijeme = $time[0] . ":" . $time[1];
            $date = explode("-",$row['datum']);
            $datum = $date[2] . "." . $date[1] . "." . $date[0] . ".";
            echo "- datuma " . $datum . " u " . $vrijeme . " sati na lokaciji " . $row['mjesto'] . ".";
            echo "</li>";
        }
        echo "</ol>";
    }
    
    // LIMIT NA BROJ SASTANAKA KOJI SE PREGLEDAVAJU
    $q = $_GET["q"];
    if (!$_GET["q"]) {
        $limit = "LIMIT 20";
    }
    else {
        $limit = "";
    }

    // PREGLED
    $Pregled = array();
        $k = 0;
    foreach ($pravoPregled as $p) {
        $r = mysqli_query($con,"SELECT rbrSastanak FROM sastanak WHERE rbrVrsta = " . $p);
        while ($row = mysqli_fetch_array($r)) {
            $Pregled[] = $row['rbrSastanak'];
        }
    }
    
    if (empty($Pregled)) {
        echo "";
    }
    else {    
    echo "<h3>Dogadaji koje mozete pregledavati:</h3>";


    $sqlArray = '(' . join(',', $Pregled) . ')';
    $r = mysqli_query($con,"SELECT * FROM sastanak WHERE rbrSastanak IN " . $sqlArray . " ORDER BY datum DESC " . $limit);
    echo "<ol>";
    while ($row = mysqli_fetch_array($r)) {
        $k += 1;
        echo "<li>";
        echo '<a href="view.php?id=' . $row['rbrSastanak'] . '">';
        echo $row['ime'] . '</a>';
        $time = explode(":",$row['vrijeme']);
        $vrijeme = $time[0] . ":" . $time[1];
        $date = explode("-",$row['datum']);
        $datum = $date[2] . "." . $date[1] . "." . $date[0] . ".";
        echo "- datuma " . $datum . " u " . $vrijeme . " sati na lokaciji " .$row['mjesto'] . ".";
        $datetime = $row['datum'] . " " . $row['vrijeme'] . ".0";
        if ((time()-(14*24*60*60)) < strtotime($datetime)) {
            if(strtotime($datetime) < time()) {
                //SASTANAK OTKLJUČAN ZA IZMJENE
                echo '<img src="icons/unlocked.png" />';
                if (in_array($row['rbrVrsta'],$pravoSaziv)) {
                    echo '<img src="icons/edit.png" />';
                }
            }
            else {
                //SASTANAK ZAKLJUČAN ZA IZMJENE
                echo '<img src="icons/future.png" />';
            }
        }
        else {
            //SASTANAK SE JOŠ NIJE DOGODIO
            echo '<img src="icons/locked.png" />';
        }
        echo "</li>";
    }
    echo "</ol>";
    }
    // DA LI DA BUDE LIMIT NA SAMO 20 DOGAĐAJA ILI DA IZLISTA SVE
    if (!$_GET["q"]) {
        if ($k == 20) {
            echo 'Trenutno se prikazuju prvih 20 događaja.<br /><br /><br />';
            echo '<p><a href="home.php?q=1" class="btn btn-large btn-block btn-danger">Prikaži sve događaje</a></p><br /><br />';
        }
    }
    else {
        echo 'Trenutno se prikazuju svi dogadaji.<br />';
        echo '<p><a href="home.php?q=0" class="btn btn-large btn-block btn-danger">Prikaži samo prvih 20</a></p><br /><br />';
    }
    if ($k > 10) {
        echo '<a href="#" class="btn btn-large btn-block btn-danger">Povratak na vrh stranice.</a><br /><br />'; 
    }
    
    ?>
 <footer>
  <p><a href="logout.php" class="btn btn-large btn-block btn-danger">Izlaz iz programa</a></p><br /><br />
 </footer>
 </body>
</html>