<?php
require_once __DIR__ . '/../models/Chamado.php';

class ChamadoController {
    private $model;

    public function __construct($db) {
        $this->model = new Chamado($db);
    }

    public function store($data) {
        
        $this->model->create($data);
        
        
        header("Location: index.php");
        exit; 
    }

    public function iniciar($id) {
        
        $this->model->iniciar($id);
        
        
        header("Location: index.php");
        exit;
    }

    public function finalizar($id, $solucao) {
        // Finaliza o chamado
        $this->model->finalizar($id, $solucao);
        
        
        header("Location: index.php");
        exit;
    }
    
    public function listar() {
        return $this->model->listar();
    }
}