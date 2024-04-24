<?php
include_once("config.php");

// Abilita CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Connessione al database
$connessione = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($connessione->connect_error) {
    $response = array(
        "status" => "error",
        "message" => "Errore di connessione al database"
    );
} else {
    // Ottieni i dati
    $data = json_decode(file_get_contents("php://input"), true);
    $color1 = isset($data['color1']) ? $data['color1'] : null;
    $color2 = isset($data['color2']) ? $data['color2'] : null;
    $color3 = isset($data['color3']) ? $data['color3'] : null;
    $color4 = isset($data['color4']) ? $data['color4'] : null;
    $creating_user_id = isset($data['creating_user_id']) ? $data['creating_user_id'] : null;

    // Verifica dei dati
    if (empty($color1) || empty($color2) || empty($color3) || empty($color4) || empty($creating_user_id)) {
        $response = array(
            "status" => "error",
            "message" => "I colori e l'ID utente sono obbligatori"
        );
    } else {
        // Prepara query
        $stmt = $connessione->prepare("INSERT INTO palettes (color1, color2, color3, color4, creating_user_id) VALUES (?, ?, ?, ?, ?)");

        // Associa i parametri alla query
        $stmt->bind_param("sssss", $color1, $color2, $color3, $color4, $creating_user_id);

        // Esegue la query
        if ($stmt->execute()) {
            $response = array(
                "status" => "success",
                "message" => "Palette inviata con successo"
            );
        } else {
            $response = array(
                "status" => "error",
                "message" => "Errore durante l'invio della palette"
            );
        }

        $stmt->close();
    }
}

// Restituisci la risposta come JSON
header('Content-Type: application/json');
echo json_encode($response);

// Chiudi la connessione al database
$connessione->close();
