<?php
$page_title = htmlspecialchars($assistant->getName());
$page_subtitle = 'Detalhes e histórico do assistente';
?>

<div class="fade-in">
    <!-- Breadcrumb -->
    <nav class="mb-4">
        <div class="d-flex align-items-center gap-2 text-muted">
            <a href="<?= $base_url ?>/assistants" class="text-primary">
                <i class="fas fa-robot"></i>
                Assistentes
            </a>
            <i class="fas fa-chevron-right" style="font-size: 0.75rem;"></i>
            <span><?= htmlspecialchars($assistant->getName()) ?></span>
        </div>
    </nav>
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Informações Principais -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white d-flex align-items-center justify-content-center" 
                                     style="width: 50px; height: 50px; border-radius: 50%; margin-right: 15px; font-weight: 600; font-size: 1.25rem;">
                                    <?= strtoupper(substr($assistant->getName(), 0, 2)) ?>
                                </div>
                                <div>
                                    <div style="font-size: 1.25rem; font-weight: 600;">
                                        <?= htmlspecialchars($assistant->getName()) ?>
                                    </div>
                                    <div class="text-muted" style="font-size: 0.875rem;">
                                        ID: <?= $assistant->getId() ?>
                                    </div>
                                </div>
                            </div>
                        </h3>
                        
                        <div class="d-flex gap-2">
                            <a href="<?= $base_url ?>/assistants/<?= $assistant->getId() ?>/edit" class="btn btn-secondary">
                                <i class="fas fa-edit"></i>
                                Editar
                            </a>
                            
                            <button type="button" 
                                    class="btn btn-danger" 
                                    onclick="confirmDelete(<?= $assistant->getId() ?>, '<?= htmlspecialchars($assistant->getName(), ENT_QUOTES) ?>')">
                                <i class="fas fa-trash"></i>
                                Excluir
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Perfil -->
                    <div class="mb-4">
                        <h4 class="mb-3">
                            <i class="fas fa-user-tag text-primary"></i>
                            Perfil e Características
                        </h4>
                        <div class="bg-light p-3 rounded">
                            <?= nl2br(htmlspecialchars($assistant->getProfile())) ?>
                        </div>
                    </div>
                    
                    <!-- Prompt Inicial -->
                    <div class="mb-4">
                        <h4 class="mb-3">
                            <i class="fas fa-play-circle text-primary"></i>
                            Prompt Inicial
                        </h4>
                        <div class="bg-light p-3 rounded">
                            <?= nl2br(htmlspecialchars($assistant->getInitialPrompt())) ?>
                        </div>
                    </div>
                    
                    <!-- Objetivo -->
                    <div class="mb-0">
                        <h4 class="mb-3">
                            <i class="fas fa-target text-primary"></i>
                            Objetivo Final
                        </h4>
                        <div class="bg-light p-3 rounded">
                            <?= nl2br(htmlspecialchars($assistant->getGoal())) ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Conversas Recentes -->
            <div class="card mt-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fas fa-comments text-primary"></i>
                            Conversas Recentes
                        </h3>
                        
                        <?php if ($conversation_count > 5): ?>
                            <a href="<?= $base_url ?>/conversations?assistant=<?= $assistant->getId() ?>" class="btn btn-outline btn-sm">
                                Ver Todas (<?= $conversation_count ?>)
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="card-body">
                    <?php if (empty($conversations)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-comments" style="font-size: 3rem; color: var(--gray-400); margin-bottom: 1rem;"></i>
                            <h4 class="text-muted mb-3">Nenhuma conversa encontrada</h4>
                            <p class="text-muted">
                                Este assistente ainda não participou de nenhuma conversa.
                            </p>
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
                                        <th>Duração</th>
                                        <th width="100">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($conversations as $conversation): ?>
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>#<?= $conversation->getId() ?></strong>
                                                    <div class="text-muted" style="font-size: 0.875rem;">
                                                        <?= $conversation->getMessageCount() ?> mensagens
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            <td>
                                                <?php 
                                                $assistant1 = $conversation->getAssistant1();
                                                $assistant2 = $conversation->getAssistant2();
                                                ?>
                                                <div style="font-size: 0.875rem;">
                                                    <div class="<?= $assistant1->getId() === $assistant->getId() ? 'text-primary font-weight-600' : '' ?>">
                                                        <?= htmlspecialchars($assistant1->getName()) ?>
                                                    </div>
                                                    <div class="text-muted">vs</div>
                                                    <div class="<?= $assistant2->getId() === $assistant->getId() ? 'text-primary font-weight-600' : '' ?>">
                                                        <?= htmlspecialchars($assistant2->getName()) ?>
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            <td>
                                                <span class="badge <?= $conversation->getStatusBadgeClass() ?>">
                                                    <?= $conversation->getStatusLabel() ?>
                                                </span>
                                            </td>
                                            
                                            <td>
                                                <div style="font-size: 0.875rem;">
                                                    <?= date('d/m/Y', strtotime($conversation->getStartTime())) ?>
                                                    <div class="text-muted">
                                                        <?= date('H:i', strtotime($conversation->getStartTime())) ?>
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            <td>
                                                <?php 
                                                $duration = $conversation->getFormattedDuration();
                                                if ($duration): 
                                                ?>
                                                    <span class="text-muted" style="font-size: 0.875rem;">
                                                        <?= $duration ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            
                                            <td>
                                                <a href="<?= $base_url ?>/conversations/<?= $conversation->getId() ?>" 
                                                   class="btn btn-sm btn-outline" 
                                                   title="Ver Conversa">
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
        
        <div class="col-lg-4">
            <!-- Estatísticas -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar text-primary"></i>
                        Estatísticas
                    </h3>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <div style="font-size: 2rem; font-weight: 700; color: var(--primary-color);">
                                    <?= $conversation_count ?>
                                </div>
                                <div class="text-muted" style="font-size: 0.875rem;">
                                    Conversas
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <div style="font-size: 2rem; font-weight: 700; color: var(--success-color);">
                                    <?php
                                    $completedCount = 0;
                                    foreach ($conversations as $conv) {
                                        if ($conv->getStatus() === 'completed') $completedCount++;
                                    }
                                    echo $completedCount;
                                    ?>
                                </div>
                                <div class="text-muted" style="font-size: 0.875rem;">
                                    Concluídas
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <strong>Última Atividade:</strong><br>
                        <?php if ($last_conversation): ?>
                            <span class="text-muted">
                                <?= date('d/m/Y H:i', strtotime($last_conversation)) ?>
                            </span>
                        <?php else: ?>
                            <span class="text-muted">Nunca</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-0">
                        <strong>Taxa de Sucesso:</strong><br>
                        <?php 
                        $successRate = $conversation_count > 0 ? round(($completedCount / $conversation_count) * 100, 1) : 0;
                        ?>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress" style="flex: 1; height: 8px;">
                                <div class="progress-bar bg-success" 
                                     style="width: <?= $successRate ?>%; background-color: var(--success-color) !important;"></div>
                            </div>
                            <span class="text-muted" style="font-size: 0.875rem;">
                                <?= $successRate ?>%
                            </span>
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
                    <div class="d-grid gap-2">
                        <a href="<?= $base_url ?>/assistants/<?= $assistant->getId() ?>/edit" class="btn btn-secondary">
                            <i class="fas fa-edit"></i>
                            Editar Assistente
                        </a>
                        
                        <a href="<?= $base_url ?>/conversations?assistant=<?= $assistant->getId() ?>" class="btn btn-outline">
                            <i class="fas fa-comments"></i>
                            Ver Todas as Conversas
                        </a>
                        
                        <a href="<?= $base_url ?>/assistants/create" class="btn btn-outline">
                            <i class="fas fa-plus"></i>
                            Criar Novo Assistente
                        </a>
                        
                        <hr>
                        
                        <button type="button" 
                                class="btn btn-danger" 
                                onclick="confirmDelete(<?= $assistant->getId() ?>, '<?= htmlspecialchars($assistant->getName(), ENT_QUOTES) ?>')">
                            <i class="fas fa-trash"></i>
                            Excluir Assistente
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Informações Técnicas -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cog text-secondary"></i>
                        Informações Técnicas
                    </h3>
                </div>
                
                <div class="card-body">
                    <div class="mb-3">
                        <strong>ID do Assistente:</strong><br>
                        <code><?= $assistant->getId() ?></code>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Tamanho do Perfil:</strong><br>
                        <span class="text-muted"><?= strlen($assistant->getProfile()) ?> caracteres</span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Tamanho do Prompt:</strong><br>
                        <span class="text-muted"><?= strlen($assistant->getInitialPrompt()) ?> caracteres</span>
                    </div>
                    
                    <div class="mb-0">
                        <strong>Tamanho do Objetivo:</strong><br>
                        <span class="text-muted"><?= strlen($assistant->getGoal()) ?> caracteres</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div id="deleteModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div class="card" style="max-width: 400px; margin: 2rem;">
        <div class="card-header">
            <h3 class="card-title text-danger">
                <i class="fas fa-exclamation-triangle"></i>
                Confirmar Exclusão
            </h3>
        </div>
        <div class="card-body">
            <p>Tem certeza que deseja excluir o assistente <strong id="assistantName"></strong>?</p>
            <p class="text-muted">Esta ação não pode ser desfeita e removerá todas as conversas associadas.</p>
        </div>
        <div class="card-body" style="border-top: 1px solid var(--gray-200); padding-top: 1rem;">
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline" onclick="closeDeleteModal()">
                    Cancelar
                </button>
                <form method="POST" action="<?= $base_url ?>/assistants/delete" style="display: inline;">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    <input type="hidden" name="id" id="deleteAssistantId">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i>
                        Excluir
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    document.getElementById('deleteAssistantId').value = id;
    document.getElementById('assistantName').textContent = name;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

// Fechar modal ao clicar fora
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

// Fechar modal com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
});

// Adicionar estilo para a barra de progresso
const style = document.createElement('style');
style.textContent = `
    .progress {
        background-color: var(--gray-200);
        border-radius: 4px;
        overflow: hidden;
    }
    .progress-bar {
        transition: width 0.3s ease;
        border-radius: 4px;
    }
`;
document.head.appendChild(style);
</script>

