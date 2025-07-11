<?php
$page_title = 'Conversa #' . $conversation->getId();
$page_subtitle = 'Visualização detalhada da conversa entre assistentes';
?>

<div class="fade-in">
    <!-- Breadcrumb -->
    <nav class="mb-4">
        <div class="d-flex align-items-center gap-2 text-muted">
            <a href="<?= $base_url ?>/conversations" class="text-primary">
                <i class="fas fa-comments"></i>
                Conversas
            </a>
            <i class="fas fa-chevron-right" style="font-size: 0.75rem;"></i>
            <span>Conversa #<?= $conversation->getId() ?></span>
        </div>
    </nav>
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Cabeçalho da Conversa -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fas fa-comments text-primary"></i>
                            Conversa #<?= $conversation->getId() ?>
                        </h3>
                        
                        <div class="d-flex gap-2">
                            <span class="badge <?= $conversation->getStatusBadgeClass() ?> badge-lg">
                                <?= $conversation->getStatusLabel() ?>
                            </span>
                            
                            <div class="dropdown">
                                <button class="btn btn-outline btn-sm" data-toggle="dropdown">
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
                                        Excluir Conversa
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Participantes -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <div class="bg-primary text-white d-flex align-items-center justify-content-center" 
                                     style="width: 50px; height: 50px; border-radius: 50%; margin-right: 15px; font-weight: 600;">
                                    <?= strtoupper(substr($assistant1->getName(), 0, 2)) ?>
                                </div>
                                <div>
                                    <div class="font-weight-600"><?= htmlspecialchars($assistant1->getName()) ?></div>
                                    <div class="text-muted" style="font-size: 0.875rem;">Assistente 1</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                                     style="width: 50px; height: 50px; border-radius: 50%; margin-right: 15px; font-weight: 600;">
                                    <?= strtoupper(substr($assistant2->getName(), 0, 2)) ?>
                                </div>
                                <div>
                                    <div class="font-weight-600"><?= htmlspecialchars($assistant2->getName()) ?></div>
                                    <div class="text-muted" style="font-size: 0.875rem;">Assistente 2</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Informações da Conversa -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="text-muted mb-1">Início</div>
                                <div style="font-size: 0.875rem;">
                                    <?= date('d/m/Y H:i', strtotime($conversation->getStartTime())) ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="text-muted mb-1">Duração</div>
                                <div style="font-size: 0.875rem;">
                                    <?php 
                                    $duration = $conversation->getFormattedDuration();
                                    echo $duration ?: ($conversation->getStatus() === 'in_progress' ? 'Em andamento' : '-');
                                    ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="text-muted mb-1">Mensagens</div>
                                <div style="font-size: 0.875rem;">
                                    <span class="badge badge-info"><?= $message_count ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="text-muted mb-1">Status</div>
                                <div style="font-size: 0.875rem;">
                                    <?= $conversation->getStatusLabel() ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Mensagens -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-envelope text-primary"></i>
                        Histórico de Mensagens (<?= $message_count ?>)
                    </h3>
                </div>
                
                <div class="card-body" style="max-height: 600px; overflow-y: auto;" id="messagesContainer">
                    <?php if (empty($messages)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-envelope-open" style="font-size: 3rem; color: var(--gray-400); margin-bottom: 1rem;"></i>
                            <h4 class="text-muted mb-3">Nenhuma mensagem encontrada</h4>
                            <p class="text-muted">Esta conversa ainda não possui mensagens.</p>
                        </div>
                    <?php else: ?>
                        <div class="messages-timeline">
                            <?php foreach ($messages as $index => $message): ?>
                                <?php 
                                $sender = $message->getSender();
                                $isAssistant1 = $sender->getId() === $assistant1->getId();
                                ?>
                                
                                <div class="message-item <?= $isAssistant1 ? 'message-left' : 'message-right' ?>" 
                                     data-message-id="<?= $message->getId() ?>">
                                    
                                    <div class="message-avatar">
                                        <div class="<?= $isAssistant1 ? 'bg-primary' : 'bg-secondary' ?> text-white d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px; border-radius: 50%; font-weight: 600; font-size: 0.875rem;">
                                            <?= strtoupper(substr($sender->getName(), 0, 2)) ?>
                                        </div>
                                    </div>
                                    
                                    <div class="message-content">
                                        <div class="message-header">
                                            <div class="message-sender">
                                                <?= htmlspecialchars($sender->getName()) ?>
                                            </div>
                                            <div class="message-time">
                                                <?= date('H:i', strtotime($message->getTimestamp())) ?>
                                                <span class="text-muted">
                                                    - <?= date('d/m/Y', strtotime($message->getTimestamp())) ?>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="message-body">
                                            <?= nl2br(htmlspecialchars($message->getContent())) ?>
                                        </div>
                                        
                                        <div class="message-meta">
                                            <small class="text-muted">
                                                <?= $message->getWordCount() ?> palavras • 
                                                <?= $message->getCharacterCount() ?> caracteres
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Controles de Status -->
            <?php if ($conversation->getStatus() === 'in_progress'): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cog text-warning"></i>
                            Controles da Conversa
                        </h3>
                    </div>
                    
                    <div class="card-body">
                        <form method="POST" action="<?= $base_url ?>/conversations/update-status">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                            <input type="hidden" name="id" value="<?= $conversation->getId() ?>">
                            
                            <div class="form-group">
                                <label class="form-label">Marcar como:</label>
                                <div class="d-grid gap-2">
                                    <button type="submit" name="status" value="completed" class="btn btn-success">
                                        <i class="fas fa-check"></i>
                                        Concluída
                                    </button>
                                    
                                    <button type="submit" name="status" value="failed" class="btn btn-danger">
                                        <i class="fas fa-times"></i>
                                        Falhou
                                    </button>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="final_agreement" class="form-label">Acordo Final (opcional):</label>
                                <textarea name="final_agreement" 
                                          id="final_agreement" 
                                          class="form-control" 
                                          rows="3"
                                          placeholder="Descreva o acordo ou resultado final da conversa..."></textarea>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Acordo Final -->
            <?php if ($conversation->getFinalAgreement()): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-handshake text-success"></i>
                            Acordo Final
                        </h3>
                    </div>
                    
                    <div class="card-body">
                        <div class="bg-light p-3 rounded">
                            <?= nl2br(htmlspecialchars($conversation->getFinalAgreement())) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Estatísticas da Conversa -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar text-info"></i>
                        Estatísticas
                    </h3>
                </div>
                
                <div class="card-body">
                    <?php
                    $assistant1Messages = 0;
                    $assistant2Messages = 0;
                    $totalWords = 0;
                    $totalChars = 0;
                    
                    foreach ($messages as $message) {
                        if ($message->getSenderId() === $assistant1->getId()) {
                            $assistant1Messages++;
                        } else {
                            $assistant2Messages++;
                        }
                        $totalWords += $message->getWordCount();
                        $totalChars += $message->getCharacterCount();
                    }
                    ?>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span><?= htmlspecialchars($assistant1->getName()) ?>:</span>
                            <span class="badge badge-primary"><?= $assistant1Messages ?></span>
                        </div>
                        <div class="progress mb-2" style="height: 8px;">
                            <div class="progress-bar bg-primary" 
                                 style="width: <?= $message_count > 0 ? ($assistant1Messages / $message_count) * 100 : 0 ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span><?= htmlspecialchars($assistant2->getName()) ?>:</span>
                            <span class="badge badge-secondary"><?= $assistant2Messages ?></span>
                        </div>
                        <div class="progress mb-2" style="height: 8px;">
                            <div class="progress-bar bg-secondary" 
                                 style="width: <?= $message_count > 0 ? ($assistant2Messages / $message_count) * 100 : 0 ?>%"></div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="text-muted">Total de Palavras</div>
                            <div class="font-weight-600"><?= number_format($totalWords) ?></div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted">Total de Caracteres</div>
                            <div class="font-weight-600"><?= number_format($totalChars) ?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ações Rápidas -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt text-warning"></i>
                        Ações Rápidas
                    </h3>
                </div>
                
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= $base_url ?>/conversations" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i>
                            Voltar às Conversas
                        </a>
                        
                        <a href="<?= $base_url ?>/assistants/<?= $assistant1->getId() ?>" class="btn btn-outline">
                            <i class="fas fa-robot"></i>
                            Ver <?= htmlspecialchars($assistant1->getName()) ?>
                        </a>
                        
                        <a href="<?= $base_url ?>/assistants/<?= $assistant2->getId() ?>" class="btn btn-outline">
                            <i class="fas fa-robot"></i>
                            Ver <?= htmlspecialchars($assistant2->getName()) ?>
                        </a>
                        
                        <hr>
                        
                        <button type="button" 
                                class="btn btn-danger" 
                                onclick="confirmDelete(<?= $conversation->getId() ?>)">
                            <i class="fas fa-trash"></i>
                            Excluir Conversa
                        </button>
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

<style>
.messages-timeline {
    padding: 1rem 0;
}

.message-item {
    display: flex;
    margin-bottom: 2rem;
    animation: fadeIn 0.3s ease-out;
}

.message-left {
    flex-direction: row;
}

.message-right {
    flex-direction: row-reverse;
}

.message-avatar {
    flex-shrink: 0;
    margin: 0 1rem;
}

.message-content {
    flex: 1;
    max-width: 70%;
}

.message-left .message-content {
    background: var(--gray-100);
    border-radius: 0 1rem 1rem 1rem;
}

.message-right .message-content {
    background: var(--primary-color);
    color: var(--white);
    border-radius: 1rem 0 1rem 1rem;
}

.message-header {
    padding: 0.75rem 1rem 0.5rem;
    border-bottom: 1px solid rgba(0,0,0,0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.message-sender {
    font-weight: 600;
    font-size: 0.875rem;
}

.message-time {
    font-size: 0.75rem;
    opacity: 0.8;
}

.message-body {
    padding: 1rem;
    line-height: 1.6;
}

.message-meta {
    padding: 0.5rem 1rem;
    border-top: 1px solid rgba(0,0,0,0.1);
    font-size: 0.75rem;
    opacity: 0.8;
}

.message-right .message-header,
.message-right .message-meta {
    border-color: rgba(255,255,255,0.2);
}

.progress {
    background-color: var(--gray-200);
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar {
    transition: width 0.3s ease;
    border-radius: 4px;
}

.badge-lg {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}

/* Dropdown styles */
.dropdown {
    position: relative;
}
.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    right: 0;
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-md);
    padding: 0.5rem 0;
    min-width: 150px;
    z-index: 1000;
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
</style>

<script>
// Dropdown functionality
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
            menu.style.display = 'block';
        }
    }
});

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

// Auto-scroll para a última mensagem
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('messagesContainer');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
});

// Auto-resize para textarea
const textarea = document.getElementById('final_agreement');
if (textarea) {
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
}
</script>

