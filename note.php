<?php

session_start();

if (!isset($_SESSION['uid'])) {
    header('Location: login.php');
    return;
}

$username = "";
$noteTitle = "";
$noteContent = "";

try {
    $conn = new PDO("mysql:host=localhost;dbname=notepersonali;charset=utf8", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
    echo "Errore interno del DB server";
    exit;
}

$stmt = $conn->prepare("SELECT username FROM utenti WHERE id = ?");
$stmt->execute([$_SESSION['uid']]);
$user = $stmt->fetch();
if ($user)
    $username = $user['username'];
else
    $username = "?";

if (isset($_GET["id"]))
{
    $stmt = $conn->prepare("SELECT * FROM note WHERE id = ? AND uid = ?");
    $stmt->execute([$_GET["id"], $_SESSION['uid']]);
    $note = $stmt->fetch();

    if ($note) {
        $noteTitle = $note['title'];
        $noteContent = $note['content'];
    } else {
        echo "Nota non trovata";
        exit;
    }
}

if (isset($_POST["title"]) && isset($_POST["content"])) {
    $stmt = $conn->prepare("UPDATE note SET title = ?, content = ?, lastedit = NOW() WHERE id = ? AND uid = ?");
    $stmt->execute([$_POST["title"], $_POST["content"], $_GET["id"], $_SESSION['uid']]);
    echo "OK";
    return;
}

?>



<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Note personali - Nota</title>
    <link rel="stylesheet" href="note.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    
    <header>
        <div class="header-left">
            <div>
                <button onclick="document.location.href='home.php'"><i class="fa-solid fa-arrow-left"></i> Home</button>
                <h1><i class="fa-solid fa-note-sticky"></i> Note personali</h1>
            </div>
            
            <div>   
                <button id="save-button"><i class="fa-solid fa-floppy-disk"></i> Salva</button>
            </div>
        </div>
        <div class="userinfo-container">
            <p id="greeting" >Ciao</p>
            <p><?php echo $username; ?></p>
        </div>
    </header>

    <div class="note-container">
        <input type="text" placeholder="Titolo della nota" id="note-title" value="<?php echo $noteTitle; ?>">
        <textarea placeholder="Scrivi qui la tua nota..." id="note-content"><?php echo $noteContent; ?></textarea>
    </div>

    <div class="note-stats-container">
    
        <p><i class="fa-solid fa-pen-to-square"></i> Ultima modifica: <?php echo $note['lastedit']; ?></p>
        <p><i class="fa-solid fa-font"></i> Parole: </p>
        <p><i class="fa-solid fa-font"></i> Caratteri: </p>
    
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


    const saveButton = document.getElementById("save-button");

    const noteContent = document.getElementById("note-content");
    var lastSavedContent = noteContent.value;
    const noteTitle = document.getElementById("note-title");
    var lastSavedNoteTitle = noteTitle.value;

    const checkChanges = function() {
        if (noteContent.value !== lastSavedContent || noteTitle.value !== lastSavedNoteTitle) 
            saveButton.style.opacity = 1;
        else
            saveButton.style.opacity = 0;
    };

    noteContent.addEventListener("input", checkChanges);
    noteTitle.addEventListener("input", checkChanges);
    

</script>
</html>
