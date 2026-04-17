<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

require("note_class.php");


// Inizializzazzione sessione e controllo login
session_start();

if (!isset($_SESSION['uid'])) {
    header('Location: login.php');
    return;
}


// Inizializzazione variabili utili per la pagina
$username = "";
$note = new Note("", "", "", "", "");


// Connessione al DB
try {
    $conn = new PDO("mysql:host=localhost;dbname=notepersonali;charset=utf8", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Errore interno del DB server";
    exit;
}


// Gestione azioni POST per salvataggio ed eliminazione note
function handleNoteActions() {
    global $conn;

    if (!isset($_POST["action"]))
        return "";

    switch ($_POST["action"]) {
        case "delete":
            if (!isset($_POST["noteid"]) || !is_numeric($_POST["noteid"]))
                return "404"; // Not found
      
            $stmt = $conn->prepare("DELETE FROM notes WHERE id = ? AND uid = ?");
            $stmt->execute([$_POST["noteid"], $_SESSION['uid']]);
            return "200"; // OK

        case "save":
            if (!isset($_POST["title"]) || !isset($_POST["content"]))
                return "400"; // Bad request

            if (isset($_POST["noteid"]) && is_numeric($_POST["noteid"]))
            {
                $stmt = $conn->prepare("UPDATE notes SET title = ?, content = ?, lastedit = CURRENT_TIMESTAMP WHERE id = ? AND uid = ?");
                $stmt->execute([$_POST["title"], $_POST["content"], $_POST["noteid"], $_SESSION['uid']]);
            } else {
                $stmt = $conn->prepare("INSERT INTO notes (uid, title, content) VALUES (?, ?, ?)");
                $stmt->execute([$_SESSION['uid'], $_POST["title"], $_POST["content"]]);
                return "200 ".$conn->lastInsertId(); // OK
            }

            return "200"; // OK
    }
}

$actionResult = handleNoteActions();
if ($actionResult != "")
    return header("HTTP/1.1 " . $actionResult); // Se è stata eseguita un'azione POST, restituiamo lo status code e terminiamo l'esecuzione


// Recupero delle informazioni utente
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$_SESSION['uid']]);
$user = $stmt->fetch();
if ($user)
    $username = $user['username'];
else
    $username = "?";


// Recupero informazioni sulla nota e controllo della validita di essa
if (isset($_GET["id"]) && is_numeric($_GET["id"]))
{
    $stmt = $conn->prepare("SELECT * FROM notes WHERE id = ? AND uid = ?");
    $stmt->execute([$_GET["id"], $_SESSION['uid']]);
    $noteFetched = $stmt->fetch();

    if ($noteFetched)
        $note = new Note($noteFetched['id'], $noteFetched['title'], $noteFetched['content'], $noteFetched['lastedit'], $noteFetched['creationdate']);
    else {
        echo "Nota non trovata";
        exit;
    }
}

?>



<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Note personali - Nota</title>
    <link rel="stylesheet" href="styles/note.css">
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
                <?php 
                    if (isset($_GET["id"]))
                        echo '<button id="delete-button"><i class="fa-solid fa-trash"></i> Elimina</button>';
                ?>
                <button id="save-button"><i class="fa-solid fa-floppy-disk"></i> Salva</button>
            </div>
        </div>
        <div class="userinfo-container">
            <p id="greeting" >Ciao</p>
            <p><?php echo $username; ?></p>
        </div>
    </header>

    <div class="note-container">
        <input type="text" placeholder="Titolo della nota" id="note-title" value="<?php echo $note->title; ?>">
        <textarea placeholder="Scrivi qui la tua nota..." id="note-content"><?php echo $note->content; ?></textarea>
    </div>

    <div class="note-stats-container">
        <p>
            <i class="fa-solid fa-calendar"></i> Creazione: 
            <span>
                <?php 
                    if (isset($_GET["id"]))
                        echo (new DateTime($note->lastEdit))->format("d/m/Y H:i:s");
                    else 
                        echo "Creazione non effettuata";
                ?>
            </span>
        </p>
        <p>
            <i class="fa-solid fa-pen-to-square"></i> Ultima modifica: 
            <span id ="note-lastsave">
                <?php 
                    if (isset($_GET["id"]))
                        echo (new DateTime($note->lastEdit))->format("d/m/Y H:i:s");
                    else 
                        echo "Nessuna modifica";
                ?>
            </span>
        </p>
        <p>
            <i class="fa-solid fa-file-word"></i> Parole: <span id ="note-words"></span>
        </p>
        <p>
            <i class="fa-solid fa-font"></i> Caratteri: <span id="note-chars"></span> 
        </p>
    </div>

</body>


<script>
    const saveButton = document.getElementById("save-button");
    const deleteButton = document.getElementById("delete-button");

    const noteContent = document.getElementById("note-content");
    var lastSavedContent = noteContent.value;
    const noteTitle = document.getElementById("note-title");
    var lastSavedNoteTitle = noteTitle.value;

    const words = document.getElementById("note-words");
    const chars = document.getElementById("note-chars");
    const lastsave = document.getElementById("note-lastsave");


    var saveTimeout;
    function checkChanges(justSaved = false) {
        if (noteContent.value !== lastSavedContent || noteTitle.value !== lastSavedNoteTitle) 
        {
            clearTimeout(saveTimeout);
            saveButton.style.backgroundColor = "white";
            saveButton.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Salva';
            saveButton.style.display = "block";
            setTimeout(() => {
                saveButton.style.opacity = 1;
            }, 1);
        }
        else
        {
            if (justSaved)
            {
                saveButton.style.backgroundColor = "rgb(0, 255, 110)";
                saveButton.innerHTML = '<i class="fa-solid fa-check"></i> Salvato!';
            }
            
            saveTimeout = setTimeout(() => {
                saveButton.style.opacity = 0;
                setTimeout(() => {
                    saveButton.style.display = "none";
                }, 300);
            }, justSaved ? 1000 : 0);
        }
    };

    function updateNoteStats() {
        chars.textContent = noteContent.value.length;
        words.textContent = noteContent.value.length ? noteContent.value.split(" ").length : "0";
    };

    async function saveNote() {
        if (noteTitle.value == "")
            noteTitle.value = "Nota del "+ new Date().toLocaleDateString();

        let req = await fetch("note.php", {
            method: "POST",
            body: new URLSearchParams({
                "action": "save",
                "title": noteTitle.value,
                "content": noteContent.value,
                "noteid": "<?php if(isset($_GET['id'])) echo $_GET['id']; ?>"
            })
        });

        if (req.status !== 200) 
            return;

        let respTxt = await req.statusText;
        if (respTxt === "OK") {
            lastSavedContent = noteContent.value;
            lastSavedNoteTitle = noteTitle.value;
            lastsave.textContent = new Date().toLocaleString();
            checkChanges(true);
        } else 
            window.location.href = "note.php?id=" + respTxt;

    }


    window.onload = function() {
        const hour = new Date().getHours();
        const greeting = document.getElementById("greeting");

        if (hour < 12)
            greeting.textContent = "Buongiorno";
        else if (hour < 18)
            greeting.textContent = "Buon pomeriggio";
        else
            greeting.textContent = "Buonasera";

        updateNoteStats();
    };


    noteContent.addEventListener("input", () => {
        checkChanges();
        updateNoteStats();
    });

    noteTitle.addEventListener("input", checkChanges);

    saveButton.addEventListener("click", saveNote);

    // Ctrl+S Shortcut
    window.addEventListener("keydown", function(e) {
        if (e.key === "s" && (e.ctrlKey || e.metaKey)) {
            e.preventDefault();
            saveNote();
        }
    });

    if (deleteButton)
        deleteButton.addEventListener("click", async function() {
            if (!confirm("Sei sicuro di voler eliminare questa nota? Questa azione è irreversibile"))
                return;

            let req = await fetch("note.php", {
                method: "POST",
                body: new URLSearchParams({
                    "action": "delete",
                    "noteid": "<?php if(isset($_GET['id'])) echo $_GET['id']; ?>"
                })
            });

            if (req.status !== 200) 
                return;

            window.location.href = "home.php";
        });


</script>

</html>
