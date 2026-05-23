<?php 
/*
================================================================================
 NOTE CLASS (IT / EN)
--------------------------------------------------------------------------------
DESCRIZIONE (IT)
- Definisce la classe PHP Note, utilizzata per modellare una nota.
- Contiene campi: id, title, content, lastEdit, creationDate.
- Fornisce metodi di utilità per lunghezza del contenuto, verifica contenuto
  vuoto, formattazione date e stampa informativa.

DESCRIPTION (EN)
- Defines the PHP Note class used to model a note.
- Includes fields: id, title, content, lastEdit, creationDate.
- Provides utility methods for content length, empty check, date formatting
  and informational printing.
================================================================================
*/

    class Note {

        public $id;
        public $title;
        public $content;
        public $lastEdit;
        public $creationDate;

        function __construct($id, $title, $content, $lastEdit, $creationDate) {
            $this->id = $id;
            $this->title = $title;
            $this->content = $content;
            $this->lastEdit = $lastEdit;
            $this->creationDate = $creationDate;
        }

        public function getContentLength() {
            return strlen($this->content);
        }

        public function isEmpty() {
            return empty($this->content);
        }

        public function getFormattedLastEdit($format) {
            $date = new DateTime($this->lastEdit);
            return $date->format($format);
        }

        public function printInformations() {
            echo "ID: " . $this->id . "<br>";
            echo "Titolo: " . $this->title . "<br>";
            echo "Contenuto: " . $this->content . "<br>";
            echo "Ultima modifica: " . $this->lastEdit . "<br>";
        }
    }   
?>