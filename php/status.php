<?php
require_once("db_config.php");
header('Content-Type: application/json');

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "DB-Verbindung fehlgeschlagen"]);
    exit;
}

// Letzten Messwert holen
$sql = "SELECT * FROM messwerte ORDER BY timestamp DESC LIMIT 1";
$mess = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);

// Aktive Pflanze holen
$sql = "SELECT * FROM zimmerpflanzen WHERE aktiv = TRUE LIMIT 1";
$pflanze = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);

if (!$mess || !$pflanze) {
    echo json_encode(["status" => "error", "message" => "Messwert oder Pflanze fehlt"]);
    exit;
}

$feuchtigkeit = $mess["feuchtigkeit"];
$feuchtMin = $pflanze["feuchtigkeit_min"] ?? null;

if ($feuchtMin === null) {
    echo json_encode(["status" => "error", "message" => "Feuchtigkeitsgrenze fehlt"]);
    exit;
}

// Nur prüfen, ob unter Minimum – sonst ist alles ok
$feuchtigkeit_ok = ($feuchtigkeit >= $feuchtMin);

echo json_encode(["ok" => $feuchtigkeit_ok]);
