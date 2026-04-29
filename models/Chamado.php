<?php
class Chamado {
    private $conn;
    private $table = "chamados";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (setor_id, prioridade_id, status) 
                  VALUES (:setor, :prioridade, 'Aberto')";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':setor', $data['setor_id']);
        $stmt->bindParam(':prioridade', $data['prioridade_id']);

        return $stmt->execute();
    }

    public function iniciar($id) {
        $query = "UPDATE {$this->table} 
                  SET status = 'Em atendimento', data_inicio = NOW()
                  WHERE id = :id AND status = 'Aberto'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    public function finalizar($id, $solucao) {
        $query = "UPDATE {$this->table} 
                  SET status = 'Finalizado', data_fim = NOW(), solucao = :solucao
                  WHERE id = :id AND status = 'Em atendimento'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':solucao', $solucao);

        return $stmt->execute();
    }
}