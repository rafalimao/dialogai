<?php
$page_title = 'Dashboard';
$page_subtitle = 'Visão geral do sistema de assistentes';

// Obter estatísticas
try {
    $totalAssistants = Assistant::count();
    $totalConversations = Conversation::count();
    $activeConversations = Conversation::countByStatus(Conversation::STATUS_IN_PROGRESS);
    $completedConversations = Conversation::countByStatus(Conversation::STATUS_COMPLETED);
    $failedConversations = Conversation::countByStatus(Conversation::STATUS_FAILED);
    $messageStats = Message::getMessageStats();
    
    // Conversas recentes
    $recentConversations = Conversation::findAll(null, 5);
    
    // Assistentes mais ativos
    $db = new Database();
    $activeAssistantsQuery = "
        SELECT a.*, COUNT(c.id) as conversation_count 
        FROM assistants a 
        LEFT JOIN conversations c ON (a.id = c.assistant1_id OR a.id = c.assistant2_id)
        GROUP BY a.id 
        ORDER BY conversation_count DESC 
        LIMIT 5
    ";
    $activeAssistantsData = $db->fetchAll($activeAssistantsQuery);
    
} catch (Exception $e) {
    $totalAssistants = 0;
    $totalConversations = 0;
    $activeConversations = 0;
    $completedConversations = 0;
    $failedConversations = 0;
    $messageStats = ['total' => 0, 'today' => 0, 'week' => 0, 'month' => 0];
    $recentConversations = [];
    $activeAssistantsData = [];
}
?>

<div class="fade-in">
    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white d-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 60px; border-radius: 50%; margin-right: 1rem;">
                            <i class="fas fa-robot" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <div style="font-size: 2rem; font-weight: 700; color: var(--primary-color);">
                                <?= $totalAssistants ?>
                            </div>
                            <div class="text-muted">Assistentes</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success text-white d-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 60px; border-radius: 50%; margin-right: 1rem;">
                            <i class="fas fa-comments" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <div style="font-size: 2rem; font-weight: 700; color: var(--success-color);">
                                <?= $totalConversations ?>
                            </div>
                            <div class="text-muted">Conversas</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning text-white d-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 60px; border-radius: 50%; margin-right: 1rem;">
                            <i class="fas fa-clock" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <div style="font-size: 2rem; font-weight: 700; color: var(--warning-color);">
                                <?= $activeConversations ?>
                            </div>
                            <div class="text-muted">Em Progresso</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info text-white d-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 60px; border-radius: 50%; margin-right: 1rem;">
                            <i class="fas fa-envelope" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <div style="font-size: 2rem; font-weight: 700; color: var(--primary-color);">
                                <?= number_format($messageStats['total']) ?>
                            </div>
                            <div class="text-muted">Mensagens</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Gráficos e Estatísticas -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <!-- Status das Conversas -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie text-primary"></i>
                        Status das Conversas
                    </h3>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Concluídas</span>
                                    <span class="badge badge-success"><?= $completedConversations ?></span>
                                </div>
                                <div class="progress mb-3" style="height: 10px;">
                                    <div class="progress-bar bg-success" 
                                         style="width: <?= $totalConversations > 0 ? ($completedConversations / $totalConversations) * 100 : 0 ?>%"></div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Em Progresso</span>
                                    <span class="badge badge-warning"><?= $activeConversations ?></span>
                                </div>
                                <div class="progress mb-3" style="height: 10px;">
                                    <div class="progress-bar bg-warning" 
                                         style="width: <?= $totalConversations > 0 ? ($activeConversations / $totalConversations) * 100 : 0 ?>%"></div>
                                </div>
                            </div>
                            
                            <div class="mb-0">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Falharam</span>
                                    <span class="badge badge-danger"><?= $failedConversations ?></span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-danger" 
                                         style="width: <?= $totalConversations > 0 ? ($failedConversations / $totalConversations) * 100 : 0 ?>%"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="text-center">
                                <div class="mb-3">
                                    <div style="font-size: 3rem; font-weight: 700; color: var(--success-color);">
                                        <?= $totalConversations > 0 ? round(($completedConversations / $totalConversations) * 100, 1) : 0 ?>%
                                    </div>
                                    <div class="text-muted">Taxa de Sucesso</div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-6">
                                        <div class="text-center p-2 bg-light rounded">
                                            <div class="font-weight-600"><?= $messageStats['today'] ?></div>
                                            <div class="text-muted" style="font-size: 0.75rem;">Hoje</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center p-2 bg-light rounded">
                                            <div class="font-weight-600"><?= $messageStats['week'] ?></div>
                                            <div class="text-muted" style="font-size: 0.75rem;">Esta Semana</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Assistentes Mais Ativos -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-trophy text-warning"></i>
                        Assistentes Mais Ativos
                    </h3>
                </div>
                
                <div class="card-body">
                    <?php if (empty($activeAssistantsData)): ?>
                        <div class="text-center py-3">
                            <i class="fas fa-robot text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2">Nenhum assistente ativo</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($activeAssistantsData as $index => $assistantData): ?>
                            <div class="d-flex align-items-center mb-3">
                                <div class="text-center me-3" style="width: 30px;">
                                    <?php if ($index === 0): ?>
                                        <i class="fas fa-crown text-warning"></i>
                                    <?php else: ?>
                                        <span class="text-muted"><?= $index + 1 ?>º</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="bg-primary text-white d-flex align-items-center justify-content-center" 
                                     style="width: 35px; height: 35px; border-radius: 50%; margin-right: 12px; font-weight: 600; font-size: 0.75rem;">
                                    <?= strtoupper(substr($assistantData['name'], 0, 2)) ?>
                                </div>
                                
                                <div style="flex: 1;">
                                    <div class="font-weight-600" style="font-size: 0.875rem;">
                                        <?= htmlspecialchars($assistantData['name']) ?>
                                    </div>
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        <?= $assistantData['conversation_count'] ?> conversas
                                    </div>
                                </div>
                                
                                <a href="<?= $base_url ?>/assistants/<?= $assistantData['id'] ?>" 
                                   class="btn btn-sm btn-outline">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Conversas Recentes -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    <i class="fas fa-history text-primary"></i>
                    Conversas Recentes
                </h3>
                
                <a href="<?= $base_url ?>/conversations" class="btn btn-outline btn-sm">
                    Ver Todas
                </a>
            </div>
        </div>
        
        <div class="card-body">
            <?php if (empty($recentConversations)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-comments text-muted" style="font-size: 3rem;"></i>
                    <h4 class="text-muted mt-3">Nenhuma conversa encontrada</h4>
                    <p class="text-muted">As conversas aparecerão aqui quando os assistentes começarem a interagir.</p>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Conversa</th>
                                <th>Participantes</th>
                                <th>Status</th>
                                <th>Início</th>
                                <th>Mensagens</th>
                                <th width="100">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentConversations as $conversation): ?>
                                <tr>
                                    <td>
                                        <strong>#<?= $conversation->getId() ?></strong>
                                    </td>
                                    
                                    <td>
                                        <?php 
                                        $assistant1 = $conversation->getAssistant1();
                                        $assistant2 = $conversation->getAssistant2();
                                        ?>
                                        <div style="font-size: 0.875rem;">
                                            <div><?= htmlspecialchars($assistant1->getName()) ?></div>
                                            <div class="text-muted">vs <?= htmlspecialchars($assistant2->getName()) ?></div>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <span class="badge <?= $conversation->getStatusBadgeClass() ?>">
                                            <?= $conversation->getStatusLabel() ?>
                                        </span>
                                    </td>
                                    
                                    <td>
                                        <div style="font-size: 0.875rem;">
                                            <?= date('d/m/Y H:i', strtotime($conversation->getStartTime())) ?>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <span class="badge badge-info">
                                            <?= $conversation->getMessageCount() ?>
                                        </span>
                                    </td>
                                    
                                    <td>
                                        <a href="<?= $base_url ?>/conversations/<?= $conversation->getId() ?>" 
                                           class="btn btn-sm btn-outline">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
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

.me-3 {
    margin-right: 1rem;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.card:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease;
}

.fa-crown {
    animation: pulse 2s infinite;
}
</style>

<script>
// Atualizar estatísticas em tempo real (opcional)
function updateStats() {
    fetch('<?= $base_url ?>/api/stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Atualizar números na tela
                console.log('Estatísticas atualizadas:', data.data);
            }
        })
        .catch(error => {
            console.error('Erro ao atualizar estatísticas:', error);
        });
}

// Atualizar a cada 30 segundos
setInterval(updateStats, 30000);

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
</script>

