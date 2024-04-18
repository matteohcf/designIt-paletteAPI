<?php
include_once("config.php");

// Abilita CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// CONNESSIONE AL DB
$connessione = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($connessione->connect_error) {
    echo "Errore di connessione: " . $connessione->connect_error;
} else {
    // Recupera i dati inviati dal frontend
    $data = json_decode(file_get_contents("php://input"), true);
    $username = isset($data['username']) ? $connessione->real_escape_string($data['username']) : null;
    $email = isset($data['email']) ? $connessione->real_escape_string($data['email']) : null;
    $password = isset($data['password']) ? $connessione->real_escape_string($data['password']) : null;

    if ($username !== null && $email !== null && $password !== null) {
        // Verifica che username, email e password non siano vuoti
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
                // Esegui la query preparata per verificare se l'utente esiste già nel database
                $sql = "SELECT * FROM utenti WHERE username = ? OR email = ?";
                $stmt = $connessione->prepare($sql);

                if ($stmt) {
                    // Associa i parametri e esegui la query
                    $stmt->bind_param("ss", $username, $email);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    // Controlla se l'utente esiste già nel database
                    if ($result->num_rows > 0) {
                        // L'utente esiste già, restituisci un messaggio di errore
                        $response = array(
                            "status" => "error",
                            "message" => "L'utente esiste già"
                        );
                    } else {
                        // Hash della password
                        $hashedPassword = md5($password);

                        // Esegui la query preparata per inserire l'utente nel database
                        $sql = "INSERT INTO utenti (email, username, password) VALUES (?, ?, ?)";
                        $stmt = $connessione->prepare($sql);

                        if ($stmt) {
                            // Associa i parametri e esegui la query
                            $stmt->bind_param("sss", $email, $username, $hashedPassword);
                            if ($stmt->execute()) {
                                // Successo, restituisci un messaggio di successo
                                $response = array(
                                    "status" => "success",
                                    "message" => "Utente registrato con successo"
                                );
                            } else {
                                // Errore durante l'inserimento dell'utente nel database
                                $response = array(
                                    "status" => "error",
                                    "message" => "Errore durante la registrazione dell'utente"
                                );
                            }
                        } else {
                            // Errore nella preparazione della query
                            $response = array(
                                "status" => "error",
                                "message" => "Errore nella preparazione della query: " . $connessione->error
                            );
                        }

                        // Chiudi lo statement
                        $stmt->close();
                    }
                } else {
                    // Errore nella preparazione della query
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

    // Restituisci la risposta al frontend come JSON
    header('Content-Type: application/json');
    echo json_encode($response);

    // Chiudi la connessione al database
    $connessione->close();
}
