/**
 * Sistema de Administração de Assistentes
 * JavaScript para funcionalidades do painel administrativo
 */

// Configurações globais
const AdminPanel = {
    baseUrl: '/painel',
    csrfToken: null,
    
    // Inicialização
    init() {
        this.setupEventListeners();
        this.setupTooltips();
        this.setupAutoSave();
        this.loadCsrfToken();
    },
    
    // Configurar event listeners
    setupEventListeners() {
        // Confirmação de exclusão
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-confirm-delete]')) {
                e.preventDefault();
                this.confirmDelete(e.target);
            }
        });
        
        // Auto-submit em mudanças de filtro
        document.addEventListener('change', (e) => {
            if (e.target.matches('[data-auto-submit]')) {
                e.target.closest('form').submit();
            }
        });
        
        // Busca em tempo real
        document.addEventListener('input', (e) => {
            if (e.target.matches('[data-live-search]')) {
                this.debounce(() => {
                    this.performLiveSearch(e.target);
                }, 300)();
            }
        });
    },
    
    // Configurar tooltips
    setupTooltips() {
        const tooltipElements = document.querySelectorAll('[title]');
        tooltipElements.forEach(element => {
            element.addEventListener('mouseenter', this.showTooltip);
            element.addEventListener('mouseleave', this.hideTooltip);
        });
    },
    
    // Auto-save para formulários
    setupAutoSave() {
        const forms = document.querySelectorAll('[data-auto-save]');
        forms.forEach(form => {
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('input', this.debounce(() => {
                    this.autoSave(form);
                }, 2000));
            });
        });
    },
    
    // Carregar token CSRF
    loadCsrfToken() {
        const tokenElement = document.querySelector('meta[name="csrf-token"]');
        if (tokenElement) {
            this.csrfToken = tokenElement.getAttribute('content');
        }
    },
    
    // Confirmação de exclusão
    confirmDelete(element) {
        const message = element.getAttribute('data-confirm-delete') || 'Tem certeza que deseja excluir este item?';
        const url = element.getAttribute('href') || element.getAttribute('data-url');
        
        if (confirm(message)) {
            if (element.tagName === 'A') {
                window.location.href = url;
            } else {
                this.submitDeleteForm(url);
            }
        }
    },
    
    // Submeter formulário de exclusão
    submitDeleteForm(url) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        
        if (this.csrfToken) {
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = 'csrf_token';
            tokenInput.value = this.csrfToken;
            form.appendChild(tokenInput);
        }
        
        document.body.appendChild(form);
        form.submit();
    },
    
    // Busca em tempo real
    performLiveSearch(input) {
        const query = input.value.trim();
        const target = input.getAttribute('data-target');
        const minLength = parseInt(input.getAttribute('data-min-length')) || 2;
        
        if (query.length < minLength) {
            this.clearSearchResults(target);
            return;
        }
        
        this.showSearchLoading(target);
        
        fetch(`${this.baseUrl}/search?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                this.displaySearchResults(target, data);
            })
            .catch(error => {
                console.error('Erro na busca:', error);
                this.hideSearchLoading(target);
            });
    },
    
    // Exibir resultados da busca
    displaySearchResults(target, data) {
        const container = document.querySelector(target);
        if (!container) return;
        
        this.hideSearchLoading(target);
        
        if (data.success && data.data.length > 0) {
            let html = '<div class="search-results">';
            data.data.forEach(item => {
                html += `
                    <div class="search-result-item" data-id="${item.id}">
                        <div class="search-result-title">${item.name}</div>
                        <div class="search-result-description">${item.description || ''}</div>
                    </div>
                `;
            });
            html += '</div>';
            container.innerHTML = html;
        } else {
            container.innerHTML = '<div class="search-no-results">Nenhum resultado encontrado</div>';
        }
    },
    
    // Limpar resultados da busca
    clearSearchResults(target) {
        const container = document.querySelector(target);
        if (container) {
            container.innerHTML = '';
        }
    },
    
    // Mostrar loading da busca
    showSearchLoading(target) {
        const container = document.querySelector(target);
        if (container) {
            container.innerHTML = '<div class="search-loading"><i class="fas fa-spinner fa-spin"></i> Buscando...</div>';
        }
    },
    
    // Esconder loading da busca
    hideSearchLoading(target) {
        const loadingElement = document.querySelector(`${target} .search-loading`);
        if (loadingElement) {
            loadingElement.remove();
        }
    },
    
    // Auto-save
    autoSave(form) {
        const formData = new FormData(form);
        const url = form.getAttribute('data-auto-save-url') || form.action;
        
        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification('Dados salvos automaticamente', 'success');
            }
        })
        .catch(error => {
            console.error('Erro no auto-save:', error);
        });
    },
    
    // Mostrar notificação
    showNotification(message, type = 'info', duration = 3000) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${this.getNotificationIcon(type)}"></i>
                <span>${message}</span>
            </div>
            <button class="notification-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove após duração especificada
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, duration);
    },
    
    // Obter ícone da notificação
    getNotificationIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    },
    
    // Mostrar tooltip
    showTooltip(e) {
        const element = e.target;
        const title = element.getAttribute('title');
        if (!title) return;
        
        // Remover title para evitar tooltip nativo
        element.setAttribute('data-original-title', title);
        element.removeAttribute('title');
        
        const tooltip = document.createElement('div');
        tooltip.className = 'custom-tooltip';
        tooltip.textContent = title;
        document.body.appendChild(tooltip);
        
        // Posicionar tooltip
        const rect = element.getBoundingClientRect();
        tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
        tooltip.style.top = rect.top - tooltip.offsetHeight - 10 + 'px';
        
        element._tooltip = tooltip;
    },
    
    // Esconder tooltip
    hideTooltip(e) {
        const element = e.target;
        if (element._tooltip) {
            element._tooltip.remove();
            element._tooltip = null;
        }
        
        // Restaurar title original
        const originalTitle = element.getAttribute('data-original-title');
        if (originalTitle) {
            element.setAttribute('title', originalTitle);
            element.removeAttribute('data-original-title');
        }
    },
    
    // Debounce function
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },
    
    // Utilitários para modais
    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    },
    
    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    },
    
    // Utilitários para formulários
    validateForm(form) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                this.showFieldError(field, 'Este campo é obrigatório');
                isValid = false;
            } else {
                this.clearFieldError(field);
            }
        });
        
        return isValid;
    },
    
    showFieldError(field, message) {
        field.classList.add('is-invalid');
        
        let errorElement = field.parentElement.querySelector('.invalid-feedback');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'invalid-feedback';
            field.parentElement.appendChild(errorElement);
        }
        
        errorElement.textContent = message;
    },
    
    clearFieldError(field) {
        field.classList.remove('is-invalid');
        const errorElement = field.parentElement.querySelector('.invalid-feedback');
        if (errorElement) {
            errorElement.remove();
        }
    },
    
    // Utilitários para tabelas
    sortTable(table, column, direction = 'asc') {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows.sort((a, b) => {
            const aValue = a.cells[column].textContent.trim();
            const bValue = b.cells[column].textContent.trim();
            
            if (direction === 'asc') {
                return aValue.localeCompare(bValue);
            } else {
                return bValue.localeCompare(aValue);
            }
        });
        
        rows.forEach(row => tbody.appendChild(row));
    },
    
    // Utilitários para dados
    formatDate(dateString, format = 'dd/mm/yyyy') {
        const date = new Date(dateString);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        
        return format
            .replace('dd', day)
            .replace('mm', month)
            .replace('yyyy', year)
            .replace('hh', hours)
            .replace('ii', minutes);
    },
    
    formatNumber(number, decimals = 0) {
        return new Intl.NumberFormat('pt-BR', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        }).format(number);
    },
    
    // Utilitários para localStorage
    saveToStorage(key, data) {
        try {
            localStorage.setItem(key, JSON.stringify(data));
        } catch (error) {
            console.error('Erro ao salvar no localStorage:', error);
        }
    },
    
    loadFromStorage(key, defaultValue = null) {
        try {
            const data = localStorage.getItem(key);
            return data ? JSON.parse(data) : defaultValue;
        } catch (error) {
            console.error('Erro ao carregar do localStorage:', error);
            return defaultValue;
        }
    }
};

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    AdminPanel.init();
});

// Exportar para uso global
window.AdminPanel = AdminPanel;

