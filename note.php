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

if (isset($_POST["title"]) && isset($_POST["content"]) && isset($_POST["noteid"])) {
    $stmt = $conn->prepare("UPDATE note SET title = ?, content = ?, lastedit = NOW() WHERE id = ? AND uid = ?");
    $stmt->execute([$_POST["title"], $_POST["content"], $_POST["noteid"], $_SESSION['uid']]);
    echo "OK";
    return;
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
        <p><i class="fa-solid fa-file-word"></i> Parole: <span id ="note-words"></span> </p>
        <p><i class="fa-solid fa-font"></i> Caratteri: <span id="note-chars"></span> </p>
    
    </div>

</body>


<script>
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
        words.textContent = noteContent.value.trim().split(/\s+/).length;
        chars.textContent = noteContent.value.length;
    };

    window.onload = function() {
        let hour = new Date().getHours();
        let greeting = document.getElementById("greeting");

        if (hour < 12)
            greeting.textContent = "Buongiorno";
        else if (hour < 18)
            greeting.textContent = "Buon pomeriggio";
        else
            greeting.textContent = "Buonasera";


        updateNoteStats();
    };


    const saveButton = document.getElementById("save-button");

    const noteContent = document.getElementById("note-content");
    var lastSavedContent = noteContent.value;
    const noteTitle = document.getElementById("note-title");
    var lastSavedNoteTitle = noteTitle.value;

    const words = document.getElementById("note-words");
    const chars = document.getElementById("note-chars");

    noteContent.addEventListener("input", () => {
        checkChanges();
        updateNoteStats();
    });

    noteTitle.addEventListener("input", checkChanges);
    

    async function saveNote() {
        let req = await fetch("note.php?id=<?php echo $_GET['id']; ?>", {
            method: "POST",
            body: new URLSearchParams({
                "title": noteTitle.value,
                "content": noteContent.value,
                "noteid": "<?php echo $_GET['id']; ?>"
            })
        });
        
        if (await req.text() != "OK")
            return;

        lastSavedContent = noteContent.value;
        lastSavedNoteTitle = noteTitle.value;
        checkChanges(true);
    }

    saveButton.addEventListener("click", saveNote);

    // Save if ctrl s is pressed

    window.addEventListener("keydown", function(e) {
        if (e.key === "s" && (e.ctrlKey || e.metaKey)) {
            e.preventDefault();
            saveNote();
        }
    });


</script>
</html>
