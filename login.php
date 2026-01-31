<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

session_start();

if (!empty($_SESSION['uid'])) {
    header("Location: home.php");
    return;
}

$loginError = "";

function handleLogin()
{
    try {
        $conn = new PDO("mysql:host=localhost;dbname=notepersonali;charset=utf8", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("SELECT id, password FROM utenti WHERE username = ?");
        $stmt->execute([$_POST['username']]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($_POST['password'], $user['password']))
            return "Username o password errati";
        
        $_SESSION['uid'] = $user['id'];
        header("Location: home.php");
        exit;
    } catch(PDOException $e) {
        return "Internal server DB error";
    }
}

if (!empty($_POST)) 
    $loginError = handleLogin();
    

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Note Personali - Login</title>
    <link rel="stylesheet" href="login_register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="centered-container">
        <div>
            <h1><i class="fa-solid fa-note-sticky"></i> Note personali</h1>
            <p>Qui potrai creare e gestire le tue note in modo facile e veloce</p>
        </div>
         <form action="login.php" method="POST">
            <div class="login-container">
                <h2>Accedi</h2>

                <div class="input-group">
                    <label for="username">Username:</label>
                    <input type="username" name="username" id="username" placeholder="Username">
                    <i class="fa-solid fa-user"></i>
                </div>
                
                <div class="input-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" placeholder="Password">
                    <i class="fa-solid fa-key"></i>
                </div>

                <a href="register.php">Non hai un account? Registrati cliccando qui</a>

                <p> <?php echo $loginError  ?> </p>

                <button type="submit">Accedi</button>
            </div>
        </form>
    </div>
</body>
</html>