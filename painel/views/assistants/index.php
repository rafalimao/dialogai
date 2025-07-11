<?php
$page_title = 'Assistentes';
$page_subtitle = 'Gerencie os assistentes do sistema';
?>

<div class="fade-in">
    <!-- Header da seção -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-2">Lista de Assistentes</h2>
            <p class="text-muted">Total: <?= $total ?> assistente<?= $total !== 1 ? 's' : '' ?></p>
        </div>
        
        <div class="d-flex gap-2">
            <a href="<?= $base_url ?>/index.php?route=assistants&action=create" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Novo Assistente
            </a>
        </div>
    </div>
    
    <!-- Filtros e Busca -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?= $base_url ?>/index.php" class="d-flex gap-3 align-items-center">
                <input type="hidden" name="route" value="assistants">
                <div class="form-group mb-0" style="flex: 1;">
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Buscar por nome do assistente..."
                           value="<?= htmlspecialchars($search) ?>">
                </div>
                
                <button type="submit" class="btn btn-secondary">
                    <i class="fas fa-search"></i>
                    Buscar
                </button>
                
                <?php if (!empty($search)): ?>
                    <a href="<?= $base_url ?>/index.php?route=assistants" class="btn btn-outline">
                        <i class="fas fa-times"></i>
                        Limpar
                    </a>
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <?php if (empty($assistants)): ?>
        <!-- Estado vazio -->
        <div class="card">
            <div class="card-body text-center" style="padding: 3rem;">
                <i class="fas fa-robot" style="font-size: 4rem; color: var(--gray-400); margin-bottom: 1rem;"></i>
                <h3 class="text-muted mb-3">
                    <?= !empty($search) ? 'Nenhum assistente encontrado' : 'Nenhum assistente cadastrado' ?>
                </h3>
                <p class="text-muted mb-4">
                    <?= !empty($search) 
                        ? 'Tente ajustar os termos de busca ou limpar os filtros.' 
                        : 'Comece criando seu primeiro assistente para iniciar as conversas automatizadas.' ?>
                </p>
                
                <?php if (empty($search)): ?>
                    <a href="<?= $base_url ?>/index.php?route=assistants&action=create" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Criar Primeiro Assistente
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <!-- Lista de Assistentes -->
        <div class="card">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Perfil</th>
                            <th>Conversas</th>
                            <th>Última Atividade</th>
                            <th width="200">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assistants as $assistant): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary text-white d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px; border-radius: 50%; margin-right: 12px; font-weight: 600;">
                                            <?= strtoupper(substr($assistant->getName(), 0, 2)) ?>
                                        </div>
                                        <div>
                                            <div class="font-weight-600"><?= htmlspecialchars($assistant->getName()) ?></div>
                                            <div class="text-muted" style="font-size: 0.875rem;">
                                                ID: <?= $assistant->getId() ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td>
                                    <div style="max-width: 300px;">
                                        <?= htmlspecialchars(substr($assistant->getProfile(), 0, 100)) ?>
                                        <?= strlen($assistant->getProfile()) > 100 ? '...' : '' ?>
                                    </div>
                                </td>
                                
                                <td>
                                    <span class="badge badge-info">
                                        <?= $assistant->getConversationCount() ?>
                                    </span>
                                </td>
                                
                                <td>
                                    <?php 
                                    $lastConversation = $assistant->getLastConversationDate();
                                    if ($lastConversation): 
                                    ?>
                                        <div style="font-size: 0.875rem;">
                                            <?= date('d/m/Y H:i', strtotime($lastConversation)) ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">Nunca</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="<?= $base_url ?>/index.php?route=assistants&action=show&id=<?= $assistant->getId() ?>" 
                                           class="btn btn-sm btn-outline" 
                                           title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <a href="<?= $base_url ?>/index.php?route=assistants&action=edit&id=<?= $assistant->getId() ?>" 
                                           class="btn btn-sm btn-secondary" 
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <button type="button" 
                                                class="btn btn-sm btn-danger" 
                                                onclick="confirmDelete(<?= $assistant->getId() ?>, '<?= htmlspecialchars($assistant->getName(), ENT_QUOTES) ?>')"
                                                title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
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
                    Mostrando <?= count($assistants) ?> de <?= $pagination['total'] ?> registros
                </div>
                
                <div class="d-flex gap-2">
                    <?php if ($pagination['has_prev']): ?>
                        <a href="<?= $base_url ?>/index.php?route=assistants&page=<?= $pagination['prev_page'] ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
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
                        <a href="<?= $base_url ?>/index.php?route=assistants&page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                           class="btn btn-sm <?= $i === $pagination['current_page'] ? 'btn-primary' : 'btn-outline' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($pagination['has_next']): ?>
                        <a href="<?= $base_url ?>/index.php?route=assistants&page=<?= $pagination['next_page'] ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
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
            <p>Tem certeza que deseja excluir o assistente <strong id="assistantName"></strong>?</p>
            <p class="text-muted">Esta ação não pode ser desfeita.</p>
        </div>
        <div class="card-body" style="border-top: 1px solid var(--gray-200); padding-top: 1rem;">
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline" onclick="closeDeleteModal()">
                    Cancelar
                </button>
                <form method="POST" action="<?= $base_url ?>/index.php?route=assistants&action=delete" style="display: inline;">
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
</script>

