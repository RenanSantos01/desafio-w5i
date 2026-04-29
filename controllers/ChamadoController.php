<?php
require_once '../models/Chamado.php';

class ChamadoController {
    private $model;

    public function __construct($db) {
        $this->model = new Chamado($db);
    }

    public function store($data) {
        return $this->model->create($data);
    }

    public function iniciar($id) {
        return $this->model->iniciar($id);
    }

    public function finalizar($id, $solucao) {
        return $this->model->finalizar($id, $solucao);
    }
}