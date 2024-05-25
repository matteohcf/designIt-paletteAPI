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
    echo "Errore di connessione: " . $connessione->connect_error;
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
    $userId = $decodedToken['id_utente'] ?? null;

    if ($userId === null) {
        http_response_code(401);
        echo json_encode(array("message" => "Invalid Token"));
        exit;
    }

    $paletteId = isset($_GET['paletteId']) ? intval($_GET['paletteId']) : null;

    if ($paletteId === null) {
        echo json_encode(array("error" => "ID della palette non specificato"));
    } else {
        // Prepara la query per verificare se l'utente ha il permesso di eliminare la palette
        $stmt_check_permission = $connessione->prepare("SELECT id_palette FROM palettes WHERE id_palette = ? AND creating_user_id = ?");
        $stmt_check_permission->bind_param("ii", $paletteId, $userId);
        $stmt_check_permission->execute();
        $result_check_permission = $stmt_check_permission->get_result();

        if ($result_check_permission->num_rows > 0) {
            // L'utente ha il permesso di eliminare la palette
            // Prepara la query per eliminare la palette
            $stmt_delete = $connessione->prepare("DELETE FROM palettes WHERE id_palette = ?");

            // Associa il parametro alla query
            $stmt_delete->bind_param("i", $paletteId);

            // Esegui la query
            if ($stmt_delete->execute()) {
                echo json_encode(array("message" => "Palette eliminata con successo con ID: " . $paletteId));
            } else {
                echo json_encode(array("error" => "Errore durante l'eliminazione della palette: " . $connessione->error));
            }

            $stmt_delete->close();
        } else {
            // L'utente non ha il permesso di eliminare la palette
            echo json_encode(array("error" => "L'utente non ha i permessi per eliminare questa palette"));
        }

        $stmt_check_permission->close();
    }
}

// Chiudi la connessione al database
$connessione->close();
?>
