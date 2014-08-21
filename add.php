﻿ <?php

session_start();
include("passwords.php");
check_logged();
check_pravo();

//PREUZIMANJE PODATAKA IZ FORME

$ime = $_POST["ime"];
$rbrVrsta = $_POST["vrsta"];
$datum = $_POST["datum"];
$vrijeme = $_POST["vrijeme"];
$mjesto = $_POST["mjesto"];
$opis = $_POST["opis"];

// PROMJENA DATUMA IZ HRVATSKOG U SQL STANDARD

$dateFields = explode(".",$datum);
$datum = $dateFields[2] . "-" . $dateFields[1] . "-" . $dateFields[0];
$vrijeme = $vrijeme . ":00";

    $con=mysqli_connect("127.0.0.1","root","root","baza");
mysqli_query($con, "SET NAMES 'utf8'");


// ID KORISNIKA
$result = mysqli_query($con,"SELECT rbrClan FROM clan WHERE username = '" . $_SESSION["logged"] . "'");
if ($row = mysqli_fetch_array($result)) {
    $rbrClan = $row['rbrClan'];
}

//OGRANAK KORISNIKA
$result = mysqli_query($con,"SELECT rbrOgranak FROM clan WHERE rbrClan = " . $rbrClan);
if ($row = mysqli_fetch_array($result)) {
    $rbrOgranak = $row['rbrOgranak'];
}

//ORGANIZACIJA KORISNIKA
$result = mysqli_query($con,"SELECT rbrOrganizacija FROM ogranak WHERE rbrOgranak = " . $rbrOgranak);
if ($row = mysqli_fetch_array($result)) {
    $rbrOrganizacija = $row['rbrOrganizacija'];
}

// NOVI BROJ SASTANKA

$result = mysqli_query($con,"SELECT MAX(rbrSastanak) AS rbrSastanak FROM sastanak");

if ($row = mysqli_fetch_array($result)) {
    $rbrSastanak = $row['rbrSastanak']+1;
}
else {
    $rbrSastanak = 1;
}

// UPIS PODATAKA U BAZU

$sql = "insert into sastanak (rbrSastanak, rbrClan, rbrVrsta, rbrOgranak, rbrOrganizacija, ime, datum, vrijeme, mjesto, opis) values (" . $rbrSastanak . ", " . $rbrClan . ", " . $rbrVrsta . ", " . $rbrOgranak. ", " . $rbrOrganizacija . ", '" . $ime . "', '" . $datum . "', '" . $vrijeme . "', '" . $mjesto . "', '" . $opis . "')";

if (!mysqli_query($con,$sql)) {
    die('Error: ' . mysqli_error($con));
}

$rbrClanPozvan = array();
$result2 = mysqli_query($con, "SELECT razina, rbrFunkcija FROM pozvani WHERE rbrVrsta =" . $rbrVrsta);
while ($row2 = mysqli_fetch_array($result2)) {
    if ($row2['razina'] == 3) {
        // SVI POZVANI IZ CIJELE GRADSKE ORGANIZACIJE, PO FUNKCIJI...
$sql = "SELECT rbrClan, ime, prezime FROM clan WHERE rbrClan IN (select rbrClan FROM clanfunkcija WHERE
        datumPocetka < '" . date('Y-m-d') . "' AND datumKraja > '" . date('Y-m-d') . "' AND rbrFunkcija = " . $row2["rbrFunkcija"] . ")";
        $result3 = mysqli_query($con, $sql);
        while ($row3 = mysqli_fetch_array($result3)) {
            $rbrClanPozvan[] = $row3['rbrClan'];
        }
    }
    else if ($row2['razina'] == 2) {
        // SVI POZVANI IZ MJESNIH OGRANAKA ZADANE MJESNE ORGANIZACIJE, PO FUNKCIJI...
$sql = "SELECT rbrClan, ime, prezime FROM clan WHERE rbrOgranak IN 
        (SELECT rbrOgranak FROM ogranak WHERE rbrOrganizacija = " . $rbrOrganizacija . ") AND rbrClan IN (select rbrClan FROM clanfunkcija WHERE 
        datumPocetka < '" . date('Y-m-d') . "' AND datumKraja > '" . date('Y-m-d') . "' AND rbrFunkcija = " . $row2["rbrFunkcija"] . ")";
        $result3 = mysqli_query($con, $sql);
        while ($row3 = mysqli_fetch_array($result3)){
            $rbrClanPozvan[] = $row3['rbrClan'];
        }
    }
    else if ($row2['razina'] == 1) {
        // SVI POZVANI IZ ZADANOG MJESNOG OGRANKA, PO FUNKCIJI...
        $result3 = mysqli_query($con, "SELECT rbrClan, ime, prezime FROM clan WHERE rbrOgranak = " . $rbrOgranak . " AND rbrClan IN 
        (select rbrClan FROM clanfunkcija WHERE datumPocetka < '" . date('Y-m-d') . "' AND datumKraja > '" . date('Y-m-d') . "' AND rbrFunkcija = " . $row2["rbrFunkcija"] . ")");
        while ($row3 = mysqli_fetch_array($result3)) {
            $rbrClanPozvan[] = $row3['rbrClan'];
        }
    }
    
}
$rbrClanPozvan = array_unique($rbrClanPozvan);

$result4 = mysqli_query($con, "SELECT ime FROM vrsta WHERE rbrVrsta = " . $rbrVrsta);
$row4 = mysqli_fetch_array($result4);
$Vrsta = $row4['ime'];

$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

foreach($rbrClanPozvan as $rbrClan) {
$sql = "SELECT ime, prezime, mail FROM clan WHERE rbrClan = " . $rbrClan;
    $result = mysqli_query($con,$sql);

    if ($row = mysqli_fetch_array($result)) {

        $message = "<html><body><div id='content'><h1>" . $row['ime'] . " " . $row['prezime'] . "!</h1><p>Dana " . $datum . " u " . $vrijeme . " sati na lokaciji " . $mjesto . " održat će se " . $Vrsta . " s nazivom " . $ime . ".</p><p>" . $opis . "</p><p>Veselimo se vašem prisustvu!</div></body></html>";

        mail($row['mail'], $ime, $message, $headers);

    }
}

header("Location: view.php?id=" . $rbrSastanak);
?>
