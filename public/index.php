<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/ChamadoController.php';

$db = (new Database())->connect();
$controller = new ChamadoController($db);

$mensagem = "";

// Capturar ação
$action = $_GET['action'] ?? null;

// ROTAS
if ($action === 'criar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensagem = $controller->store($_POST);
}

if ($action === 'iniciar' && isset($_GET['id'])) {
    $mensagem = $controller->iniciar($_GET['id']);
}

if ($action === 'finalizar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensagem = $controller->finalizar($_POST['id'], $_POST['solucao']);
}

// Buscar chamados
$chamados = $controller->listar();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Controle de Atendimentos</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<h2>Sistema de Controle de Atendimentos</h2>

<?php if ($mensagem): ?>
    <p style="background:#22c55e;padding:10px;border-radius:5px;">
        <?= $mensagem ?>
    </p>
<?php endif; ?>

<!-- FORM CRIAR -->
<h3>Criar Chamado</h3>
<form method="POST" action="/desafio-w5i/public/index.php?action=criar">
    <label>Setor ID:</label>
    <input type="number" name="setor_id" required>

    <label>Prioridade ID:</label>
    <input type="number" name="prioridade_id" required>

    <button type="submit">Criar</button>
</form>

<!-- FORM INICIAR -->
<h3>Iniciar Chamado</h3>
<form method="GET" action="/desafio-w5i/public/index.php">
    <input type="hidden" name="action" value="iniciar">

    <label>ID do Chamado:</label>
    <input type="number" name="id" required>

    <button type="submit">Iniciar</button>
</form>

<!-- FORM FINALIZAR -->
<h3>Finalizar Chamado</h3>
<form method="POST" action="/desafio-w5i/public/index.php?action=finalizar">
    <label>ID:</label>
    <input type="number" name="id" required>

    <label>Solução:</label>
    <input type="text" name="solucao" required>

    <button type="submit">Finalizar</button>
</form>

<!-- LISTAGEM -->
<h3>Lista de Chamados</h3>

<table border="1" cellpadding="10">
<tr>
    <th>ID</th>
    <th>Setor</th>
    <th>Prioridade</th>
    <th>Status</th>
    <th>Tempo (h)</th>
</tr>

<?php foreach ($chamados as $c): 

    if ($c['data_inicio']) {
        $inicio = strtotime($c['data_inicio']);
        $fim = $c['data_fim'] ? strtotime($c['data_fim']) : time();
        $horas = ($fim - $inicio) / 3600;
    } else {
        $horas = 0;
    }

    $atrasado = ($horas > $c['tempo_estimado']) ? "atrasado" : "";
?>

<tr class="<?= $atrasado ?>">
    <td><?= $c['id'] ?></td>
    <td><?= $c['setor'] ?></td>
    <td><?= $c['prioridade'] ?></td>
    <td><?= $c['status'] ?></td>
    <td><?= round($horas, 2) ?></td>
</tr>

<?php endforeach; ?>

</table>

</body>
</html>