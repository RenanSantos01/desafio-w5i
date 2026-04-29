<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Carregar dependências com caminho seguro
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/ChamadoController.php';

// Conectar ao banco
$db = (new Database())->connect();

if (!$db) {
    die("Erro ao conectar com o banco de dados.");
}

// Instanciar controller
$controller = new ChamadoController($db);

// Capturar ação
$action = $_GET['action'] ?? null;

// Roteamento simples
switch ($action) {

    case 'criar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->store($_POST);
            echo "Chamado criado com sucesso!";
        } else {
            echo "Método inválido.";
        }
        break;

    case 'iniciar':
        if (isset($_GET['id'])) {
            $controller->iniciar($_GET['id']);
            echo "Chamado iniciado!";
        } else {
            echo "ID não informado.";
        }
        break;

    case 'finalizar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->finalizar($_POST['id'], $_POST['solucao']);
            echo "Chamado finalizado!";
        } else {
            echo "Método inválido.";
        }
        break;

    default:
    echo "<h2>Sistema de Controle de Atendimentos</h2>";

    echo "<h3>Criar Chamado</h3>
    <form method='POST' action='?action=criar'>
        Setor ID: <input type='number' name='setor_id' required><br>
        Prioridade ID: <input type='number' name='prioridade_id' required><br>
        <button type='submit'>Criar</button>
    </form>";

    echo "<h3>Iniciar Chamado</h3>
    <form method='GET'>
        <input type='hidden' name='action' value='iniciar'>
        ID do Chamado: <input type='number' name='id' required>
        <button type='submit'>Iniciar</button>
    </form>";

    echo "<h3>Finalizar Chamado</h3>
    <form method='POST' action='?action=finalizar'>
        ID: <input type='number' name='id' required><br>
        Solução: <input type='text' name='solucao' required><br>
        <button type='submit'>Finalizar</button>
    </form>";

    break;
}