<?php
class Chamado {
    private $conn;
    private $table = "chamados";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Criar chamado
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (setor_id, prioridade_id, status) 
                  VALUES (:setor, :prioridade, 'Aberto')";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':setor', $data['setor_id']);
        $stmt->bindParam(':prioridade', $data['prioridade_id']);

        if ($stmt->execute()) {
            return "Chamado criado com sucesso";
        }

        return "Erro ao criar chamado";
    }

    // Iniciar atendimento
    public function iniciar($id) {
        if (!is_numeric($id)) {
            return "ID inválido";
        }

        $query = "UPDATE {$this->table} 
                  SET status = 'Em atendimento', data_inicio = NOW()
                  WHERE id = :id AND status = 'Aberto'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            return "Não foi possível iniciar (já iniciado, finalizado ou inválido)";
        }

        return "Chamado iniciado com sucesso";
    }

    // Finalizar atendimento
    public function finalizar($id, $solucao) {
        if (!is_numeric($id)) {
            return "ID inválido";
        }

        $query = "UPDATE {$this->table} 
                  SET status = 'Finalizado', 
                      data_fim = NOW(), 
                      solucao = :solucao
                  WHERE id = :id AND status = 'Em atendimento'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':solucao', $solucao);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            return "Não foi possível finalizar (não iniciado ou inválido)";
        }

        return "Chamado finalizado com sucesso";
    }

    // Listar chamados (com JOIN)
    public function listar() {
        $query = "SELECT 
                    c.*, 
                    s.nome AS setor,
                    p.nome AS prioridade,
                    p.tempo_estimado
                  FROM chamados c
                  JOIN setores s ON c.setor_id = s.id
                  JOIN prioridades p ON c.prioridade_id = p.id";

        return $this->conn->query($query);
    }
}