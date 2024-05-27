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

    // Query per ottenere i dati della palette
    $sql = "SELECT id_palette, color1, color2, color3, color4, likes, creating_user_id FROM palettes WHERE creating_user_id = ? ORDER BY likes DESC";
    $stmt = $connessione->prepare($sql);

    if ($stmt) {
        // Associa i parametri e esegui la query
        $stmt->bind_param("s", $id_utente);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $paletteData = array();

            while ($row = $result->fetch_assoc()) {
                $paletteId = $row['id_palette'];
                $row['isLiked'] = false;
                $row['isSaved'] = false;

                // Controlla se l'utente ha messo like alla palette
                $query_check_like = "SELECT * FROM likes WHERE id_palette = ? AND id_utente = ?";
                $stmt_check_like = $connessione->prepare($query_check_like);
                $stmt_check_like->bind_param("ss", $paletteId, $id_utente);
                $stmt_check_like->execute();
                $result_check_like = $stmt_check_like->get_result();
                if ($result_check_like->num_rows > 0) {
                    $row['isLiked'] = true;
                }

                // Controlla se l'utente ha salvato la palette
                $query_check_saved = "SELECT * FROM save_palettes WHERE id_palette = ? AND id_utente = ?";
                $stmt_check_saved = $connessione->prepare($query_check_saved);
                $stmt_check_saved->bind_param("ss", $paletteId, $id_utente);
                $stmt_check_saved->execute();
                $result_check_saved = $stmt_check_saved->get_result();
                if ($result_check_saved->num_rows > 0) {
                    $row['isSaved'] = true;
                }

                $paletteData[] = $row;
            }

            // Restituisci i dati come JSON
            header('Content-Type: application/json');
            echo json_encode($paletteData);
        } else {
            echo "Nessun dato della palette trovato.";
        }

        $stmt->close();
    } else {
        echo "Errore nella preparazione della query: " . $connessione->error;
    }
}

// Chiudi la connessione al database
$connessione->close();
