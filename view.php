﻿<!DOCTYPE html>
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
        alert("Hvala na izvještaju!");
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
    
    $con=mysqli_connect("127.0.0.1","root","root","baza");
    mysqli_query($con, "SET NAMES 'utf8'");
    $result = mysqli_query($con, "SELECT * FROM sastanak WHERE rbrSastanak ='" . $_GET["id"] . "'");

    $row = mysqli_fetch_array($result);

    // VRAĆANJE VREMENA IZ SQL STANDARDA U HRVATSKI
    
    $time = explode(":",$row['vrijeme']);
    $vrijeme = $time[0] . ":" . $time[1];
    $date = explode("-",$row['datum']);
    $datum = $date[2] . "." . $date[1] . "." . $date[0] . ".";
    $var = $row['datum'] . " " . $row['vrijeme'] . ".0";
    
    // PROVJERA JE LI MOGUĆE EDITIRANJE (SASTANAK PROŠAO, ALI NE PRIJE VIŠE OD 2 TJEDNA)        
    $editable = false;
    if (((time()-(30*24*60*60)) < strtotime($var))&&(strtotime($var)<time())&&(in_array($row['rbrVrsta'],$pravoSaziv))) {  
        $editable = true; //30 dana, 24 sata, 60 minuta, 60 sekundi od danas
    }
    
    //POPIS NEPROMIJENJIVIH DETALJA
    
    // IME
    echo "Naziv događaja: " . $row['ime'];
    
    // VRSTA
    $result2 = mysqli_query($con, "SELECT ime FROM vrsta WHERE rbrVrsta =" . $row["rbrVrsta"]);
    if($row2 = mysqli_fetch_array($result2)) {
        echo "<br /><br />Vrsta: " . $row2['ime'];
    }
    // MJESNI OGRANAK - AKO AKCIJA NIJE NA GRADSKOJ NI NA MJESNOJ RAZINI - OVO <5 JE HARDKODIRANO
    
    if ($row["rbrVrsta"] < 5) {
        $result2 = mysqli_query($con, "SELECT ime FROM ogranak WHERE rbrOgranak =" . $row["rbrOgranak"]);
        if($row2 = mysqli_fetch_array($result2)) {
            echo "<br />Mjesni ogranak: " . $row2['ime'];
        }
    }
    
    // MJESNA ORGANIZACIJA - AKO AKCIJA NIJE NA GRADSKOJ RAZINI - OVO <10 JE HARDKODIRANO
    else if ($row["rbrVrsta"] < 9) {
        $result2 = mysqli_query($con, "SELECT ime FROM organizacija WHERE rbrOrganizacija =" . $row["rbrOrganizacija"]);
        if($row2 = mysqli_fetch_array($result2)) {
            echo "<br />Mjesna organizacija: " . $row2['ime'];
        }
    }
    

    // OSTALE NEPROMJENJIVE INFORMACIJE:
    
    echo "<br /><br />Opis / Dnevni red: " . $row['opis'];
    echo "<br /><br />Datum održavanja: " . $datum;
    echo "<br />Vrijeme održavanja: " . $vrijeme;
    echo '<br />Mjesto održavanja: <a href="http://maps.google.com/?q=' . $row['mjesto'] . ' Zagreb, Croatia" target="_blank">'. $row['mjesto'] . "</a><br />";
    
    // PROMJENJIVE INFORMACIJE
    
    $gosti = $row['gosti'];
    $zakljucak = $row['zakljucak'];
    $pitanje = $row['pitanje'];
    $odgovor = $row['odgovor'];
    $rbrVrsta = $row['rbrVrsta'];
    $masovanSastanak = $row['masovanSastanak'];
    $zapisnikOdobren = $row['zapisnikOdobren'];

    // FORMA ZA PROMJENU
    
    echo '<form name="view" action="edit.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">';
    echo  '<input type="hidden" name="id" value=' . $_GET["id"] . '>';
    
    // POPIS PRISUTNIH
    
    echo '<br />Popis prisutnih:<br />';

    $result2 = mysqli_query($con, "SELECT razina, rbrFunkcija FROM pozvani WHERE rbrVrsta =" . $row["rbrVrsta"]);
    
    $rbrClanPozvan = array();
    
    while ($row2 = mysqli_fetch_array($result2)) {
        if ($row2['razina'] == 3) {
            // SVI POZVANI IZ CIJELE GRADSKE ORGANIZACIJE, PO FUNKCIJI...
            $result3 = mysqli_query($con, "SELECT rbrClan, ime, prezime FROM clan WHERE rbrClan IN (select rbrClan FROM clanfunkcija WHERE
            datumPocetka < '" . date('Y-m-d') . "' AND datumKraja > '" . date('Y-m-d') . "' AND rbrFunkcija = " . $row2["rbrFunkcija"] . ")");
        }
        if ($row2['razina'] == 2) {
            // SVI POZVANI IZ MJESNIH OGRANAKA ZADANE MJESNE ORGANIZACIJE, PO FUNKCIJI...
            $result3 = mysqli_query($con, "SELECT rbrClan, ime, prezime FROM clan WHERE rbrOgranak IN 
            (SELECT rbrOgranak FROM ogranak WHERE rbrOrganizacija = " . $row['rbrOrganizacija'] . ") AND rbrClan IN (select rbrClan FROM clanfunkcija WHERE 
            datumPocetka < '" . date('Y-m-d') . "' AND datumKraja > '" . date('Y-m-d') . "' AND rbrFunkcija = " . $row2["rbrFunkcija"] . ")");
        }
        if ($row2['razina'] == 1) {
            // SVI POZVANI IZ ZADANOG MJESNOG OGRANKA, PO FUNKCIJI...
            $result3 = mysqli_query($con, "SELECT rbrClan, ime, prezime FROM clan WHERE rbrOgranak = " . $row['rbrOgranak'] . " AND rbrClan IN 
            (select rbrClan FROM clanfunkcija WHERE datumPocetka < '" . date('Y-m-d') . "' AND datumKraja > '" . date('Y-m-d') . "' AND rbrFunkcija = " . $row2["rbrFunkcija"] . ")");
        }
        while ($row3 = mysqli_fetch_array($result3)) {
            $rbrClanPozvan[] = $row3['rbrClan'];
        }
    }
    $rbrClanPozvan = array_unique($rbrClanPozvan);
        
    // STVARANJE LISTE SVIH VEĆ POKVAČENIH
    $listaPokvacenihClanova = array();
    $result4 = mysqli_query($con, "SELECT rbrClan, ime, prezime FROM clan WHERE rbrClan IN (SELECT rbrClan FROM clansastanak WHERE rbrSastanak = " . $row["rbrSastanak"] . ")");
    $listaClanova = array();
    while($row4 = mysqli_fetch_array($result4)) {
      $listaPokvacenihClanova[] = $row4['rbrClan'];
    }
    
    
    if ($editable) {
    
        // SASTANAK NIJE ZAKLJUČAN, MOGU SE POKVAČITI SVI KOJI SU BILI POZVANI, A DOŠLI SU
        $k = 0;
        foreach($rbrClanPozvan as $rbrClan) {
            echo '<input type="checkbox" id="id' . $rbrClan . '" name="prisutni[]" value="';
            echo $rbrClan . '" ';
            if (!empty($listaPokvacenihClanova)) {
                foreach($listaPokvacenihClanova as $i) {
                    if ($i == $rbrClan) {
                        echo "checked";
                    }
                }
            }
            echo "/>";
            echo '<label for="id' . $rbrClan . '">';
            $result5 = mysqli_query($con, "SELECT ime, prezime FROM clan WHERE rbrClan = " . $rbrClan);
            if ($row5 = mysqli_fetch_array($result5)) {
                echo $row5['ime'] . " " . $row5['prezime'];
            }
            else {
                echo "?";
            }
            echo "</label><br />";
            $k++;
      }
      
      // AKO SE NIJE EVIDENTIRALO PRISUSTVO:
      
      echo '
      <br />
      <input type="checkbox" id="masovanSastanak" name="masovanSastanak" value=1 ';
      if ($masovanSastanak) echo "checked";
      echo '></input>
      <label for="masovanSastanak">Prisustvo se nije evidentiralo / popis prisutnih je nepotpun</label>
      <br />';
    }
    else {
    
        // SASTANAK JE ZAKLJUČAN, MOŽE SE SAMO PROČITATI TKO JE DOŠAO IZ listaPokvacenihClanova
        
        if ($masovan) {
            echo 'Prisustvo se nije evidentiralo / popis prisutnih je nepotpun.';
        }
        else {
            if (empty($listaPokvacenihClanova)) {
                echo 'Nije evidentiran nijedan član.<br />';
            }
            else {
                while($row4 = mysqli_fetch_array($result4)) {
                    echo $row4['ime'] . " " . $row4['prezime'] . "<br />";
                }
            }
        }
    }
    
    // IZGRADNJA PLACEHOLDERA - STANDARDIZIRANO AKO JE NULL, INAČE ONO ŠTO PIŠE U BAZI
    
    if ($pitanje == NULL) {
    $placeholderPitanja = "Ovdje zapišite ako imate pitanja upućena višoj instanci";
    }
    else {
    $placeholderPitanja = $pitanje;
    }
    if ($odgovor == NULL) {
    $placeholderOdgovor = "Prikazat će se ovdje kada budu unešeni.";
    }
    else {
    $placeholderOdgovor = $odgovor;
    }
    if ($zakljucak == NULL ) {
    $placeholderZakljucak = "Ovdje zapišite donesene zaključke.";
    }
    else {
    $placeholderZakljucak = $zakljucak;
    }
    if ($gosti == NULL ) {
    $placeholderGosti = "Ovdje upišite sve ostale sudionike događaja.";
    }
    else {
    $placeholderGosti = $gosti;
    }

    // PROMJENJIVI DETALJI

    if ($editable) {
    
        // GOSTI
        
        echo '
        <br />
        <label for="gosti">Gosti:</label>
        <br />
        <textarea id="gosti" name="gosti" style="width:500px; height:50px;" placeholder="' . $placeholderGosti . '" ></textarea>
        <br />';
        
        // PITANJA
        
        echo '
        <br />
        <label for="pitanje">Pitanja višoj instanci:</label>
        <br />
        <textarea id="pitanje" name="pitanje" style="width:500px; height:50px;" placeholder="' . $placeholderPitanja . '" ></textarea>
        <br />';
    }
       // ODGOVOR
       $answerable = (((time()-(14*24*60*60)) < strtotime($var))&&(strtotime($var)<time())&&(in_array($row['rbrVrsta'],$pravoOdgovor)));
        if ($answerable) {
            echo '
            <br />
            <label for="odgovor">Odgovori više instance:</label>
            <br />
            <textarea id="odgovor" name="odgovor" style="width:500px; height:50px;" placeholder="' . $placeholderOdgovor . '" ></textarea>
            <br />';
        }
        else {
            echo '
            <br />
            <label for="odgovor">Odgovori više instance:</label>
            <br /><br /> ' .
            $placeholderOdgovor . '
            <br /><br />';
        }
        
    if ($editable) {
        // ZAKLJUČAK
        
        echo '
        <label for="zakljucak">Zaključak:</label>
        <br />
        <textarea id="zakljucak" name="zakljucak" style="width:500px; height:50px;" placeholder="' . $placeholderZakljucak . '"></textarea>
        <br />
        <br />';
        
        // ZAPISNIK
        
        echo '
        <label for="zapisnik">Zapisnik: </label>
        <input type="file" id="zapisnik" name="zapisnik" />
        <br />
        <br />';
        
        // ZAPISNIK ODOBREN
        
        echo '
        <input type="checkbox" id="zapisnikOdobren" name="zapisnikOdobren" value=1 ';
        if ($zapisnikOdobren) echo "checked";
        echo '
        />
        <label for="zapisnikOdobren">Zapisnik odobren na idućem sastanku.</label>
        <br />';
        
    }
    
    // AKO JE PROŠAO ROK ZA PROMJENU
    
    // GOSTI
    
    else {
        if ($gosti == NULL) {
            echo 'Gosti nisu evidentirani.<br />';
        }
        else {
            echo 'Gosti: ' . $gosti;
        }
        
        // PITANJA
        
        if ($pitanje == NULL) {
            echo 'Nije bilo pitanja višoj instanci.<br />';
        }
        else {
            echo 'Pitanja višoj instanci: ' . $pitanje . '<br />';
        }
        
        // PITANJA
        
        if ($odgovor == NULL) {
            echo 'Još nema odgovora od više instance.<br />';
        }
        else {
            echo 'Odgovor od više instance: ' . $odgovor . '<br />';
        }
        
        // ZAKLJUČAK
        
        if ($zakljucak == NULL) {
            echo 'Zaključak nije evidentiran.<br />';
        }
        else {
            echo 'Zaključak: ' . $zakljucak;
        }
        
        // ZAPISNIK
        
        if ($zapisnikOdobren) {
            echo 'Zapisnik je odobren.<br />';
        }
        else {
            echo 'Zapisnik nije odobren.<br />';
        }
    }

    // AKO SE MOŽE SPREMITI, IMA GUMB SPREMI
    if ($editable||$answerable) {
    echo '
    <br />
    <input type="submit" value="Spremi" class="btn btn-large btn-block btn-danger" style="width:500px;"/>';
    }
    echo '
    </form>
    <br />';  
    if ($_GET["q"] == 1) {
    echo "Zapisnik nije dostupan.";
    }
    else {
    echo '<a href="preuzmi.php?id=' . $_GET["id"] . '" class="btn btn-large btn-block btn-danger">Preuzmi zapisnik sa ovog sastanka</a><br />';
    }
    echo '<br />
    <br />';
    ?>
  <footer>
   <a href="home.php" class="btn btn-large btn-block btn-danger">Natrag</a> 
   <a href="logout.php" class="btn btn-large btn-block btn-danger">Izlaz iz programa</a><br /><br /><br />
  </footer>
 </body>
</html>