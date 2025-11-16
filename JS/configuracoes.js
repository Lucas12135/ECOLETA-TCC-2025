document.addEventListener('DOMContentLoaded', () => {
    // Carregar configurações salvas
    loadSavedSettings();

    // Adicionar listeners para os formulários
    setupFormListeners();

    // Adicionar listeners para os toggles
    setupToggleListeners();

    // Adicionar listeners para os selects
    setupSelectListeners();
});

// Funções para os Modais
function showChangePasswordModal() {
    const modal = document.getElementById('changePasswordModal');
    modal.style.display = 'block';
}

function showDeleteAccountModal() {
    const modal = document.getElementById('deleteAccountModal');
    modal.style.display = 'block';
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.style.display = 'none';
}

// Fechar modal quando clicar fora dele
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}

// Setup dos Formulários
function setupFormListeners() {
    // Formulário de alteração de senha
    const changePasswordForm = document.getElementById('changePasswordForm');
    if (changePasswordForm) {
        changePasswordForm.addEventListener('submit', handleChangePassword);
    }

    // Formulário de exclusão de conta
    const deleteAccountForm = document.getElementById('deleteAccountForm');
    if (deleteAccountForm) {
        deleteAccountForm.addEventListener('submit', handleDeleteAccount);
    }
}

// Setup dos Toggles
function setupToggleListeners() {
    const toggleInputs = document.querySelectorAll('.toggle-switch input[type="checkbox"]');
    toggleInputs.forEach(toggle => {
        toggle.addEventListener('change', handleToggleChange);
    });
}

// Setup dos Selects
function setupSelectListeners() {
    const selects = document.querySelectorAll('.setting-select');
    selects.forEach(select => {
        select.addEventListener('change', handleSelectChange);
    });
}

// Handlers
async function handleChangePassword(event) {
    event.preventDefault();

    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    // Validação básica
    if (newPassword !== confirmPassword) {
        showNotification('As senhas não coincidem', 'error');
        return;
    }

    try {
        const response = await fetch('../BANCO/change_password.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                currentPassword,
                newPassword
            })
        });

        const data = await response.json();

        if (data.success) {
            showNotification('Senha alterada com sucesso!', 'success');
            closeModal('changePasswordModal');
            document.getElementById('changePasswordForm').reset();
        } else {
            showNotification(data.message || 'Erro ao alterar a senha', 'error');
        }
    } catch (error) {
        showNotification('Erro ao processar a solicitação', 'error');
    }
}

async function handleDeleteAccount(event) {
    event.preventDefault();

    const password = document.getElementById('deleteConfirmPassword').value;

    const confirmed = confirm('Tem certeza que deseja excluir sua conta? Esta ação é irreversível.');
    
    if (!confirmed) return;

    try {
        const response = await fetch('../BANCO/delete_account.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ password })
        });

        const data = await response.json();

        if (data.success) {
            showNotification('Conta excluída com sucesso', 'success');
            setTimeout(() => {
                window.location.href = '../index.php';
            }, 2000);
        } else {
            showNotification(data.message || 'Erro ao excluir a conta', 'error');
        }
    } catch (error) {
        showNotification('Erro ao processar a solicitação', 'error');
    }
}

async function handleToggleChange(event) {
    const setting = event.target.id;
    const value = event.target.checked;

    try {
        const response = await fetch('../BANCO/update_settings.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                setting,
                value
            })
        });

        const data = await response.json();

        if (data.success) {
            showNotification('Configuração atualizada', 'success');
        } else {
            showNotification('Erro ao atualizar configuração', 'error');
            // Reverter o toggle se houver erro
            event.target.checked = !value;
        }
    } catch (error) {
        showNotification('Erro ao salvar configuração', 'error');
        // Reverter o toggle se houver erro
        event.target.checked = !value;
    }
}

async function handleSelectChange(event) {
    const setting = event.target.id;
    const value = event.target.value;

    try {
        const response = await fetch('../BANCO/update_settings.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                setting,
                value
            })
        });

        const data = await response.json();

        if (data.success) {
            showNotification('Preferência atualizada', 'success');
        } else {
            showNotification('Erro ao atualizar preferência', 'error');
        }
    } catch (error) {
        showNotification('Erro ao salvar preferência', 'error');
    }
}

// Carregar configurações salvas
async function loadSavedSettings() {
    try {
        const response = await fetch('../BANCO/get_settings.php');
        const settings = await response.json();

        if (settings.success) {
            // Atualizar toggles
            Object.keys(settings.data).forEach(key => {
                const element = document.getElementById(key);
                if (element && element.type === 'checkbox') {
                    element.checked = settings.data[key];
                } else if (element && element.tagName === 'SELECT') {
                    element.value = settings.data[key];
                }
            });
        }
    } catch (error) {
        console.error('Erro ao carregar configurações:', error);
    }
}

// Função de notificação
function showNotification(message, type = 'info') {
    // Verificar se o elemento de notificação já existe
    let notification = document.querySelector('.notification');
    if (!notification) {
        // Criar elemento de notificação
        notification = document.createElement('div');
        notification.className = 'notification';
        document.body.appendChild(notification);
    }

    // Adicionar classe de tipo e mensagem
    notification.className = `notification ${type}`;
    notification.textContent = message;

    // Mostrar notificação
    notification.style.display = 'block';
    notification.style.opacity = '1';

    // Ocultar após 3 segundos
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            notification.style.display = 'none';
        }, 300);
    }, 3000);
}

// Estilização da notificação via JavaScript
const style = document.createElement('style');
style.textContent = `
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 1001;
        transition: opacity 0.3s ease;
        display: none;
    }

    .notification.success {
        background-color: var(--success);
    }

    .notification.error {
        background-color: var(--danger);
    }

    .notification.info {
        background-color: var(--primary);
    }
`;
document.head.appendChild(style);