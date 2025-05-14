<?php
require_once("db_config.php");

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options); 
} catch(PDOException $e) {
    error_log("DB Error: " . $e->getMessage());
    echo json_encode(["status" => "error", "message" => "DB-Verbindung fehlgeschlagen"]);
    exit;
}

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

// Daten prüfen
$feuchtigkeit = $input["feuchtigkeit"] ?? null;
$licht = $input["licht"] ?? null;

if ($feuchtigkeit !== null && $licht !== null) {
    $sql = "INSERT INTO messwerte (feuchtigkeit, licht) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$feuchtigkeit, $licht]);
    echo json_encode(["status" => "ok", "message" => "Messwert gespeichert"]);
} else {
    echo json_encode(["status" => "error", "message" => "Feuchtigkeit oder Licht fehlt"]);
}
?>
