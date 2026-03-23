<?php
define('DEVEL', true);
define('DEBUG_LOG_FILE', '/var/log/caddy/arduino.log');

function log_debug($message) {
    if (defined('DEVEL') && DEVEL === true) {
        if (is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        }
        error_log(date("F j, Y, g:i a") . " - " . $message . PHP_EOL, 3, DEBUG_LOG_FILE);
    }
}

$jsonBrut = file_get_contents('php://input');
log_debug("Recu : " . $jsonBrut);

$donnees = json_decode($jsonBrut);

if (json_last_error() !== JSON_ERROR_NONE) {
    log_debug("Erreur JSON : " . json_last_error_msg());
    http_response_code(400);
    echo json_encode(["erreur" => "JSON invalide"]);
    exit;
}

$adresse_ip = $_SERVER['REMOTE_ADDR'];

$dsn  = "mysql:host=localhost;dbname=examen1;charset=utf8mb4";
$user = "examen1";                                                                                                                                                                                                                                                                         
$pass = "examen1";

try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $stmt = $pdo->prepare("INSERT INTO boutons 
        (etat_bouton_rouge, etat_bouton_jaune, adresse_ip, date_heure) 
        VALUES (:etat_bouton_rouge, :etat_bouton_jaune, :adresse_ip, NOW())");

    $stmt->execute([
        ':etat_bouton_rouge' => (bool)$donnees->bouton_rouge ? 1 : 0,
        ':etat_bouton_jaune' => (bool)$donnees->bouton_jaune ? 1 : 0,
        ':adresse_ip'        => $adresse_ip
    ]);

    log_debug("Enregistrement en BD reussi. IP : " . $adresse_ip);
    http_response_code(200);
    echo json_encode(["status" => "ok", "message" => "Enregistrement reussi"]);

} catch (Exception $exception) {
    log_debug("Erreur BD : " . $exception->getMessage());
    http_response_code(500);
    echo json_encode(["erreur" => "Erreur BD"]);
}