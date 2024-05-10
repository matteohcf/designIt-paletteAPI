<?php

include_once("config.php");
include_once("verifyTokenJWT.php");

// Connessione al database
$connessione = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($connessione->connect_error) {
    die("Errore di connessione al database: " . $connessione->connect_error);
}

/* Verifica token */
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
$id_palette = isset($data["id_palette"]) ? $connessione->real_escape_string($data["id_palette"]) : null;
$id_utente = isset($data["id_utente"]) ? $connessione->real_escape_string($data["id_utente"]) : null;

if ($id_palette !== null && $id_utente !== null) {
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
                    echo "Errore durante il salvataggio della palette: " . $stmt_insert->error;
                }
                $stmt_insert->close();
            } else {
                echo "Errore nella preparazione della query di inserimento: " . $connessione->error;
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
                    echo "Errore durante la rimozione della palette salvata: " . $stmt_delete->error;
                }
                $stmt_delete->close();
            } else {
                echo "Errore nella preparazione della query di eliminazione: " . $connessione->error;
            }
        }

        $stmt->close();
    } else {
        echo "Errore nella preparazione della query di verifica: " . $connessione->error;
    }
} else {
    echo "Id della palette o id dell'utente non forniti.";
}

// Chiudi la connessione al database
$connessione->close();
