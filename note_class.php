<?php 
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