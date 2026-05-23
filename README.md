# NotePersonali

## IT

### Descrizione
**IT**: Applicazione web per creare e gestire in modo facile e veloce le note personali.

### Prerequisiti / Requisiti
- **PHP** (con estensioni PDO per MySQL)
- **MySQL / MariaDB**
- **Server web Apache + PHP** (tipico in XAMPP)

### Installazione e Avvio (XAMPP)
1. Copia la cartella `NotePersonali` dentro `htdocs`.
2. Avvia **Apache** e **MySQL** da XAMPP.
3. Importa `db.sql` nel DB manager (es. phpMyAdmin).
   - Database: `notepersonali`
4. Verifica le credenziali nel codice (devono corrispondere al tuo ambiente):
   - `new PDO("mysql:host=localhost;dbname=notepersonali;charset=utf8", "root", "")`
5. Apri nel browser:
   - `http://localhost/NotePersonali/login.php`

### Funzionalità
- Autenticazione tramite sessione PHP (login/register/logout)
- CRUD base delle note (crea, visualizza, salva, elimina)
- Caricamento note dell’utente dal database (filtrate per `uid`)
- Salvataggio via `fetch()` (richieste POST a `note.php`)
- Calcolo parole e caratteri lato client

### Struttura del progetto
- `login.php` : pagina di login
- `register.php` : pagina di registrazione
- `logout.php` : distrugge la sessione e reindirizza al login
- `home.php` : elenco note dell’utente
- `note.php` : editor e gestione di una singola nota
- `note_class.php` : classe `Note` (modello dati)
- `db.sql` : schema del database (tabelle `users` e `notes`)
- `styles/` : fogli di stile CSS

### Schema database (db.sql)
Il database `notepersonali` include due tabelle:

#### `users`
- `id` (PK)
- `username` (UNIQUE)
- `password` (hash)

#### `notes`
- `id` (PK)
- `uid` (FK verso `users.id`)
- `title`
- `content`
- `creationdate`
- `lastedit`

Ogni nota appartiene a un singolo utente tramite il campo `uid`.

### Flusso utente
1. **Register**: crea un account con password hashata.
2. **Login**: verifica credenziali e imposta `$_SESSION['uid']`.
3. **Home**: mostra tutte le note dell’utente ordinate per `lastedit DESC`.
4. **Note**: apertura editor per una nota (con `?id=...`).
   - Salvataggio: `POST note.php` con `action=save`
   - Eliminazione: `POST note.php` con `action=delete`
5. **Logout**: termina la sessione.

### Sicurezza & Note tecniche
- Le password sono salvate in DB usando `password_hash()` e verificate con `password_verify()`.
- Le query usano `PDO` con prepared statements (riduce i rischi di SQL injection).
- Possibili miglioramenti: validazioni più robuste, gestione errori più completa per le risposte AJAX.

---

## EN

### Description
**EN**: Web application to create and manage personal notes easily and quickly.

### Requirements
- **PHP** (with PDO extension for MySQL)
- **MySQL / MariaDB**
- **Apache + PHP web server** (typical for XAMPP)

### Installation & Setup (XAMPP)
1. Copy the `NotePersonali` folder into `htdocs`.
2. Start **Apache** and **MySQL** from XAMPP.
3. Import `db.sql` into your DB manager (e.g. phpMyAdmin).
   - Database: `notepersonali`
4. Verify credentials in code (must match your environment):
   - `new PDO("mysql:host=localhost;dbname=notepersonali;charset=utf8", "root", "")`
5. Open in your browser:
   - `http://localhost/NotePersonali/login.php`

### Features
- PHP session-based authentication (login/register/logout)
- Basic notes CRUD (create, view, save, delete)
- User notes loaded from the database (filtered by `uid`)
- Save via `fetch()` (POST requests to `note.php`)
- Words and characters counter computed client-side

### Project Structure
- `login.php` : login page
- `register.php` : registration page
- `logout.php` : destroys the session and redirects to login
- `home.php` : user notes list
- `note.php` : editor and management for a single note
- `note_class.php` : `Note` class (data model)
- `db.sql` : database schema (tables `users` and `notes`)
- `styles/` : CSS stylesheets

### Database Schema (db.sql)
The `notepersonali` database includes two tables:

#### `users`
- `id` (PK)
- `username` (UNIQUE)
- `password` (hash)

#### `notes`
- `id` (PK)
- `uid` (FK to `users.id`)
- `title`
- `content`
- `creationdate`
- `lastedit`

Each note belongs to exactly one user via the `uid` field.

### User Flow
1. **Register**: creates an account with a hashed password.
2. **Login**: verifies credentials and sets `$_SESSION['uid']`.
3. **Home**: shows all user notes ordered by `lastedit DESC`.
4. **Note**: opens the editor for a note (via `?id=...`).
   - Save: `POST note.php` with `action=save`
   - Delete: `POST note.php` with `action=delete`
5. **Logout**: ends the session.

### Security & Technical Notes
- Passwords are stored using `password_hash()` and verified using `password_verify()`.
- Queries use `PDO` prepared statements (reduces SQL injection risk).
- Possible improvements: stronger validation, more complete error handling for AJAX responses.

