<?php
include_once("config.php");

// Connessione al database
$connessione = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($connessione->connect_error) {
    echo "Errore di connessione: " . $connessione->connect_error;
} else {
    // Recupera i dati
    $data = json_decode(file_get_contents("php://input"), true);
    $username = isset($data['username']) ? $connessione->real_escape_string($data['username']) : null;
    $email = isset($data['email']) ? $connessione->real_escape_string($data['email']) : null;
    $password = isset($data['password']) ? $connessione->real_escape_string($data['password']) : null;

    if ($username !== null && $email !== null && $password !== null) {
        // Verifica che username, email e password non siano vuoti e siano stati passati
        if (empty($username) || empty($email) || empty($password)) {
            $response = array(
                "status" => "error",
                "message" => "Username, email e password sono obbligatori"
            );
        } else {
            // Verifica se l'indirizzo email è valido
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response = array(
                    "status" => "error",
                    "message" => "L'indirizzo email non è valido"
                );
            } else {
                $sql = "SELECT * FROM utenti WHERE username = ? OR email = ?";
                $stmt = $connessione->prepare($sql);

                if ($stmt) {
                    // Associa i parametri e esegui la query
                    $stmt->bind_param("ss", $username, $email);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        // L'utente esiste già
                        $response = array(
                            "status" => "error",
                            "message" => "L'utente esiste già"
                        );
                    } else {
                        // Hash della password da inserire nel database
                        $hashedPassword = md5($password);

                        // Esegui la query preparata per inserire l'utente nel database con l'hash della password
                        $sql = "INSERT INTO utenti (email, username, password) VALUES (?, ?, ?)";
                        $stmt = $connessione->prepare($sql);

                        if ($stmt) {
                            // Associa i parametri e esegui la query
                            $stmt->bind_param("sss", $email, $username, $hashedPassword);
                            if ($stmt->execute()) {
                                // Inserito correttamente
                                $response = array(
                                    "status" => "success",
                                    "message" => "Utente registrato con successo"
                                );
                            } else {
                                // Errore durante l'inserimento dell'utente
                                $response = array(
                                    "status" => "error",
                                    "message" => "Errore durante la registrazione dell'utente"
                                );
                            }
                        } else {
                            $response = array(
                                "status" => "error",
                                "message" => "Errore nella preparazione della query: " . $connessione->error
                            );
                        }

                        $stmt->close();
                    }
                } else {
                    $response = array(
                        "status" => "error",
                        "message" => "Errore nella preparazione della query: " . $connessione->error
                    );
                }
            }
        }
    } else {
        echo "Username, email o password non forniti.";
    }

    // Restituisci i dati come JSON
    header('Content-Type: application/json');
    echo json_encode($response);

    // Chiudi la connessione al database
    $connessione->close();
}
