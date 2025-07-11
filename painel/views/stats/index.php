<?php
$page_title = 'Estatísticas';
$page_subtitle = 'Análise detalhada do desempenho do sistema';
?>

<div class="fade-in">
    <!-- Resumo Geral -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <div style="font-size: 3rem; font-weight: 700; color: var(--primary-color);">
                        <?= $stats['assistants'] ?? 0 ?>
                    </div>
                    <div class="text-muted">Total de Assistentes</div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <div style="font-size: 3rem; font-weight: 700; color: var(--success-color);">
                        <?= $stats['conversations']['total'] ?? 0 ?>
                    </div>
                    <div class="text-muted">Total de Conversas</div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <div style="font-size: 3rem; font-weight: 700; color: var(--info-color);">
                        <?= number_format($stats['messages']['total'] ?? 0) ?>
                    </div>
                    <div class="text-muted">Total de Mensagens</div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <div style="font-size: 3rem; font-weight: 700; color: var(--warning-color);">
                        <?= $stats['success_rate'] ?? 0 ?>%
                    </div>
                    <div class="text-muted">Taxa de Sucesso</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Estatísticas de Conversas -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line text-primary"></i>
                        Status das Conversas
                    </h3>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-success text-white rounded mb-3">
                                <div style="font-size: 2rem; font-weight: 700;">
                                    <?= $stats['conversations']['completed'] ?? 0 ?>
                                </div>
                                <div>Concluídas</div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-warning text-white rounded mb-3">
                                <div style="font-size: 2rem; font-weight: 700;">
                                    <?= $stats['conversations']['in_progress'] ?? 0 ?>
                                </div>
                                <div>Em Progresso</div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-danger text-white rounded mb-3">
                                <div style="font-size: 2rem; font-weight: 700;">
                                    <?= $stats['conversations']['failed'] ?? 0 ?>
                                </div>
                                <div>Falharam</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Gráfico de barras simples -->
                    <div class="mt-4">
                        <h5>Distribuição por Status</h5>
                        <?php
                        $total = $stats['conversations']['total'] ?? 1;
                        $completedPercent = $total > 0 ? ($stats['conversations']['completed'] / $total) * 100 : 0;
                        $inProgressPercent = $total > 0 ? ($stats['conversations']['in_progress'] / $total) * 100 : 0;
                        $failedPercent = $total > 0 ? ($stats['conversations']['failed'] / $total) * 100 : 0;
                        ?>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Concluídas</span>
                                <span><?= round($completedPercent, 1) ?>%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" style="width: <?= $completedPercent ?>%"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Em Progresso</span>
                                <span><?= round($inProgressPercent, 1) ?>%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-warning" style="width: <?= $inProgressPercent ?>%"></div>
                            </div>
                        </div>
                        
                        <div class="mb-0">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Falharam</span>
                                <span><?= round($failedPercent, 1) ?>%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-danger" style="width: <?= $failedPercent ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock text-info"></i>
                        Métricas de Tempo
                    </h3>
                </div>
                
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div style="font-size: 2.5rem; font-weight: 700; color: var(--info-color);">
                            <?= $stats['avg_duration'] ?? 0 ?>
                        </div>
                        <div class="text-muted">Duração Média (minutos)</div>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Conversas Hoje:</span>
                            <strong><?= $stats['conversations']['today'] ?? 0 ?></strong>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Esta Semana:</span>
                            <strong><?= $stats['conversations']['week'] ?? 0 ?></strong>
                        </div>
                    </div>
                    
                    <div class="mb-0">
                        <div class="d-flex justify-content-between">
                            <span>Este Mês:</span>
                            <strong><?= $stats['conversations']['month'] ?? 0 ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Estatísticas de Mensagens -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-envelope text-primary"></i>
                        Atividade de Mensagens
                    </h3>
                </div>
                
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded">
                                <div style="font-size: 1.5rem; font-weight: 600;">
                                    <?= $stats['messages']['today'] ?? 0 ?>
                                </div>
                                <div class="text-muted">Hoje</div>
                            </div>
                        </div>
                        
                        <div class="col-6">
                            <div class="p-3 bg-light rounded">
                                <div style="font-size: 1.5rem; font-weight: 600;">
                                    <?= $stats['messages']['week'] ?? 0 ?>
                                </div>
                                <div class="text-muted">Esta Semana</div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded">
                                <div style="font-size: 1.5rem; font-weight: 600;">
                                    <?= $stats['messages']['month'] ?? 0 ?>
                                </div>
                                <div class="text-muted">Este Mês</div>
                            </div>
                        </div>
                        
                        <div class="col-6">
                            <div class="p-3 bg-light rounded">
                                <div style="font-size: 1.5rem; font-weight: 600;">
                                    <?= number_format($stats['messages']['total'] ?? 0) ?>
                                </div>
                                <div class="text-muted">Total</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle text-info"></i>
                        Informações do Sistema
                    </h3>
                </div>
                
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Versão do Sistema:</span>
                            <strong><?= SITE_VERSION ?></strong>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Timezone:</span>
                            <strong><?= TIMEZONE ?></strong>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Data/Hora Atual:</span>
                            <strong><?= date('d/m/Y H:i:s') ?></strong>
                        </div>
                    </div>
                    
                    <div class="mb-0">
                        <div class="d-flex justify-content-between">
                            <span>Uptime do Sistema:</span>
                            <strong>
                                <?php
                                $uptime = time() - filemtime(__DIR__ . '/../../config/config.php');
                                $hours = floor($uptime / 3600);
                                $minutes = floor(($uptime % 3600) / 60);
                                echo "{$hours}h {$minutes}m";
                                ?>
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Ações Rápidas -->
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-bolt text-warning"></i>
                Ações Rápidas
            </h3>
        </div>
        
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <a href="<?= $base_url ?>/assistants" class="btn btn-outline w-100 mb-3">
                        <i class="fas fa-robot"></i>
                        Ver Assistentes
                    </a>
                </div>
                
                <div class="col-md-3">
                    <a href="<?= $base_url ?>/conversations" class="btn btn-outline w-100 mb-3">
                        <i class="fas fa-comments"></i>
                        Ver Conversas
                    </a>
                </div>
                
                <div class="col-md-3">
                    <a href="<?= $base_url ?>/messages" class="btn btn-outline w-100 mb-3">
                        <i class="fas fa-envelope"></i>
                        Ver Mensagens
                    </a>
                </div>
                
                <div class="col-md-3">
                    <a href="<?= $base_url ?>/dashboard" class="btn btn-outline w-100 mb-3">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.progress {
    background-color: var(--gray-200);
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar {
    transition: width 0.3s ease;
    border-radius: 4px;
}

.w-100 {
    width: 100% !important;
}

.card:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease;
    box-shadow: var(--shadow-lg);
}
</style>

<script>
// Animação de entrada para os cards
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});

// Atualizar estatísticas periodicamente
function updateStats() {
    // Recarregar a página a cada 5 minutos para atualizar estatísticas
    setTimeout(() => {
        window.location.reload();
    }, 300000); // 5 minutos
}

updateStats();
</script>

