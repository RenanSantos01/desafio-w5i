<?php
date_default_timezone_set('America/Sao_Paulo'); // Sincroniza o horário do PHP com o do Brasil

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/ChamadoController.php';
require_once __DIR__ . '/../models/Setor.php';
require_once __DIR__ . '/../models/Prioridade.php';

$db = (new Database())->connect();

$controller = new ChamadoController($db);
$setorModel = new Setor($db);
$prioridadeModel = new Prioridade($db);

$mensagem = "";

// ROTAS
$action = $_GET['action'] ?? null;

if ($action === 'criar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensagem = $controller->store($_POST);
}

if ($action === 'iniciar' && isset($_GET['id'])) {
    $mensagem = $controller->iniciar($_GET['id']);
}

if ($action === 'finalizar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensagem = $controller->finalizar($_POST['id'], $_POST['solucao']);
}

// Dados
$chamados_raw = $controller->listar();
// Garante que seja um array para podermos percorrer mais de uma vez (para os cards e para a tabela)
$chamados = is_array($chamados_raw) ? $chamados_raw : $chamados_raw->fetchAll(PDO::FETCH_ASSOC);

$setores = $setorModel->listar();
$prioridades = $prioridadeModel->listar();

// Cálculos para o Dashboard
$countAbertos = 0;
$countEmAtendimento = 0;
$countFinalizados = 0;
$countAtrasados = 0;

foreach ($chamados as $c) {
    if ($c['status'] == 'Aberto') $countAbertos++;
    if ($c['status'] == 'Em atendimento') $countEmAtendimento++;
    if ($c['status'] == 'Finalizado') $countFinalizados++;
    
    $horas = 0;
    if ($c['data_inicio']) {
        $inicio = strtotime($c['data_inicio']);
        $fim = $c['data_fim'] ? strtotime($c['data_fim']) : time();
        $horas = ($fim - $inicio) / 3600;
    }
    if ($horas > $c['tempo_estimado'] && $c['status'] !== 'Finalizado') {
        $countAtrasados++;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Atendimentos | W5i</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f6f9; }
        .card-stats { border-radius: 10px; border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1"><i class="bi bi-headset"></i> Service Desk - W5i</span>
    </div>
</nav>

<div class="container-fluid px-4">

    <?php if ($mensagem): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle-fill"></i> <?= htmlspecialchars($mensagem) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card card-stats bg-white p-3 border-start border-4 border-secondary text-center">
                <h6 class="text-muted mb-1">Abertos</h6>
                <h3 class="mb-0"><?= $countAbertos ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stats bg-white p-3 border-start border-4 border-primary text-center">
                <h6 class="text-muted mb-1">Em Atendimento</h6>
                <h3 class="mb-0 text-primary"><?= $countEmAtendimento ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stats bg-white p-3 border-start border-4 border-success text-center">
                <h6 class="text-muted mb-1">Finalizados</h6>
                <h3 class="mb-0 text-success"><?= $countFinalizados ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stats bg-white p-3 border-start border-4 border-danger text-center">
                <h6 class="text-muted mb-1">Atrasados (SLA Estourado)</h6>
                <h3 class="mb-0 text-danger"><?= $countAtrasados ?></h3>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0"><i class="bi bi-plus-circle"></i> Novo Chamado</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="/desafio-w5i/public/index.php?action=criar">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Setor:</label>
                            <select name="setor_id" class="form-select" required>
                                <option value="" disabled selected>Selecione...</option>
                                <?php foreach ($setores as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Prioridade:</label>
                            <select name="prioridade_id" class="form-select" required>
                                <option value="" disabled selected>Selecione...</option>
                                <?php foreach ($prioridades as $p): ?>
                                    <option value="<?= $p['id'] ?>">
                                        <?= htmlspecialchars($p['nome']) ?> (SLA: <?= $p['tempo_estimado'] ?>h)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark">Abrir Chamado</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0"><i class="bi bi-list-task"></i> Fila de Chamados</h5>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Setor</th>
                                <th>Prioridade (SLA)</th>
                                <th>Status</th>
                                <th>Tempo Decorrido</th>
                                <th>Solução</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($chamados as $c): 
                                // Cálculo do Tempo
                                $horas = 0;
                                if ($c['data_inicio']) {
                                    $inicio = strtotime($c['data_inicio']);
                                    $fim = $c['data_fim'] ? strtotime($c['data_fim']) : time();
                                    $horas = ($fim - $inicio) / 3600;
                                }
                                
                                // Regra de Atraso
                                $estourouSLA = ($horas > $c['tempo_estimado']);
                                $classLinha = ($estourouSLA && $c['status'] !== 'Finalizado') ? "table-danger" : "";
                                
                                // Estilo do Status
                                $badgeClass = 'bg-secondary';
                                if ($c['status'] == 'Em atendimento') $badgeClass = 'bg-primary';
                                if ($c['status'] == 'Finalizado') $badgeClass = 'bg-success';
                            ?>
                            <tr class="<?= $classLinha ?>">
                                <td><strong><?= $c['id'] ?></strong></td>
                                <td><?= htmlspecialchars($c['setor']) ?></td>
                                <td><?= htmlspecialchars($c['prioridade']) ?> (<?= $c['tempo_estimado'] ?>h)</td>
                                <td><span class="badge <?= $badgeClass ?>"><?= $c['status'] ?></span></td>
                                
                                <td class="<?= $estourouSLA ? 'text-danger fw-bold' : '' ?>">
                                    <i class="bi bi-clock"></i> <?= number_format($horas, 2, ',', '.') ?>h
                                </td>
                                
                                <td class="text-muted text-break" style="max-width: 200px;">
                                    <?= htmlspecialchars($c['solucao'] ?? '-') ?>
                                </td>
                                
                                <td class="text-center" style="min-width: 180px;">
                                    <?php if ($c['status'] == 'Aberto'): ?>
                                        <a href="/desafio-w5i/public/index.php?action=iniciar&id=<?= $c['id'] ?>" class="btn btn-sm btn-primary">
                                            <i class="bi bi-play-fill"></i> Iniciar
                                        </a>
                                    <?php elseif ($c['status'] == 'Em atendimento'): ?>
                                        <form method="POST" action="/desafio-w5i/public/index.php?action=finalizar" class="d-flex justify-content-center">
                                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                            <input type="text" name="solucao" class="form-control form-control-sm me-1" placeholder="Solução..." required style="max-width: 150px;">
                                            <button type="submit" class="btn btn-sm btn-success" title="Finalizar Atendimento">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-success"><i class="bi bi-check2-all"></i> Concluído</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if(empty($chamados)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Nenhum chamado encontrado.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>