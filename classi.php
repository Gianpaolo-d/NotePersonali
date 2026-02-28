<?php 

    class Note {
        public $id;
        public $title;
        public $content;
        public $lastEdit;

        function __construct($id, $title, $content, $lastEdit) {
            $this->id = $id;
            $this->title = $title;
            $this->content = $content;
            $this->lastEdit = $lastEdit;
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


    $notaTest = new Note(1, "Nota di test", "Contenuto della nota di test", "2026/02/28 08:44:00");
    $notaTest->printInformations();
    echo "<br>";
    echo "Lunghezza contenuto: " . $notaTest->getContentLength() . "<br>";
    echo "E' vuota?: " . ($notaTest->isEmpty() ? "Si" : "No") . "<br>";
    echo "Ultima modifica formattata: " . $notaTest->getFormattedLastEdit("H:i:s d/m/Y") . "<br>";
    




   
?>