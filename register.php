<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

session_start();

if (!empty($_SESSION['uid'])) {
    header("Location: home.php");
    return;
}

$registerError = "";

function handleRegistration() {
    if (strlen($_POST['username']) < 3 || strlen($_POST['username']) > 20)
        return "L'username deve essere compreso tra 3 e 20 caratteri";

    try {
        $conn = new PDO("mysql:host=localhost;dbname=notepersonali;charset=utf8", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("SELECT id FROM utenti WHERE username = ?");
        $stmt->execute([$_POST['username']]);
        $user = $stmt->fetch();

        if ($user)
            return "Username già esistente";
        
        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO utenti (username, password) VALUES (?, ?)");
        $stmt->execute([$_POST['username'], $hash]);

        $_SESSION['uid'] = $conn->lastInsertId();
        header("Location: home.php");
        exit;
    }  catch(PDOException $e) {
        return "Errore interno del DB server";
    }
}

if (!empty($_POST)) 
    $registerError = handleRegistration();


?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Note Personali - Registrazione</title>
    <link rel="stylesheet" href="login_register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="centered-container">
        <div>
            <h1><i class="fa-solid fa-note-sticky"></i> Note personali</h1>
            <p>Qui potrai creare e gestire le tue note in modo facile e veloce</p>
        </div>
         <form action="register.php" method="POST">
            <div class="login-container">
                <h2>Registrazione</h2>

                <div class="input-group">
                    <label for="username">Username:</label>
                    <input type="username" name="username" id="username" placeholder="Username" required>
                    <i class="fa-solid fa-user"></i>
                </div>
                
                <div class="input-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    <i class="fa-solid fa-key"></i>
                </div>

                <div class="input-group">
                    <label for="password2">Ripeti password:</label>
                    <input type="password" id="password2" placeholder="Ripeti password" required>
                    <i class="fa-solid fa-key"></i>
                </div>

                <a href="login.php">Hai gia un account? Accedi cliccando qui</a>

                <p> <?php echo $registerError  ?> </p>

                <button>Regitrati</button>
            </div>
        </form>
    </div>
</body>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelector("form").addEventListener("submit", function(event) {
            const password = document.getElementById("password").value.trim();
            const password2 = document.getElementById("password2").value.trim();
            if (password === password2) 
                return;
                
            event.preventDefault();
            document.getElementById("errortxt").textContent = "Le password non corrispondono";
        });
    });
</script>

</html>