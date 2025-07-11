<?php
$page_title = 'Editar Assistente';
$page_subtitle = 'Atualize as informações do assistente';

// Usar dados do formulário se houver erro, senão usar dados do assistente
$data = $form_data ?? $assistant->toArray();
$errors = $form_errors ?? [];
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
            <a href="<?= $base_url ?>/assistants/<?= $assistant->getId() ?>" class="text-primary">
                <?= htmlspecialchars($assistant->getName()) ?>
            </a>
            <i class="fas fa-chevron-right" style="font-size: 0.75rem;"></i>
            <span>Editar</span>
        </div>
    </nav>
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Formulário Principal -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit text-primary"></i>
                        Editar Informações do Assistente
                    </h3>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="<?= $base_url ?>/assistants/<?= $assistant->getId() ?>/edit" id="assistantForm">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        
                        <!-- Nome -->
                        <div class="form-group">
                            <label for="name" class="form-label">
                                Nome do Assistente *
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                                   placeholder="Ex: Assistente de Vendas, Suporte Técnico..."
                                   value="<?= htmlspecialchars($data['name'] ?? '') ?>"
                                   maxlength="255"
                                   required>
                            
                            <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($errors['name']) ?>
                                </div>
                            <?php endif; ?>
                            
                            <small class="text-muted">
                                Escolha um nome descritivo e único para identificar o assistente.
                            </small>
                        </div>
                        
                        <!-- Perfil -->
                        <div class="form-group">
                            <label for="profile" class="form-label">
                                Perfil e Características *
                            </label>
                            <textarea id="profile" 
                                      name="profile" 
                                      class="form-control <?= isset($errors['profile']) ? 'is-invalid' : '' ?>" 
                                      rows="4"
                                      placeholder="Descreva o perfil, personalidade e características do assistente..."
                                      required><?= htmlspecialchars($data['profile'] ?? '') ?></textarea>
                            
                            <?php if (isset($errors['profile'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($errors['profile']) ?>
                                </div>
                            <?php endif; ?>
                            
                            <small class="text-muted">
                                Defina como o assistente deve se comportar, seu estilo de comunicação e características principais.
                            </small>
                        </div>
                        
                        <!-- Prompt Inicial -->
                        <div class="form-group">
                            <label for="initial_prompt" class="form-label">
                                Prompt Inicial *
                            </label>
                            <textarea id="initial_prompt" 
                                      name="initial_prompt" 
                                      class="form-control <?= isset($errors['initial_prompt']) ? 'is-invalid' : '' ?>" 
                                      rows="5"
                                      placeholder="Instruções iniciais que o assistente receberá ao iniciar uma conversa..."
                                      required><?= htmlspecialchars($data['initial_prompt'] ?? '') ?></textarea>
                            
                            <?php if (isset($errors['initial_prompt'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($errors['initial_prompt']) ?>
                                </div>
                            <?php endif; ?>
                            
                            <small class="text-muted">
                                Instruções detalhadas sobre como o assistente deve iniciar e conduzir as conversas.
                            </small>
                        </div>
                        
                        <!-- Objetivo -->
                        <div class="form-group">
                            <label for="goal" class="form-label">
                                Objetivo Final *
                            </label>
                            <textarea id="goal" 
                                      name="goal" 
                                      class="form-control <?= isset($errors['goal']) ? 'is-invalid' : '' ?>" 
                                      rows="3"
                                      placeholder="Qual é o objetivo que o assistente deve alcançar nas conversas..."
                                      required><?= htmlspecialchars($data['goal'] ?? '') ?></textarea>
                            
                            <?php if (isset($errors['goal'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($errors['goal']) ?>
                                </div>
                            <?php endif; ?>
                            
                            <small class="text-muted">
                                Defina claramente qual resultado ou acordo o assistente deve buscar alcançar.
                            </small>
                        </div>
                        
                        <!-- Botões de Ação -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="<?= $base_url ?>/assistants/<?= $assistant->getId() ?>" class="btn btn-outline">
                                <i class="fas fa-arrow-left"></i>
                                Voltar
                            </a>
                            
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                    <i class="fas fa-undo"></i>
                                    Restaurar
                                </button>
                                
                                <button type="submit" class="btn btn-success" id="submitBtn">
                                    <i class="fas fa-save"></i>
                                    Salvar Alterações
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Informações do Assistente -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle text-info"></i>
                        Informações do Assistente
                    </h3>
                </div>
                
                <div class="card-body">
                    <div class="mb-3">
                        <strong>ID:</strong><br>
                        <span class="text-muted">#<?= $assistant->getId() ?></span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Conversas Participadas:</strong><br>
                        <span class="badge badge-info"><?= $assistant->getConversationCount() ?></span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Última Atividade:</strong><br>
                        <?php 
                        $lastConversation = $assistant->getLastConversationDate();
                        if ($lastConversation): 
                        ?>
                            <span class="text-muted">
                                <?= date('d/m/Y H:i', strtotime($lastConversation)) ?>
                            </span>
                        <?php else: ?>
                            <span class="text-muted">Nunca</span>
                        <?php endif; ?>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex gap-2">
                        <a href="<?= $base_url ?>/assistants/<?= $assistant->getId() ?>" class="btn btn-outline btn-sm">
                            <i class="fas fa-eye"></i>
                            Visualizar
                        </a>
                        
                        <button type="button" 
                                class="btn btn-danger btn-sm" 
                                onclick="confirmDelete(<?= $assistant->getId() ?>, '<?= htmlspecialchars($assistant->getName(), ENT_QUOTES) ?>')">
                            <i class="fas fa-trash"></i>
                            Excluir
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Histórico de Alterações -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history text-warning"></i>
                        Dicas de Edição
                    </h3>
                </div>
                
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Atenção:</strong> Alterações no perfil e prompts podem afetar conversas futuras. Teste as mudanças antes de aplicar em produção.
                    </div>
                    
                    <ul style="font-size: 0.875rem; margin: 0; padding-left: 1.2rem;">
                        <li class="mb-2">Mantenha a consistência do perfil</li>
                        <li class="mb-2">Teste prompts com diferentes cenários</li>
                        <li class="mb-2">Documente mudanças importantes</li>
                        <li class="mb-0">Monitore o desempenho após alterações</li>
                    </ul>
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
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
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
// Dados originais para restaurar
const originalData = {
    name: <?= json_encode($assistant->getName()) ?>,
    profile: <?= json_encode($assistant->getProfile()) ?>,
    initial_prompt: <?= json_encode($assistant->getInitialPrompt()) ?>,
    goal: <?= json_encode($assistant->getGoal()) ?>
};

// Validação do formulário
document.getElementById('assistantForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    
    // Mostrar loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="loading"></span> Salvando...';
    
    // Validação básica
    const name = document.getElementById('name').value.trim();
    const profile = document.getElementById('profile').value.trim();
    const initialPrompt = document.getElementById('initial_prompt').value.trim();
    const goal = document.getElementById('goal').value.trim();
    
    if (!name || !profile || !initialPrompt || !goal) {
        e.preventDefault();
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        
        alert('Por favor, preencha todos os campos obrigatórios.');
        return;
    }
    
    // Se chegou até aqui, o formulário será enviado
    // O loading será mantido até a página recarregar
});

// Função para restaurar dados originais
function resetForm() {
    if (confirm('Tem certeza que deseja restaurar os dados originais?')) {
        document.getElementById('name').value = originalData.name;
        document.getElementById('profile').value = originalData.profile;
        document.getElementById('initial_prompt').value = originalData.initial_prompt;
        document.getElementById('goal').value = originalData.goal;
        
        // Remover classes de erro
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        
        // Trigger auto-resize
        document.querySelectorAll('textarea').forEach(textarea => {
            textarea.style.height = 'auto';
            textarea.style.height = (textarea.scrollHeight) + 'px';
        });
    }
}

// Modal de exclusão
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

// Auto-resize para textareas
document.querySelectorAll('textarea').forEach(textarea => {
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
    
    // Aplicar auto-resize inicial
    textarea.style.height = 'auto';
    textarea.style.height = (textarea.scrollHeight) + 'px';
});

// Contador de caracteres para o nome
const nameInput = document.getElementById('name');
const nameGroup = nameInput.closest('.form-group');

nameInput.addEventListener('input', function() {
    const length = this.value.length;
    const maxLength = 255;
    
    // Remover contador existente
    const existingCounter = nameGroup.querySelector('.char-counter');
    if (existingCounter) {
        existingCounter.remove();
    }
    
    // Adicionar novo contador
    if (length > 0) {
        const counter = document.createElement('small');
        counter.className = 'char-counter text-muted d-block mt-1';
        counter.textContent = `${length}/${maxLength} caracteres`;
        
        if (length > maxLength * 0.9) {
            counter.className = 'char-counter text-warning d-block mt-1';
        }
        
        nameGroup.appendChild(counter);
    }
});

// Detectar mudanças no formulário
let hasChanges = false;
const form = document.getElementById('assistantForm');
const inputs = form.querySelectorAll('input, textarea');

inputs.forEach(input => {
    input.addEventListener('input', function() {
        hasChanges = true;
    });
});

// Avisar sobre mudanças não salvas
window.addEventListener('beforeunload', function(e) {
    if (hasChanges) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// Não avisar ao submeter o formulário
form.addEventListener('submit', function() {
    hasChanges = false;
});
</script>

