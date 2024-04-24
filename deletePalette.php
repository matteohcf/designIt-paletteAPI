<?php
include_once("config.php");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Methods: *");
    http_response_code(200);
    exit;
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

// Connessione al database
$connessione = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($connessione->connect_error) {
    echo "Errore di connessione: " . $connessione->connect_error;
} else {
    $paletteId = isset($_GET['paletteId']) ? $_GET['paletteId'] : null;
    $userId = isset($_GET['userId']) ? $_GET['userId'] : null;

    if ($paletteId === null || $userId === null) {
        echo json_encode(array("error" => "ID della palette o ID dell'utente non specificato"));
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
