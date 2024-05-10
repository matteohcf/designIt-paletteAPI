<?php
include_once("config.php");
include_once("verifyTokenJWT.php");

// Connessione al database
$connessione = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($connessione->connect_error) {
    $response = array(
        "status" => "error",
        "message" => "Errore di connessione al database"
    );
} else {
    /* Verifica del token */
    $headers = getallheaders();
    $token = "null";
    foreach ($headers as $name => $value) {
        if ($name === 'Authorization') {
            // Dividi il valore dell'header per ottenere solo il token
            $token = trim(str_replace('Bearer', '', $value));
            break;
        }
    }
    /* echo $token; */

    // Verify the token using the function
    $decodedToken = verifyToken($token);

    // Handle invalid token
    if ($decodedToken === false) {
    http_response_code(401);
    echo json_encode(array("message" => "Invalid Token"));
    exit;
    }
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
