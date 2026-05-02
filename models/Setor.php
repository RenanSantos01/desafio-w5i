<?php
class Setor {
    private $conn;
    private $table = "setores";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function listar() {
        return $this->conn->query("SELECT * FROM {$this->table}");
    }
}