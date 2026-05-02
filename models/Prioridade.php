<?php
class Prioridade {
    private $conn;
    private $table = "prioridades";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function listar() {
        return $this->conn->query("SELECT * FROM {$this->table}");
    }
}