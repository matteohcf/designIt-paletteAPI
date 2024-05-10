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
    /* $token = $data['token']; */
    $google = $data['google'];
    $email = $data['email'];
    $username = $data['username'];

    if (/* empty($token) || */ empty($google) || empty($email) || empty($username)) {
        $response = array(
            "status" => "error",
            "message" => "Dati mancanti"
        );
    } else if ($google === true) {
        // Verifica se l'utente è già registrato con Google
        $checkGoogleQuery = "SELECT * FROM utenti WHERE email = '$email' AND auth = 'google'";
        $checkGoogleResult = $connessione->query($checkGoogleQuery);

        if ($checkGoogleResult->num_rows > 0) {
            // L'utente è già registrato con Google
            $userData = $checkGoogleResult->fetch_assoc();

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
                "message" => "L'utente è già registrato con Google - Login",
                "id_utente" => $userData['id_utente'],
                "email" => $userData['email'],
                "username" => $userData['username'],
                "auth" => $userData['auth'],
                "token" => $jwt
            );
        } else {
            // Verifica se l'utente è già registrato normalmente
            $checkQuery = "SELECT * FROM utenti WHERE email = '$email' AND auth != 'google'";
            $checkResult = $connessione->query($checkQuery);

            if ($checkResult->num_rows > 0) {
                // L'utente è già registrato normalmente
                $response = array(
                    "status" => "error",
                    "message" => "L'utente è già registrato normalmente"
                );
            } else {
                // L'utente non è registrato, esegui l'inserimento nel database
                $insertQuery = "INSERT INTO utenti (email, username, auth) VALUES ('$email', '$username', 'google')";
                if ($connessione->query($insertQuery) === TRUE) {
                    $response = array(
                        "status" => "success",
                        "message" => "Utente GOOGLE registrato con successo"
                    );
                    /* Rifai la select */
                    $checkGoogleQuery = "SELECT * FROM utenti WHERE email = '$email' AND auth = 'google'";
                    $checkGoogleResult = $connessione->query($checkGoogleQuery);

                    if ($checkGoogleResult->num_rows > 0) {
                        // L'utente è già registrato con Google
                        $userData = $checkGoogleResult->fetch_assoc();

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
                            "message" => "Utente registrato + Login Success",
                            "id_utente" => $userData['id_utente'],
                            "email" => $userData['email'],
                            "username" => $userData['username'],
                            "auth" => $userData['auth'],
                            "token" => $jwt
                        );
                    }
                } else {
                    // Errore durante l'inserimento dell'utente nel database
                    $response = array(
                        "status" => "error",
                        "message" => "Errore durante la registrazione dell'utente GOOGLE"
                    );
                }
            }
        }
    }

    // Restituisci la risposta come JSON
    header('Content-Type: application/json');
    echo json_encode($response);

    // Chiudi la connessione al database
    $connessione->close();
}
