<?php
include_once("config.php");
include_once("vendor/autoload.php"); // Carica la libreria JWT

use \Firebase\JWT\JWT;

// Connessione al database
$connessione = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($connessione->connect_error) {
    echo "Errore di connessione: " . $connessione->connect_error;
} else {
    // Ottieni i dati
    $data = json_decode(file_get_contents("php://input"), true);
    $email = isset($data['email']) ? $connessione->real_escape_string($data['email']) : null;
    $password = isset($data['password']) ? $connessione->real_escape_string($data['password']) : null;
    $passwordHashed = md5($password);

    if ($email !== null && $password !== null) {
        $sql = "SELECT * FROM utenti WHERE email = ? AND password = ? AND auth = 'normal'";
        $stmt = $connessione->prepare($sql);

        if ($stmt) {
            // Associa i parametri e esegui la query
            $stmt->bind_param("ss", $email, $passwordHashed);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Utente trovato
                $userData = $result->fetch_assoc();

                // Chiudi il risultato e la dichiarazione preparata
                $result->close();
                $stmt->close();

                // Genera il token JWT
                $secret_key = "FFGGDDKSJ344";
                $token = array(
                    "id_utente" => $userData['id_utente'],
                    "email" => $userData['email'],
                    "exp" => time() + (60 * 60) // Scadenza del token dopo 1 ora
                );
                $jwt = JWT::encode($token, $secret_key, 'HS256');

                $response = array(
                    "status" => "success",
                    "message" => "Login effettuato con successo",
                    "id_utente" => $userData['id_utente'],
                    "email" => $userData['email'],
                    "username" => $userData['username'],
                    "auth" => $userData['auth'],
                    "token" => $jwt
                );
            } else {
                // Dato non trovato
                $response = array(
                    "status" => "error",
                    "message" => "Credenziali non valide"
                );
            }
        } else {
            echo "Errore nella preparazione della query: " . $connessione->error;
        }
    } else {
        echo "Email o password non fornite.";
    }

    // Restituisci i dati come JSON
    header('Content-Type: application/json');
    echo json_encode($response);

    // Chiudi la connessione al database
    $connessione->close();
}
?>
