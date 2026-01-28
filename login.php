<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Note Personali - Login</title>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="centered-container">
        <div>
            <h1><i class="fa-solid fa-note-sticky"></i> Note personali</h1>
            <p>Qui potrai creare e gestire le tue note in modo facile e veloce</p>
        </div>
        <div class="login-container">
            <h2>Accedi</h2>

            <form action="login.php" method="POST">
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

                <button>Accedi</button>
            </form>
        </div>
    </div>
</body>
</html>