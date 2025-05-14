<?php
require_once("db_config.php");
header('Content-Type: application/json');

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "DB-Verbindung fehlgeschlagen"]);
    exit;
}

// Letzter Messwert
$sql = "SELECT * FROM messwerte ORDER BY timestamp DESC LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$messwert = $stmt->fetch(PDO::FETCH_ASSOC);

// Aktive Pflanze (nur eine!)
$sql = "SELECT * FROM zimmerpflanzen WHERE aktiv = TRUE LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$aktive_pflanze = $stmt->fetch(PDO::FETCH_ASSOC);

// Alle Pflanzennamen
$sql = "SELECT name FROM zimmerpflanzen ORDER BY name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$alle_pflanzen = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode([
    "messwert" => $messwert,
    "aktive_pflanze" => $aktive_pflanze,
    "alle_pflanzen" => $alle_pflanzen
]);
?>
