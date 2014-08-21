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
     var x=document.forms["new"]["datump"].value;
     if (x.match(/^(0[1-9]|[1-2][0-9]|3[0-1])\.(0[1-9]|1[0-2])\.[0-9]{4}\.$/)==null) {
      alert("Datum mora biti u obliku poput ovog: 01.02.2013.");
      return false;
     }
    }
    (function (exports) {
    function valOrFunction(val, ctx, args) {
        if (typeof val == "function") {
            return val.apply(ctx, args);
        } else {
            return val;
        }
    }
    function InvalidInputHelper(input, options) {
        input.setCustomValidity(valOrFunction(options.defaultText, window, [input]));

        function changeOrInput() {
            if (input.value == "") {
                input.setCustomValidity(valOrFunction(options.emptyText, window, [input]));
            } else {
                input.setCustomValidity("");
            }
        }

        function invalid() {
            if (input.value == "") {
                input.setCustomValidity(valOrFunction(options.emptyText, window, [input]));
            } else {
               console.log("INVALID!"); input.setCustomValidity(valOrFunction(options.invalidText, window, [input]));
            }
        }

        input.addEventListener("change", changeOrInput);
        input.addEventListener("input", changeOrInput);
        input.addEventListener("invalid", invalid);
    }
    exports.InvalidInputHelper = InvalidInputHelper;
})(window);
    </script>
 </head>
<body><div id="login-box"><div id="box1"><div id="crveni-box">
  
   <h1>Baza Gradske organizacije SDP Zagreb</h1>
  <div id="box2" />
  <h2>Dodaj funkciju članu</h2>
  <?php
  //  $funkcije[] = $_POST["funkcije"];
    session_start();
    include("passwords.php");
    check_logged();
    check_pravo();
    echo "<div id='korisnik'>Korisnik: " . $_SESSION["logged"] . "</div>";
    
    $con=mysqli_connect("127.0.0.1","root","root","baza");
    mysqli_query($con, "SET NAMES 'utf8'");
    // UPIT ZA OGRANKOM
    
    $sql = "SELECT * FROM clan WHERE rbrClan = ";
    $sql = $sql . $_GET["id"];
    $result = mysqli_query($con,$sql);
    
    if ($row = mysqli_fetch_array($result)) {
        echo $row['ime'] . " " . $row['prezime'] . " s brojem iskaznice " . $row['rbrClan'] . ".<br />";
        $ogranak = $row['rbrOgranak'];
    }
    //UPIT ZA ORGANIZACIJOM */
    $result = mysqli_query($con,"SELECT rbrOrganizacija FROM ogranak WHERE rbrOgranak = " . $ogranak);
    if ($row = mysqli_fetch_array($result)) {
        $organizacija = $row['rbrOrganizacija'];
    }
    echo '<form name="new" action="addmandate.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">';

    echo '<br /><label for="funckija">Funkcija: </label>';
    
    echo '<select name="funkcija" class="select-block mbl btn-block" id="funkcija" style="width:252px" required>';
    
    // UPIT ZA FUNKCIJAMA
    $result = mysqli_query($con,"SELECT * FROM funkcija");
    while ($row = mysqli_fetch_array($result)) {
        echo '<option value="' . $row['rbrFunkcija'] . '">';
        echo $row['ime'];
        echo '</option>';
        }
    ?>
  </select>
  <br />

  <label for="datump">Početak obnašanja funkcije:</label>
  <input type="hidden" name="id" value="<?php echo $_GET["id"]; ?>" />
  <input type="hidden" name="ogranak" value="<?php echo $ogranak; ?>" />
  <input type="hidden" name="organizacija" value="<?php echo $organizacija; ?>" />
  <input type="date" name="datump" id="datump" style="width:121px;" placeholder="01.01.2014." required />
  <br /><br />
  <input type="submit" value="Dodaj članu funkciju" class="btn btn-large btn-block btn-danger" style="width:320px;"/>
  </form>
  <br />
  <a href="home.php" class="btn btn-large btn-block btn-danger">Natrag</a><br /><br /><br />
  <footer>
   <a href="logout.php" class="btn btn-large btn-block btn-danger">Izlaz iz programa</a><br /><br />
  </footer>
 </body>
 <script>
 InvalidInputHelper(document.getElementById("datump"), {
    defaultText: "Upiši datum početka obnašanja funkcije.",
    emptyText: "Upiši datum početka obnašanja funkcije.",
    invalidText: function (input) {
        return 'Ne možeš staviti "' + input.value + '" kao datum početka obnašanja funkcije.';
    }
});
 </script>
</html>