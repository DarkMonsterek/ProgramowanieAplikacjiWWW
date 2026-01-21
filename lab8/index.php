<?php
include('cfg.php');
include('showpage.php');
include('contact.php');
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Projekt 1">
    <meta name="keywords" content="HTML5, CSS3, JavaScript">
    <meta name="author" content="Przemysław Karpowicz">
    <title>Największe Budynki Świata | Summit</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <script src="js/kolorujtlo.js" type="text/javascript"></script>
    <script src="js/timedate.js" type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body bgcolor="#0d1b2a" onload="startclock()">




    <header class="main-header">
        <div class="container header-container">
            <div class="logo">
                <a href="index.php">
                    <img src="images/logo.png" alt="Summit Logo" class="logo-img">
                </a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="index.php" class="active">Strona Główna</a></li>
                    <li><a href="index.php?idp=burj_khalifa">Burj Khalifa</a></li>
                    <li><a href="index.php?idp=merdeka_118">Merdeka 118</a></li>
                    <li><a href="index.php?idp=shanghai_tower">Shanghai Tower</a></li>
                    <li><a href="index.php?idp=abraj_al_bait">Abraj Al-Bait</a></li>
                    <li><a href="index.php?idp=ping_an">Ping An Center</a></li>
                    <li><a href="index.php?idp=filmy">Filmy</a></li>
                    <li><a href="index.php?idp=kontakt">Kontakt</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <?php
    // include('showpage.php'); // Removed duplicate include
    
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

    // Domyślna strona (ID 1 - Strona Główna)
    $stronaId = null;

    if (empty($_GET['idp'])) {
        $stronaId = 1;
    } elseif ($_GET['idp'] == 'glowna') {
        $stronaId = 1;
    } elseif ($_GET['idp'] == 'burj_khalifa') {
        $stronaId = 2;
    } elseif ($_GET['idp'] == 'merdeka_118') {
        $stronaId = 3;
    } elseif ($_GET['idp'] == 'shanghai_tower') {
        $stronaId = 4;
    } elseif ($_GET['idp'] == 'abraj_al_bait') {
        $stronaId = 5;
    } elseif ($_GET['idp'] == 'ping_an') {
        $stronaId = 6;
    } elseif ($_GET['idp'] == 'filmy') {
        $stronaId = 7;
    } elseif ($_GET['idp'] == 'kontakt') {
        if (isset($_POST['wyslij_kontakt'])) {
            WyslijMailKontakt("admin@moj_projekt.pl");
        } else {
            echo PokazKontakt();
        }
    } else {
        // Jeśli nie znaleziono dopasowania, wyświetl np. stronę główną lub błąd
        $stronaId = 1;
    }

    if ($stronaId) {
        echo PokazPodstrone($stronaId);
    }
    // echo PokazPodstrone($stronaId); -> moved inside blocks or handled above
    ?>


    <footer class="main-footer">
        <div class="container footer-content">
            <div class="footer-info">
                <p>&copy; 2026 Summit - Największe Budynki Świata.</p>
                <div id="data" style="margin-top: 5px; font-size: 0.9em; color: var(--text-muted);"></div>
                <div id="zegarek" style="margin-top: 2px; font-size: 0.9em; color: var(--text-muted);"></div>
            </div>
            <div class="background-changer" style="margin-top: 20px; width: 100%; text-align: center;">
                <p style="margin-bottom: 10px; color: var(--accent-color);">Zmień kolor tła:</p>
                <FORM METHOD="POST" NAME="background">
                    <INPUT TYPE="button" VALUE="żółty" ONCLICK="changeBackground('#FFF000')">
                    <INPUT TYPE="button" VALUE="czarny" ONCLICK="changeBackground('#000000')">
                    <INPUT TYPE="button" VALUE="biały" ONCLICK="changeBackground('#FFFFFF')">
                    <INPUT TYPE="button" VALUE="zielony" ONCLICK="changeBackground('#00FF00')">
                    <INPUT TYPE="button" VALUE="niebieski" ONCLICK="changeBackground('#0000FF')">
                    <INPUT TYPE="button" VALUE="pomarańczowy" ONCLICK="changeBackground('#FF8000')">
                    <INPUT TYPE="button" VALUE="szary" ONCLICK="changeBackground('#c0c0c0')">
                    <INPUT TYPE="button" VALUE="czerwony" ONCLICK="changeBackground('#FF0000')">
                </FORM>
            </div>

            <nav class="footer-nav">
                <ul>
                    <li><a href="index.php">Strona Główna</a></li>
                    <li><a href="#ranking">Ranking</a></li>
                    <li><a href="#gallery">Galeria</a></li>
                </ul>
            </nav>
        </div>
    </footer>
    <?php
    $nr_indeksu = '175003';
    $nrGrupy = 'ISI2';
    echo 'Autor: Przemysław Karpowicz ' . $nr_indeksu . ' grupa ' . $nrGrupy . ' <br /><br />';
    ?>
</body>

</html>