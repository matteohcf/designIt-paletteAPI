<?php
include_once("config.php");
include_once("verifyTokenJWT.php");

// Funzione per ottenere l'header Authorization
function getAuthorizationHeader() {
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { // Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
}

// Connessione al database
$connessione = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($connessione->connect_error) {
    $response = array(
        "status" => "error",
        "message" => "Errore di connessione al database"
    );
} else {
    // Verifica del token
    $token = getAuthorizationHeader();
    if ($token) {
        $token = trim(str_replace('Bearer', '', $token));
    } else {
        http_response_code(401);
        echo json_encode(array("message" => "Missing Authorization Header"));
        exit;
    }

    // Verify the token using the function
    $decodedToken = verifyToken($token);

    // Handle invalid token
    if ($decodedToken === false) {
        http_response_code(401);
        echo json_encode(array("message" => "Invalid Token"));
        exit;
    }

    // Ottieni l'ID utente dal token decodificato
    $creating_user_id = $decodedToken['id_utente'] ?? null;

    if ($creating_user_id === null) {
        http_response_code(401);
        echo json_encode(array("message" => "Invalid Token"));
        exit;
    }

    // Ottieni i dati dal post
    $data = json_decode(file_get_contents("php://input"), true);
    $color1 = isset($data['color1']) ? $data['color1'] : null;
    $color2 = isset($data['color2']) ? $data['color2'] : null;
    $color3 = isset($data['color3']) ? $data['color3'] : null;
    $color4 = isset($data['color4']) ? $data['color4'] : null;

    // Verifica dei dati
    if (empty($color1) || empty($color2) || empty($color3) || empty($color4)) {
        $response = array(
            "status" => "error",
            "message" => "Tutti i colori sono obbligatori"
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
?>
