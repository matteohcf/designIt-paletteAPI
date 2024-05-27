<?php
include_once("config.php");
include_once("verifyTokenJWT.php");

// Funzione per ottenere l'header Authorization
function getAuthorizationHeader()
{
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
    echo "Errore di connessione: " . $connessione->connect_error;
} else {
    // Verifica token
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
    $id_utente = $decodedToken['id_utente'] ?? null;

    if ($id_utente === null) {
        http_response_code(401);
        echo json_encode(array("message" => "Invalid Token"));
        exit;
    }

    // Query per ottenere i dati delle palette salvate e a cui ha messo like l'utente
    $sql = "SELECT p.id_palette, p.color1, p.color2, p.color3, p.color4, p.likes, p.creating_user_id,
                CASE WHEN sp.id_utente IS NOT NULL THEN true ELSE false END AS isSaved,
                CASE WHEN l.id_utente IS NOT NULL THEN true ELSE false END AS isLiked
                FROM palettes p 
                LEFT JOIN save_palettes sp ON p.id_palette = sp.id_palette AND sp.id_utente = ?
                LEFT JOIN likes l ON p.id_palette = l.id_palette AND l.id_utente = ?
                WHERE sp.id_utente IS NOT NULL OR l.id_utente IS NOT NULL
                ORDER BY p.likes DESC";

    $stmt = $connessione->prepare($sql);

    if ($stmt) {
        // Associa i parametri e esegui la query
        $stmt->bind_param("ss", $id_utente, $id_utente);
        $stmt->execute();
        $result = $stmt->get_result();

        $paletteData = array();

        while ($row = $result->fetch_assoc()) {
            // Converti i valori di isSaved e isLiked in boolean
            $row['isSaved'] = (bool)$row['isSaved'];
            $row['isLiked'] = (bool)$row['isLiked'];
            $paletteData[] = $row;
        }

        // Restituisci i dati come JSON
        header('Content-Type: application/json');
        echo json_encode($paletteData);

        $stmt->close();
    } else {
        echo "Errore nella preparazione della query: " . $connessione->error;
    }
}

// Chiudi la connessione al database
$connessione->close();
