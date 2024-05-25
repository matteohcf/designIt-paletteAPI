<?php

include_once("config.php");
include_once("verifyTokenJWT.php"); // Includi il file con la funzione verifyToken

// Connessione al database
$connessione = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($connessione->connect_error) {
    die(json_encode(array("message" => "Errore di connessione: " . $connessione->connect_error)));
}

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

function getBearerToken() {
    $headers = getAuthorizationHeader();
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    }
    return null;
}

/* Verifica token */
$token = getBearerToken();

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

// Ottieni i dati dal post
$data = json_decode(file_get_contents("php://input"), true);
$id_palette = isset($data["id_palette"]) ? $connessione->real_escape_string($data["id_palette"]) : null;

if ($id_palette) {
    // Verifica se l'utente ha già messo like per questa palette
    $stmt_check_like = $connessione->prepare("SELECT * FROM likes WHERE id_utente = ? AND id_palette = ?");
    $stmt_check_like->bind_param("ss", $id_utente, $id_palette);
    $stmt_check_like->execute();
    $result_check_like = $stmt_check_like->get_result();

    if ($result_check_like->num_rows == 0) { // L'utente non ha ancora messo like per questa palette
        $stmt_update_likes = $connessione->prepare("UPDATE palettes SET likes = likes + 1 WHERE id_palette = ?");
        $stmt_update_likes->bind_param("s", $id_palette);
        $stmt_insert_like = $connessione->prepare("INSERT INTO likes (id_utente, id_palette) VALUES (?, ?)");
        $stmt_insert_like->bind_param("ss", $id_utente, $id_palette);

        if ($stmt_update_likes->execute() && $stmt_insert_like->execute()) {
            $stmt_get_likes = $connessione->prepare("SELECT likes FROM palettes WHERE id_palette = ?");
            $stmt_get_likes->bind_param("s", $id_palette);
            $stmt_get_likes->execute();
            $result_likes = $stmt_get_likes->get_result();

            if ($result_likes) {
                $row = $result_likes->fetch_assoc();
                $likes = $row['likes'];
                echo json_encode(array("likes" => $likes, "id_palette" => $id_palette, "id_utente" => $id_utente, "isLiked" => true));
            } else {
                http_response_code(500);
                echo json_encode(array("message" => "Errore nell'ottenere i likes aggiornati."));
            }
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Errore nell'aggiornamento dei likes: " . $connessione->error));
        }
    } else {
        $stmt_undo_like = $connessione->prepare("UPDATE palettes SET likes = likes - 1 WHERE id_palette = ?");
        $stmt_undo_like->bind_param("s", $id_palette);
        $stmt_delete_like = $connessione->prepare("DELETE FROM likes WHERE id_utente = ? AND id_palette = ?");
        $stmt_delete_like->bind_param("ss", $id_utente, $id_palette);

        if ($stmt_undo_like->execute() && $stmt_delete_like->execute()) {
            $stmt_get_likes = $connessione->prepare("SELECT likes FROM palettes WHERE id_palette = ?");
            $stmt_get_likes->bind_param("s", $id_palette);
            $stmt_get_likes->execute();
            $result_likes = $stmt_get_likes->get_result();

            if ($result_likes) {
                $row = $result_likes->fetch_assoc();
                $likes = $row['likes'];
                echo json_encode(array("likes" => $likes, "id_palette" => $id_palette, "id_utente" => $id_utente, "isLiked" => false));
            } else {
                http_response_code(500);
                echo json_encode(array("message" => "Errore nell'ottenere i likes aggiornati."));
            }
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Errore nell'aggiornamento dei likes: " . $connessione->error));
        }
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Dati mancanti."));
}

$connessione->close();

?>
