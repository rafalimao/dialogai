<?php
$page_title = 'Novo Assistente';
$page_subtitle = 'Cadastre um novo assistente no sistema';

// Recuperar dados do formulário em caso de erro
$formData = $_SESSION['form_data'] ?? [];
$formErrors = $_SESSION['form_errors'] ?? [];
unset($_SESSION['form_data'], $_SESSION['form_errors']);
?>

<div class="fade-in">
    <!-- Breadcrumb -->
    <nav class="mb-4">
        <div class="d-flex align-items-center gap-2 text-muted">
            <a href="<?= $base_url ?>/index.php?route=assistants" class="text-primary">
                <i class="fas fa-robot"></i>
                Assistentes
            </a>
            <i class="fas fa-chevron-right" style="font-size: 0.75rem;"></i>
            <span>Novo Assistente</span>
        </div>
    </nav>
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Formulário Principal -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus-circle text-primary"></i>
                        Informações do Assistente
                    </h3>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="<?= $base_url ?>/index.php?route=assistants&action=store" id="assistantForm">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        
                        <!-- Nome -->
                        <div class="form-group">
                            <label for="name" class="form-label">
                                Nome do Assistente *
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   class="form-control <?= isset($formErrors['name']) ? 'is-invalid' : '' ?>" 
                                   placeholder="Ex: Assistente de Vendas, Suporte Técnico..."
                                   value="<?= htmlspecialchars($formData['name'] ?? '') ?>"
                                   maxlength="255"
                                   required>
                            
                            <?php if (isset($formErrors['name'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($formErrors['name']) ?>
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
                                      class="form-control <?= isset($formErrors['profile']) ? 'is-invalid' : '' ?>" 
                                      rows="4"
                                      placeholder="Descreva o perfil, personalidade e características do assistente..."
                                      required><?= htmlspecialchars($formData['profile'] ?? '') ?></textarea>
                            
                            <?php if (isset($formErrors['profile'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($formErrors['profile']) ?>
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
                                      class="form-control <?= isset($formErrors['initial_prompt']) ? 'is-invalid' : '' ?>" 
                                      rows="5"
                                      placeholder="Instruções iniciais que o assistente receberá ao iniciar uma conversa..."
                                      required><?= htmlspecialchars($formData['initial_prompt'] ?? '') ?></textarea>
                            
                            <?php if (isset($formErrors['initial_prompt'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($formErrors['initial_prompt']) ?>
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
                                      class="form-control <?= isset($formErrors['goal']) ? 'is-invalid' : '' ?>" 
                                      rows="3"
                                      placeholder="Qual é o objetivo que o assistente deve alcançar nas conversas..."
                                      required><?= htmlspecialchars($formData['goal'] ?? '') ?></textarea>
                            
                            <?php if (isset($formErrors['goal'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($formErrors['goal']) ?>
                                </div>
                            <?php endif; ?>
                            
                            <small class="text-muted">
                                Defina claramente qual resultado ou acordo o assistente deve buscar alcançar.
                            </small>
                        </div>
                        
                        <!-- Botões de Ação -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="<?= $base_url ?>/index.php?route=assistants" class="btn btn-outline">
                                <i class="fas fa-arrow-left"></i>
                                Voltar
                            </a>
                            
                            <div class="d-flex gap-2">
                                <button type="reset" class="btn btn-secondary" onclick="resetForm()">
                                    <i class="fas fa-undo"></i>
                                    Limpar
                                </button>
                                
                                <button type="submit" class="btn btn-success" id="submitBtn">
                                    <i class="fas fa-save"></i>
                                    Salvar Assistente
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Dicas e Ajuda -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-lightbulb text-warning"></i>
                        Dicas para Criar Assistentes
                    </h3>
                </div>
                
                <div class="card-body">
                    <div class="mb-3">
                        <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem;">
                            <i class="fas fa-user-tag text-primary"></i>
                            Nome
                        </h4>
                        <p style="font-size: 0.875rem; margin-bottom: 1rem;">
                            Use nomes descritivos que indiquem a função ou especialidade do assistente.
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem;">
                            <i class="fas fa-mask text-primary"></i>
                            Perfil
                        </h4>
                        <p style="font-size: 0.875rem; margin-bottom: 1rem;">
                            Defina personalidade, tom de voz, nível de formalidade e características comportamentais.
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem;">
                            <i class="fas fa-play-circle text-primary"></i>
                            Prompt Inicial
                        </h4>
                        <p style="font-size: 0.875rem; margin-bottom: 1rem;">
                            Instruções claras sobre como iniciar conversas e quais informações coletar primeiro.
                        </p>
                    </div>
                    
                    <div class="mb-0">
                        <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem;">
                            <i class="fas fa-target text-primary"></i>
                            Objetivo
                        </h4>
                        <p style="font-size: 0.875rem; margin-bottom: 0;">
                            Seja específico sobre o resultado desejado: venda, suporte, coleta de dados, etc.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Exemplos -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-code text-info"></i>
                        Exemplo de Assistente
                    </h3>
                </div>
                
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Nome:</strong><br>
                        <span class="text-muted">Assistente de Vendas Premium</span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Perfil:</strong><br>
                        <span class="text-muted" style="font-size: 0.875rem;">
                            Profissional experiente, consultivo, focado em entender necessidades antes de apresentar soluções...
                        </span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Objetivo:</strong><br>
                        <span class="text-muted" style="font-size: 0.875rem;">
                            Qualificar leads e agendar demonstrações do produto para prospects qualificados.
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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

// Função para limpar formulário
function resetForm() {
    if (confirm('Tem certeza que deseja limpar todos os campos?')) {
        document.getElementById('assistantForm').reset();
        
        // Remover classes de erro
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
    }
}

// Auto-resize para textareas
document.querySelectorAll('textarea').forEach(textarea => {
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
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
</script>

