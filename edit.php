<?php
session_start();
include("passwords.php");
check_logged();
echo "<div id='korisnik'>Korisnik: " . $_SESSION["logged"] . "</div>";
check_pravo();

    $con=mysqli_connect("127.0.0.1","root","root","baza");
mysqli_query($con, "SET NAMES 'utf8'");

$sql = "select * from sastanak where rbrSastanak=" . $_POST["id"];
$result = mysqli_query($con,$sql);
if($row = mysqli_fetch_array($result)) {

    // ZAKLJUČAK
    
    if ($_POST["zakljucak"] != "") {
        $zakljucak = $_POST["zakljucak"];
    }
    else {
        $zakljucak = $row['zakljucak'];
    }
    
    // PITANJE
    
    if ($_POST["pitanje"] != "") {
        $pitanje = $_POST["pitanje"];
    }
    else {
        $pitanje = $row['pitanje'];
    }

    // ODGOVOR
    
    if ($_POST["odgovor"] != "") {
        $odgovor = $_POST["odgovor"];
    }
    else {
        $odgovor = $row['odgovor'];
    }
    
    // GOSTI
    
    if ($_POST["gosti"] != "") {
        $gosti = $_POST["gosti"];
    }
    else {
        $gosti = $row['gosti'];
    }
    
}

$m = $_POST['masovanSastanak'];
if (!$m) {
    $m = 0;
}

$z = $_POST['zapisnikOdobren'];
if (!$z) {
    $z = 0;
}

// DODAVANJE DETALJA IZ ZAPISNIKA

$sql = "update sastanak set zakljucak='" . $zakljucak . "',pitanje='" . $pitanje . "',gosti='" . $gosti . "',odgovor='" . $odgovor . "',masovanSastanak=" . $m . ",zapisnikOdobren=" . $z . " where rbrSastanak=" . $_POST['id'];

if (!mysqli_query($con,$sql)) {
    die('Error: ' . mysqli_error($con));
}

// DODAVANJE PRISUTNOSTI

if (!empty($_POST['prisutni'])) {
    foreach($_POST['prisutni'] as $i) {
        $sql = "select * from clansastanak where rbrSastanak=" . $_POST['id'] . " and rbrClan=" . $i;
        $result = mysqli_query($con,$sql);
        if($row = mysqli_fetch_array($result)) {
            //Već postoji, ne treba dodavati opet.
        }
        else {
            $sql = "insert into clansastanak (rbrSastanak, rbrClan) values ('" . $_POST['id'] . "', '" . $i . "')";
            if (!mysqli_query($con,$sql)) {
                die('Error: ' . mysqli_error($con));
            }
        }
    }
}

// BRISANJE PRISUTNOSTI

$sql = "select * from clansastanak where rbrSastanak=" . $_POST['id'];
$result = mysqli_query($con,$sql);
while($row = mysqli_fetch_array($result)) {
    $deleteMember = true;
    foreach($_POST['prisutni'] as $i) {
        if ($i==$row['rbrClan']) {
            $deleteMember = false;
        }
    }
    if ($deleteMember) {
        $sql = "DELETE FROM clansastanak WHERE rbrSastanak=" . $_POST['id'] . " AND rbrClan=" . $row['rbrClan'];
        if (!mysqli_query($con,$sql)) {
            die('Error: ' . mysqli_error($con));
        }
        
    }
}

if ($_FILES["zapisnik"]["error"] == 4) {
    echo "Još nije dodan zapisnik.";
}
else {
    $ekstenzija = end(explode(".", $_FILES["zapisnik"]["name"]));
    $zapisnik = $_POST['id'] . "." . $ekstenzija;
    move_uploaded_file($_FILES["zapisnik"]["tmp_name"],"zapisnici\\" . $zapisnik);
    if (!mysqli_query($con,$sql)) {
        die('Error: ' . mysqli_error($con));
    }
}

mysqli_close($con);
header("Location: view.php?id=" . $_POST['id']);
 ?>
