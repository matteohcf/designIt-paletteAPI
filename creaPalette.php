<?php
include_once("config.php");

// Abilita CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// CONNESSIONE AL DB
$connessione = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($connessione->connect_error) {
    $response = array(
        "status" => "error",
        "message" => "Errore di connessione al database"
    );
} else {
    // Recupera i dati inviati dal frontend
    $data = json_decode(file_get_contents("php://input"), true);
    $color1 = isset($data['color1']) ? $data['color1'] : null;
    $color2 = isset($data['color2']) ? $data['color2'] : null;
    $color3 = isset($data['color3']) ? $data['color3'] : null;
    $color4 = isset($data['color4']) ? $data['color4'] : null;
    $creating_user_id = isset($data['creating_user_id']) ? $data['creating_user_id'] : null;

    // Verifica che i colori e l'id dell'utente siano stati inviati
    if (empty($color1) || empty($color2) || empty($color3) || empty($color4) || empty($creating_user_id)) {
        $response = array(
            "status" => "error",
            "message" => "I colori e l'ID utente sono obbligatori"
        );
    } else {
        // Prepara la query
        $stmt = $connessione->prepare("INSERT INTO palettes (color1, color2, color3, color4, creating_user_id) VALUES (?, ?, ?, ?, ?)");

        // Associa i parametri alla query
        $stmt->bind_param("sssss", $color1, $color2, $color3, $color4, $creating_user_id);

        // Esegue la query
        if ($stmt->execute()) {
            // Successo, restituisci un messaggio di successo
            $response = array(
                "status" => "success",
                "message" => "Palette inviata con successo"
            );
        } else {
            // Errore durante l'inserimento della palette nel database
            $response = array(
                "status" => "error",
                "message" => "Errore durante l'invio della palette"
            );
        }

        // Chiudi lo statement
        $stmt->close();
    }
}

// Restituisci la risposta al frontend come JSON
header('Content-Type: application/json');
echo json_encode($response);

// Chiudi la connessione al database
$connessione->close();
