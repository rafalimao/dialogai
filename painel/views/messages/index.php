<?php
$page_title = 'Mensagens';
$page_subtitle = 'Visualize todas as mensagens trocadas entre assistentes';
?>

<div class="fade-in">
    <!-- Header da seção -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-2">Lista de Mensagens</h2>
            <p class="text-muted">Total: <?= $total ?> mensagem<?= $total !== 1 ? 's' : '' ?></p>
        </div>
        
        <div class="d-flex gap-2">
            <?php if ($conversation_id > 0): ?>
                <a href="<?= $base_url ?>/conversations/<?= $conversation_id ?>" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i>
                    Voltar à Conversa
                </a>
            <?php endif; ?>
            
            <a href="<?= $base_url ?>/conversations" class="btn btn-outline">
                <i class="fas fa-comments"></i>
                Ver Conversas
            </a>
        </div>
    </div>
    
    <?php if (empty($messages)): ?>
        <!-- Estado vazio -->
        <div class="card">
            <div class="card-body text-center" style="padding: 3rem;">
                <i class="fas fa-envelope-open" style="font-size: 4rem; color: var(--gray-400); margin-bottom: 1rem;"></i>
                <h3 class="text-muted mb-3">Nenhuma mensagem encontrada</h3>
                <p class="text-muted mb-4">
                    <?= $conversation_id > 0 
                        ? 'Esta conversa ainda não possui mensagens.' 
                        : 'As mensagens aparecerão aqui quando os assistentes começarem a conversar.' ?>
                </p>
                
                <a href="<?= $base_url ?>/conversations" class="btn btn-primary">
                    <i class="fas fa-comments"></i>
                    Ver Conversas
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Lista de Mensagens -->
        <div class="card">
            <div class="card-body">
                <div class="messages-list">
                    <?php foreach ($messages as $message): ?>
                        <?php 
                        $sender = $message->getSender();
                        $recipient = $message->getRecipient();
                        $conversation = $message->getConversation();
                        ?>
                        
                        <div class="message-item" data-message-id="<?= $message->getId() ?>">
                            <div class="message-header">
                                <div class="d-flex align-items-center">
                                    <div class="message-avatar bg-primary text-white">
                                        <?= strtoupper(substr($sender->getName(), 0, 2)) ?>
                                    </div>
                                    
                                    <div class="message-info">
                                        <div class="message-sender">
                                            <?= htmlspecialchars($sender->getName()) ?>
                                        </div>
                                        <div class="message-meta">
                                            <span class="text-muted">para</span>
                                            <?= htmlspecialchars($recipient->getName()) ?>
                                            <span class="text-muted">•</span>
                                            <a href="<?= $base_url ?>/conversations/<?= $conversation->getId() ?>" 
                                               class="text-primary">
                                                Conversa #<?= $conversation->getId() ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="message-time">
                                    <?= date('d/m/Y H:i', strtotime($message->getTimestamp())) ?>
                                </div>
                            </div>
                            
                            <div class="message-content">
                                <div class="message-text">
                                    <?= nl2br(htmlspecialchars(substr($message->getContent(), 0, 300))) ?>
                                    <?php if (strlen($message->getContent()) > 300): ?>
                                        <span class="text-muted">...</span>
                                        <a href="<?= $base_url ?>/messages/<?= $message->getId() ?>" 
                                           class="text-primary">
                                            Ver completa
                                        </a>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="message-stats">
                                    <small class="text-muted">
                                        <?= $message->getWordCount() ?> palavras • 
                                        <?= $message->getCharacterCount() ?> caracteres
                                    </small>
                                </div>
                            </div>
                            
                            <div class="message-actions">
                                <a href="<?= $base_url ?>/messages/<?= $message->getId() ?>" 
                                   class="btn btn-sm btn-outline" 
                                   title="Ver mensagem completa">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <a href="<?= $base_url ?>/conversations/<?= $conversation->getId() ?>" 
                                   class="btn btn-sm btn-outline" 
                                   title="Ver conversa">
                                    <i class="fas fa-comments"></i>
                                </a>
                                
                                <button type="button" 
                                        class="btn btn-sm btn-outline text-danger" 
                                        onclick="confirmDelete(<?= $message->getId() ?>)"
                                        title="Excluir mensagem">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Paginação -->
        <?php if ($pagination['total_pages'] > 1): ?>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Mostrando <?= count($messages) ?> de <?= $pagination['total'] ?> registros
                </div>
                
                <div class="d-flex gap-2">
                    <?php if ($pagination['has_prev']): ?>
                        <a href="<?= $base_url ?>/messages?page=<?= $pagination['prev_page'] ?><?= $conversation_id ? '&conversation=' . $conversation_id : '' ?>" 
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
                        <a href="<?= $base_url ?>/messages?page=<?= $i ?><?= $conversation_id ? '&conversation=' . $conversation_id : '' ?>" 
                           class="btn btn-sm <?= $i === $pagination['current_page'] ? 'btn-primary' : 'btn-outline' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($pagination['has_next']): ?>
                        <a href="<?= $base_url ?>/messages?page=<?= $pagination['next_page'] ?><?= $conversation_id ? '&conversation=' . $conversation_id : '' ?>" 
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
            <p>Tem certeza que deseja excluir esta mensagem?</p>
            <p class="text-muted">Esta ação não pode ser desfeita.</p>
        </div>
        <div class="card-body" style="border-top: 1px solid var(--gray-200); padding-top: 1rem;">
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline" onclick="closeDeleteModal()">
                    Cancelar
                </button>
                <form method="POST" action="<?= $base_url ?>/messages/delete" style="display: inline;">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    <input type="hidden" name="id" id="deleteMessageId">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i>
                        Excluir
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.messages-list {
    max-height: 70vh;
    overflow-y: auto;
}

.message-item {
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    margin-bottom: 1rem;
    background: var(--white);
    transition: all 0.2s ease;
}

.message-item:hover {
    border-color: var(--primary-color);
    box-shadow: var(--shadow-sm);
}

.message-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid var(--gray-200);
    background: var(--gray-50);
}

.message-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
    margin-right: 1rem;
}

.message-info {
    flex: 1;
}

.message-sender {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.message-meta {
    font-size: 0.875rem;
    color: var(--gray-600);
}

.message-time {
    font-size: 0.875rem;
    color: var(--gray-600);
}

.message-content {
    padding: 1rem;
}

.message-text {
    line-height: 1.6;
    margin-bottom: 0.5rem;
}

.message-stats {
    margin-bottom: 1rem;
}

.message-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
    padding: 0 1rem 1rem;
}

@media (max-width: 768px) {
    .message-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .message-actions {
        justify-content: center;
    }
}
</style>

<script>
function confirmDelete(id) {
    document.getElementById('deleteMessageId').value = id;
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

// Animação de entrada
document.addEventListener('DOMContentLoaded', function() {
    const messageItems = document.querySelectorAll('.message-item');
    messageItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            item.style.transition = 'all 0.3s ease';
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>

