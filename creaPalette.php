<?php
include_once("config.php");

// Abilita CORS
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// CONNESSIONE AL DB
$connessione = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($connessione->connect_error) {
    echo "Errore di connessione: " . $connessione->connect_error;
} else {
    // Recupera i dati inviati dal frontend
    $data = json_decode(file_get_contents("php://input"), true);
    $color1 = $data['color1'];
    $color2 = $data['color2'];
    $color3 = $data['color3'];
    $color4 = $data['color4'];
    $creating_user_id = $data['creating_user_id'];

    // Verifica che username, email e password siano stati inviati
    if (empty($color1) || empty($color2) || empty($color3) || empty($color4)) {
        $response = array(
            "status" => "error",
            "message" => "I colori sono obbligatori"
        );
    } else {
        // Inserisci l'utente nel database
        $insertQuery = "INSERT INTO palettes (color1, color2, color3, color4, creating_user_id) VALUES ('$color1', '$color2', '$color3', '$color4', '$creating_user_id')";
        if ($connessione->query($insertQuery) === TRUE) {
            // Successo, restituisci un messaggio di successo
            $response = array(
                "status" => "success",
                "message" => "Colori inviati con successo"
            );
        } else {
            // Errore durante l'inserimento dell'utente nel database
            $response = array(
                "status" => "error",
                "message" => "Errore durante l'invio dei colori"
            );
        }
    }
}

// Restituisci la risposta al frontend come JSON
header('Content-Type: application/json');
echo json_encode($response);

// Chiudi la connessione al database
$connessione->close();
?>