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
    die("Errore di connessione al database: " . $connessione->connect_error);
}

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

// Ottieni i dati dal corpo della richiesta
$data = json_decode(file_get_contents("php://input"), true);
$id_palette = isset($data["id_palette"]) ? $connessione->real_escape_string($data["id_palette"]) : null;

if ($id_palette !== null) {
    // Verifica se l'utente ha già salvato questa palette
    $query_check_save = "SELECT * FROM save_palettes WHERE id_utente = ? AND id_palette = ?";
    $stmt = $connessione->prepare($query_check_save);

    if ($stmt) {
        // Associa i parametri e esegui la query
        $stmt->bind_param("ii", $id_utente, $id_palette);
        $stmt->execute();
        $result_check_save = $stmt->get_result();

        if ($result_check_save->num_rows == 0) { // L'utente non ha ancora salvato questa palette
            // Salva la palette per l'utente
            $sql_insert = "INSERT INTO save_palettes (id_utente, id_palette) VALUES (?, ?)";
            $stmt_insert = $connessione->prepare($sql_insert);

            if ($stmt_insert) {
                // Associa i parametri e esegui la query
                $stmt_insert->bind_param("ii", $id_utente, $id_palette);
                if ($stmt_insert->execute()) {
                    echo json_encode(array("id_palette" => $id_palette, "id_utente" => $id_utente, "isSaved" => true));
                } else {
                    echo json_encode(array("error" => "Errore durante il salvataggio della palette: " . $stmt_insert->error));
                }
                $stmt_insert->close();
            } else {
                echo json_encode(array("error" => "Errore nella preparazione della query di inserimento: " . $connessione->error));
            }
        } else {
            // Rimuovi la palette salvata per l'utente
            $sql_delete = "DELETE FROM save_palettes WHERE id_utente = ? AND id_palette = ?";
            $stmt_delete = $connessione->prepare($sql_delete);

            if ($stmt_delete) {
                // Associa i parametri e esegui la query
                $stmt_delete->bind_param("ii", $id_utente, $id_palette);
                if ($stmt_delete->execute()) {
                    echo json_encode(array("id_palette" => $id_palette, "id_utente" => $id_utente, "isSaved" => false));
                } else {
                    echo json_encode(array("error" => "Errore durante la rimozione della palette salvata: " . $stmt_delete->error));
                }
                $stmt_delete->close();
            } else {
                echo json_encode(array("error" => "Errore nella preparazione della query di eliminazione: " . $connessione->error));
            }
        }

        $stmt->close();
    } else {
        echo json_encode(array("error" => "Errore nella preparazione della query di verifica: " . $connessione->error));
    }
} else {
    echo json_encode(array("error" => "Id della palette non fornito."));
}

// Chiudi la connessione al database
$connessione->close();
?>
