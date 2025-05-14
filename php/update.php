<?php
require_once("db_config.php");
header('Content-Type: application/json');

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "DB-Verbindung fehlgeschlagen"]);
    exit;
}

// JSON oder Formulardaten empfangen
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($input)) {
    $input = $_POST;
}

$name = $input["name"] ?? null;

if ($name !== null) {
    // 1. Alle Pflanzen auf inaktiv setzen
    $sql1 = "UPDATE zimmerpflanzen SET aktiv = FALSE";
    $pdo->prepare($sql1)->execute();

    // 2. Gewählte Pflanze aktiv setzen
    $sql2 = "UPDATE zimmerpflanzen SET aktiv = TRUE WHERE name = ?";
    $stmt = $pdo->prepare($sql2);
    $stmt->execute([$name]);

    echo json_encode(["status" => "ok", "message" => "Pflanze '$name' ist jetzt aktiv."]);
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Kein Pflanzenname übergeben"]);
}
