<?php
/*************************************************************
 * Kapitel 12: Website2DB > Schritt 2: Form -> DB
 * db_config.php
 * Datenbank-Verbindung
 * Ersetze $db_host, $db_name, $db_user, $db_pass durch deine eigenen Daten. 
 * Lade diese Datei NICHT auf GitHub
 * Beispiel: https://fiessling.ch/im4/12_website2db/Schritt2_form_to_db/db_config.php
 * GitHub: https://github.com/Interaktive-Medien/im_physical_computing/blob/main/12_Website2DB/Schritt2_form_to_db/db_config.php_template
 *************************************************************/

$db_host = "yf07cu.myd.infomaniak.com";  // Infomaniak z. B. "rv9w2f.myd.infomaniak.com", beim FHGR Edu-Server und xampp steht hier "localhost"
$db_name = "yf07cu_giessmich";                   // Infomaniak z. B. "rv9w2f_fiessling", Edu-Server: "650665_4_1", xampp: "sensor2website"
$db_user = "yf07cu_lucatimla";               // Infomaniak z. B. "rv9w2f_fiessling", Edu-Server: "650665_4_1", xampp: "root"
$db_pass = "Kim.luca1";               // xampp: ""
$db_charset = "utf8";

$dsn = "mysql:host=$db_host;dbname=$db_name;charset=$db_charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
?>
