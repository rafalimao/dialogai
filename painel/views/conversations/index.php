<?php
$page_title = 'Conversas';
$page_subtitle = 'Visualize e gerencie as conversas entre assistentes';
?>

<div class="fade-in">
    <!-- Header da seção -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-2">Lista de Conversas</h2>
            <p class="text-muted">Total: <?= $total ?> conversa<?= $total !== 1 ? 's' : '' ?></p>
        </div>
        
        <div class="d-flex gap-2">
            <a href="<?= $base_url ?>/conversations/stats" class="btn btn-outline">
                <i class="fas fa-chart-bar"></i>
                Estatísticas
            </a>
        </div>
    </div>
    
    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?= $base_url ?>/conversations" class="row align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">Todos os status</option>
                        <option value="in_progress" <?= $filters['status'] === 'in_progress' ? 'selected' : '' ?>>
                            Em Progresso
                        </option>
                        <option value="completed" <?= $filters['status'] === 'completed' ? 'selected' : '' ?>>
                            Concluída
                        </option>
                        <option value="failed" <?= $filters['status'] === 'failed' ? 'selected' : '' ?>>
                            Falhou
                        </option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Assistente</label>
                    <select name="assistant" class="form-control">
                        <option value="">Todos os assistentes</option>
                        <?php foreach ($assistants as $assistant): ?>
                            <option value="<?= $assistant->getId() ?>" <?= $filters['assistant'] == $assistant->getId() ? 'selected' : '' ?>>
                                <?= htmlspecialchars($assistant->getName()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-filter"></i>
                            Filtrar
                        </button>
                        
                        <a href="<?= $base_url ?>/conversations" class="btn btn-outline">
                            <i class="fas fa-times"></i>
                            Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Resumo de Status -->
    <?php if (!empty($conversations)): ?>
        <div class="row mb-4">
            <?php
            $statusCounts = [
                'in_progress' => 0,
                'completed' => 0,
                'failed' => 0
            ];
            
            foreach ($conversations as $conv) {
                $statusCounts[$conv->getStatus()]++;
            }
            ?>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div style="font-size: 2rem; font-weight: 700; color: var(--warning-color);">
                            <?= $statusCounts['in_progress'] ?>
                        </div>
                        <div class="text-muted">Em Progresso</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div style="font-size: 2rem; font-weight: 700; color: var(--success-color);">
                            <?= $statusCounts['completed'] ?>
                        </div>
                        <div class="text-muted">Concluídas</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div style="font-size: 2rem; font-weight: 700; color: var(--danger-color);">
                            <?= $statusCounts['failed'] ?>
                        </div>
                        <div class="text-muted">Falharam</div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (empty($conversations)): ?>
        <!-- Estado vazio -->
        <div class="card">
            <div class="card-body text-center" style="padding: 3rem;">
                <i class="fas fa-comments" style="font-size: 4rem; color: var(--gray-400); margin-bottom: 1rem;"></i>
                <h3 class="text-muted mb-3">Nenhuma conversa encontrada</h3>
                <p class="text-muted mb-4">
                    <?= !empty($filters['status']) || !empty($filters['assistant']) 
                        ? 'Tente ajustar os filtros para encontrar conversas.' 
                        : 'As conversas aparecerão aqui quando os assistentes começarem a interagir.' ?>
                </p>
                
                <?php if (!empty($filters['status']) || !empty($filters['assistant'])): ?>
                    <a href="<?= $base_url ?>/conversations" class="btn btn-primary">
                        <i class="fas fa-times"></i>
                        Limpar Filtros
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <!-- Lista de Conversas -->
        <div class="card">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Conversa</th>
                            <th>Participantes</th>
                            <th>Status</th>
                            <th>Início</th>
                            <th>Duração</th>
                            <th>Mensagens</th>
                            <th width="150">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($conversations as $conversation): ?>
                            <tr>
                                <td>
                                    <div>
                                        <strong>#<?= $conversation->getId() ?></strong>
                                        <?php if ($conversation->getFinalAgreement()): ?>
                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                <i class="fas fa-handshake text-success"></i>
                                                Com acordo
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                
                                <td>
                                    <?php 
                                    $assistant1 = $conversation->getAssistant1();
                                    $assistant2 = $conversation->getAssistant2();
                                    ?>
                                    <div style="font-size: 0.875rem;">
                                        <div class="mb-1">
                                            <i class="fas fa-robot text-primary" style="font-size: 0.75rem;"></i>
                                            <?= htmlspecialchars($assistant1->getName()) ?>
                                        </div>
                                        <div class="text-muted" style="font-size: 0.75rem;">vs</div>
                                        <div>
                                            <i class="fas fa-robot text-secondary" style="font-size: 0.75rem;"></i>
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
                                    <?php elseif ($conversation->getStatus() === 'in_progress'): ?>
                                        <span class="text-warning" style="font-size: 0.875rem;">
                                            <i class="fas fa-clock"></i>
                                            Em andamento
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td>
                                    <span class="badge badge-info">
                                        <?= $conversation->getMessageCount() ?>
                                    </span>
                                </td>
                                
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="<?= $base_url ?>/conversations/<?= $conversation->getId() ?>" 
                                           class="btn btn-sm btn-outline" 
                                           title="Ver Conversa">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline" 
                                                    data-toggle="dropdown" 
                                                    title="Mais opções">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a href="<?= $base_url ?>/conversations/<?= $conversation->getId() ?>/export?format=json" 
                                                   class="dropdown-item">
                                                    <i class="fas fa-download"></i>
                                                    Exportar JSON
                                                </a>
                                                <a href="<?= $base_url ?>/conversations/<?= $conversation->getId() ?>/export?format=txt" 
                                                   class="dropdown-item">
                                                    <i class="fas fa-file-alt"></i>
                                                    Exportar TXT
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <button type="button" 
                                                        class="dropdown-item text-danger"
                                                        onclick="confirmDelete(<?= $conversation->getId() ?>)">
                                                    <i class="fas fa-trash"></i>
                                                    Excluir
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Paginação -->
        <?php if ($pagination['total_pages'] > 1): ?>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Mostrando <?= count($conversations) ?> de <?= $pagination['total'] ?> registros
                </div>
                
                <div class="d-flex gap-2">
                    <?php if ($pagination['has_prev']): ?>
                        <a href="<?= $base_url ?>/conversations?page=<?= $pagination['prev_page'] ?><?= http_build_query($filters) ? '&' . http_build_query($filters) : '' ?>" 
                           class="btn btn-outline btn-sm">
                            <i class="fas fa-chevron-left"></i>
                            Anterior
                        </a>
                    <?php endif; ?>
                    
                    <?php 
                    $startPage = max(1, $pagination['current_page'] - 2);
                    $endPage = min($pagination['total_pages'], $pagination['current_page'] + 2);
                    ?>
                    
                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <a href="<?= $base_url ?>/conversations?page=<?= $i ?><?= http_build_query($filters) ? '&' . http_build_query($filters) : '' ?>" 
                           class="btn btn-sm <?= $i === $pagination['current_page'] ? 'btn-primary' : 'btn-outline' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($pagination['has_next']): ?>
                        <a href="<?= $base_url ?>/conversations?page=<?= $pagination['next_page'] ?><?= http_build_query($filters) ? '&' . http_build_query($filters) : '' ?>" 
                           class="btn btn-outline btn-sm">
                            Próximo
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
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
            <p>Tem certeza que deseja excluir esta conversa?</p>
            <p class="text-muted">Esta ação não pode ser desfeita e removerá todas as mensagens associadas.</p>
        </div>
        <div class="card-body" style="border-top: 1px solid var(--gray-200); padding-top: 1rem;">
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline" onclick="closeDeleteModal()">
                    Cancelar
                </button>
                <form method="POST" action="<?= $base_url ?>/conversations/delete" style="display: inline;">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    <input type="hidden" name="id" id="deleteConversationId">
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
// Dropdown simples
document.addEventListener('click', function(e) {
    // Fechar todos os dropdowns
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.style.display = 'none';
    });
    
    // Abrir dropdown clicado
    if (e.target.matches('[data-toggle="dropdown"]') || e.target.closest('[data-toggle="dropdown"]')) {
        e.preventDefault();
        const button = e.target.matches('[data-toggle="dropdown"]') ? e.target : e.target.closest('[data-toggle="dropdown"]');
        const menu = button.parentElement.querySelector('.dropdown-menu');
        if (menu) {
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
            menu.style.position = 'absolute';
            menu.style.top = '100%';
            menu.style.left = '0';
            menu.style.backgroundColor = 'var(--white)';
            menu.style.border = '1px solid var(--gray-200)';
            menu.style.borderRadius = 'var(--border-radius)';
            menu.style.boxShadow = 'var(--shadow-md)';
            menu.style.padding = '0.5rem 0';
            menu.style.minWidth = '150px';
            menu.style.zIndex = '1000';
        }
    }
});

// Estilo para dropdown items
const style = document.createElement('style');
style.textContent = `
    .dropdown {
        position: relative;
    }
    .dropdown-menu {
        display: none;
    }
    .dropdown-item {
        display: block;
        padding: 0.5rem 1rem;
        color: var(--gray-700);
        text-decoration: none;
        font-size: 0.875rem;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        cursor: pointer;
    }
    .dropdown-item:hover {
        background-color: var(--gray-100);
        color: var(--primary-color);
    }
    .dropdown-item.text-danger:hover {
        background-color: var(--danger-color);
        color: var(--white);
    }
    .dropdown-divider {
        height: 1px;
        background-color: var(--gray-200);
        margin: 0.5rem 0;
    }
`;
document.head.appendChild(style);

function confirmDelete(id) {
    document.getElementById('deleteConversationId').value = id;
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
</script>

