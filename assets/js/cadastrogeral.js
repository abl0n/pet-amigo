// ==========================================
// Cadastro Geral - Máscaras, validação e utilitários
// ==========================================

document.addEventListener('DOMContentLoaded', function() {
    initializeMasks();
    initializeValidations();
    loadEstados();
    initCepListener();
});

/* ---------- MÁSCARAS ---------- */
function initializeMasks() {
    // CEP: 00000-000
    document.querySelectorAll('input[name="cep"]').forEach(input => {
        input.addEventListener('input', e => {
            let v = e.target.value.replace(/\D/g, '').substring(0, 8);
            if (v.length > 5) v = v.substring(0, 5) + '-' + v.substring(5);
            e.target.value = v;
        });
    });

    // Telefone: (00) 00000-0000
    document.querySelectorAll('input[name="telefone"]').forEach(input => {
        input.addEventListener('input', e => {
            let v = e.target.value.replace(/\D/g, '').substring(0, 11);
            if (v.length > 2) v = '(' + v.substring(0, 2) + ') ' + v.substring(2);
            if (v.length > 10) v = v.substring(0, 10) + '-' + v.substring(10);
            e.target.value = v;
        });
    });

    // CPF: 000.000.000-00
    document.querySelectorAll('input[name="cpf"]').forEach(input => {
        input.addEventListener('input', e => {
            let v = e.target.value.replace(/\D/g, '').substring(0, 11);
            if (v.length > 3) v = v.substring(0, 3) + '.' + v.substring(3);
            if (v.length > 7) v = v.substring(0, 7) + '.' + v.substring(7);
            if (v.length > 11) v = v.substring(0, 11) + '-' + v.substring(11);
            e.target.value = v;
        });
    });
}

/* ---------- VALIDAÇÃO ---------- */
function initializeValidations() {
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) e.preventDefault();
        });
    });
}

function validateForm(form) {
    let isValid = true;
    form.querySelectorAll('[required]').forEach(field => {
        if (!field.value.trim()) {
            markFieldAsInvalid(field, 'Este campo é obrigatório');
            isValid = false;
        } else {
            markFieldAsValid(field);
        }
    });

    form.querySelectorAll('input[type="email"]').forEach(field => {
        if (field.value && !isValidEmail(field.value)) {
            markFieldAsInvalid(field, 'E-mail inválido');
            isValid = false;
        }
    });

    return isValid;
}

function markFieldAsInvalid(field, message) {
    field.style.borderColor = '#e74c3c';
    let el = field.parentNode.querySelector('.error-message');
    if (!el) {
        el = document.createElement('div');
        el.className = 'error-message';
        field.parentNode.appendChild(el);
    }
    el.textContent = message;
    el.style.display = 'block';
}

function markFieldAsValid(field) {
    field.style.borderColor = '';
    const el = field.parentNode.querySelector('.error-message');
    if (el) el.style.display = 'none';
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

/* ---------- ESTADOS ---------- */
function loadEstados() {
    const estados = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA',
                     'MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN',
                     'RS','RO','RR','SC','SP','SE','TO'];

    document.querySelectorAll('select[name="estado"]').forEach(select => {
        // Não duplica se já tem opções
        if (select.querySelector('option[value="SP"]')) return;
        estados.forEach(uf => {
            const option = document.createElement('option');
            option.value = uf;
            option.textContent = uf;
            select.appendChild(option);
        });
    });
}

/* ---------- CEP (ViaCEP) ---------- */
function initCepListener() {
    const cepInput = document.querySelector('input[name="cep"]');
    if (!cepInput) return;
    cepInput.addEventListener('blur', () => buscarCEP(cepInput));
}

function buscarCEP(input) {
    const cep = input.value.replace(/\D/g, '');
    if (cep.length !== 8) return;

    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(r => r.json())
        .then(data => {
            if (data.erro) return;
            const set = (id, val) => { const el = document.getElementById(id); if (el) el.value = val || ''; };
            set('logradouro', data.logradouro);
            set('bairro', data.bairro);
            set('cidade', data.localidade);
            set('estado', data.uf);
        })
        .catch(err => console.error('Erro ViaCEP:', err));
}

/* ---------- UTILITÁRIOS ---------- */
function showMessage(message, type = 'success') {
    const container = document.querySelector('.cadastro-container');
    if (!container) return;
    const div = document.createElement('div');
    div.className = `message ${type}`;
    div.textContent = message;
    container.insertBefore(div, container.firstChild);
    setTimeout(() => div.remove(), 5000);
}

function setLoading(button, isLoading) {
    if (isLoading) {
        button.disabled = true;
        button.innerHTML = '<span class="loading"></span> Processando...';
    } else {
        button.disabled = false;
        button.innerHTML = button.getAttribute('data-original-text') || 'Salvar';
    }
}