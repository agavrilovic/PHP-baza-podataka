﻿<?php

error_reporting(E_ALL ^ E_NOTICE);

function login() {
    global $_SESSION, $USERS;
    $con=mysqli_connect("127.0.0.1","root","root","baza");
    mysqli_query($con, "SET NAMES 'utf8'");
    if (mysqli_connect_errno()) {
        echo "Greška pri spajanju s bazom podataka: " . mysqli_connect_error();
    }
    $result = mysqli_query($con,"SELECT username, password FROM clan");
    while ($row = mysqli_fetch_array($result)) {
        $USERS[$row['username']] = $row['password'];
    }
    if ($_POST["ac"]=="log") {
        if ($USERS[$_POST["username"]]==$_POST["password"]) {
              $_SESSION["logged"]=$_POST["username"];
         } else {
              echo '<div id="krivalozinka">Kriva lozinka.</div>';
         };
     }
     if (array_key_exists($_SESSION["logged"],$USERS)) {
        header("Location: home.php");
    }
};

function check_logged(){
    global $_SESSION, $USERS;
    $con=mysqli_connect("127.0.0.1","root","root","baza");
    mysqli_query($con, "SET NAMES 'utf8'");
    if (mysqli_connect_errno()) {
        echo "Greška pri spajanju s bazom podataka: " . mysqli_connect_error();
    }
    $result = mysqli_query($con,"SELECT username, password FROM clan");
    while ($row = mysqli_fetch_array($result)) {
        $USERS[$row['username']] = $row['password'];
    }
    if (!array_key_exists($_SESSION["logged"],$USERS)) {
         header("Location: login.php");
    }
};

function check_pravo(){
    $con=mysqli_connect("127.0.0.1","root","root","baza");
    mysqli_query($con, "SET NAMES 'utf8'");
    global $pravoSaziv;
    global $pravoPregled;
    global $pravoOdgovor;
    global $Admin;
    $pravoSaziv = array();
    $pravoPregled = array();
    $pravoOdgovor = array();
    $result = mysqli_query($con,"SELECT rbrVrsta,saziv,pregled,odgovor FROM prava WHERE rbrFunkcija IN (SELECT rbrFunkcija FROM clanfunkcija WHERE datumPocetka < '" . date('Y-m-d') . "' AND datumKraja > '" . date('Y-m-d') . "' AND rbrClan = (SELECT rbrClan FROM clan WHERE username ='" . $_SESSION["logged"] . "'))");
    while ($row = mysqli_fetch_array($result)) {
        if ($row['saziv'] == 1) {
            $pravoSaziv[] = $row['rbrVrsta'];
        }
        if ($row['pregled'] == 1) {
            $pravoPregled[] = $row['rbrVrsta'];
        }
        if ($row['odgovor'] == 1) {
            $pravoOdgovor[] = $row['rbrVrsta'];
        }
    }
    $pravoSaziv = array_unique($pravoSaziv);
    $pravoPregled = array_unique($pravoPregled);
    $pravoOdgovor = array_unique($pravoOdgovor);
    $sql = "SELECT * FROM clanfunkcija WHERE rbrFunkcija = 0 AND datumPocetka < '" . date('Y-m-d') . "' AND datumKraja > '" . date('Y-m-d') . "' AND rbrClan = (SELECT rbrClan FROM clan WHERE username ='" . $_SESSION["logged"] . "')";
    $result = mysqli_query($con,$sql);
    if ($row = mysqli_fetch_array($result)) {
        $Admin = true;
    }
    else {
        $Admin = false;
    }
    // ONEMOGUĆI PREGLEDAVANJE SASTANAKA BEZ PRIVILEGIJA PROMJENOM VARIJABLE
    if ($_GET['id'] != 0 && $Admin == false) {
        $result = mysqli_query($con,"SELECT rbrVrsta FROM sastanak WHERE rbrSastanak = " . $_GET['id']);
        $row = mysqli_fetch_array($result);
        if (!in_array($row['rbrVrsta'],$pravoPregled)) {
            header("Location: home.php");
        }
    }
};

?>
