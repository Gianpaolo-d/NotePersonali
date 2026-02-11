<?php
    ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

    session_start();

    if (empty($_SESSION['uid'])) {
        header("Location: login.php");
        return;
    }


    $note = [];
    $username = "";

    try {
        $conn = new PDO("mysql:host=localhost;dbname=notepersonali;charset=utf8", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("SELECT username FROM utenti WHERE id = ?");
        $stmt->execute([$_SESSION['uid']]);
        $user = $stmt->fetch();
        if ($user)
            $username = $user['username'];
        else
            $username = "?";

        $stmt = $conn->prepare("SELECT * FROM note WHERE uid = ? ORDER BY lastedit DESC");
        $stmt->execute([$_SESSION['uid']]);
        $note = $stmt->fetchAll();
        
    } catch(PDOException $e) {
        echo "Errore interno del DB server";
        exit;
    }

?>


<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Note Personali - Home</title>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <div>
            <h1><i class="fa-solid fa-note-sticky"></i> Note personali</h1>
        </div>
        <div class="userinfo-container">
            <div>
                <p id="greeting" >Ciao</p>
                <p><?php echo $username; ?></p>
            </div>
            <div>
                <button onclick="document.location.href='logout.php'" class="logout-button"><i class="fa-solid fa-right-from-bracket"></i> Esci</button>
            </div>
        </div>
    </header>
    
    <div class="notes-container">
        <div class="new-note" onclick="window.location.href='note.php'">
            <h2><i class="fa-solid fa-plus"></i> Nuova nota</h2>
        </div>


        <?php
        
        foreach ($note as $n) {
            echo "<div onclick='window.location.href=\"note.php?id=" . $n['id'] . "\"' class='note'>";
            echo "<h2>" . $n['title'] . "</h2>";
            echo "<p class='note-content'>" . $n['content'] . "</p>";

            $lastEdit = new DateTime($n["lastedit"]);
            echo "<p class='note-lastedit'>" . $lastEdit->format("d/m/Y H:i:s") . "</p>";

            echo "</div>";
        }
        ?>
    </div>
</body>

<script>

    window.onload = function() {
        let hour = new Date().getHours();
        let greeting = document.getElementById("greeting");

        if (hour < 12)
            greeting.textContent = "Buongiorno";
        else if (hour < 18)
            greeting.textContent = "Buon pomeriggio";
        else
            greeting.textContent = "Buonasera";
    };
</script>
</html>