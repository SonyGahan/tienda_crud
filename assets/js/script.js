/**
 * ============================================
 * JAVASCRIPT PERSONALIZADO PARA TIENDA CRUD
 * ============================================
 */

// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function () {

    // Inicializar componentes de Bootstrap
    initBootstrapComponents();

    // Configurar eventos personalizados
    setupCustomEvents();

    // Configurar validaciones de formularios
    setupFormValidations();

    // Configurar alertas auto-dismiss
    setupAlerts();

    console.log('Sistema CRUD cargado correctamente');
});

/**
 * Inicializar componentes de Bootstrap
 */
function initBootstrapComponents() {
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Inicializar popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
}

/**
 * Configurar eventos personalizados
 */
function setupCustomEvents() {
    // Confirmar eliminación de productos
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const productName = this.getAttribute('data-product-name');
            const deleteUrl = this.getAttribute('href');

            showDeleteConfirmation(productName, deleteUrl);
        });
    });

    // Preview de imagen para inputs tipo file
    const imageInput = document.getElementById('imagen');
    if (imageInput) {
        imageInput.addEventListener('change', previewImageFile);
    }

    // Botón para limpiar la imagen
    const clearBtn = document.querySelector('.preview-container .btn-outline-danger');
    if (clearBtn) {
        clearBtn.addEventListener('click', clearImage);
    }

    // Auto-format de precios
    const priceInputs = document.querySelectorAll('input[name="precio"]');
    priceInputs.forEach(input => {
        input.addEventListener('blur', formatPrice);
    });

    // Autocompletar código desde nombre
    const nameField = document.getElementById('nombre');
    const codeField = document.getElementById('codigo_producto');

    if (nameField && codeField) {
        nameField.addEventListener('blur', function () {
            if (!codeField.value.trim()) {
                let codigo = this.value
                    .toUpperCase()
                    .replace(/[^A-Z0-9]/g, '')
                    .substring(0, 6);
                if (codigo) {
                    codeField.value = codigo + '001';
                }
            }
        });
    }
}

/**
 * Previsualizar imagen cargada en input file
 */
function previewImageFile(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('preview-image');
    const container = document.getElementById('preview-container');

    if (file && preview && container) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            container.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }
}

function clearImage() {
    const input = document.getElementById('imagen');
    const container = document.getElementById('preview-container');
    const preview = document.getElementById('preview-image');
    if (input && container && preview) {
        input.value = '';
        container.classList.add('d-none');
        preview.src = '';
    }
}

/**
 * Configurar validaciones de formularios
 */
function setupFormValidations() {
    const forms = document.querySelectorAll('.needs-validation');

    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }
            form.classList.add('was-validated');
        }, false);
    });

    const codigoInputs = document.querySelectorAll('input[name="codigo_producto"]');
    codigoInputs.forEach(input => {
        input.addEventListener('blur', validateProductCode);
    });
}

/**
 * Configurar alertas auto-dismiss
 */
function setupAlerts() {
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
}

/**
 * Mostrar confirmación de eliminación
 */
function showDeleteConfirmation(productName, deleteUrl) {
    const modal = document.createElement('div');
    modal.innerHTML = `
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Confirmar Eliminación
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">¿Estás seguro de que deseas eliminar el producto:</p>
                        <p class="fw-bold text-danger">"${productName}"</p>
                        <p class="text-muted small">Esta acción no se puede deshacer.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <a href="${deleteUrl}" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>Eliminar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(modal);
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();

    document.getElementById('deleteModal').addEventListener('hidden.bs.modal', function () {
        document.body.removeChild(modal);
    });
}

/**
 * Formatear precio automáticamente
 */
function formatPrice(event) {
    let value = event.target.value;
    value = value.replace(/[^0-9.]/g, '');
    const parts = value.split('.');
    if (parts.length > 2) {
        value = parts[0] + '.' + parts.slice(1).join('');
    }
    if (parts[1] && parts[1].length > 2) {
        value = parseFloat(value).toFixed(2);
    }
    event.target.value = value;
}

/**
 * Validar código de producto único
 */
function validateProductCode(event) {
    const codigo = event.target.value.trim();
    if (codigo.length < 3) {
        event.target.setCustomValidity('El código debe tener al menos 3 caracteres');
        return;
    }
    event.target.setCustomValidity('');
    // Validación AJAX pendiente
}

/**
 * Mostrar loading spinner
 */
function showLoading(message = 'Cargando...') {
    const loadingDiv = document.createElement('div');
    loadingDiv.id = 'loading-overlay';
    loadingDiv.innerHTML = `
        <div class="d-flex justify-content-center align-items-center position-fixed top-0 start-0 w-100 h-100"
             style="background: rgba(0,0,0,0.5); z-index: 9999;">
            <div class="text-center text-white">
                <div class="spinner-border mb-3" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <div>${message}</div>
            </div>
        </div>
    `;
    document.body.appendChild(loadingDiv);
}

/**
 * Ocultar loading spinner
 */
function hideLoading() {
    const loadingOverlay = document.getElementById('loading-overlay');
    if (loadingOverlay) {
        loadingOverlay.remove();
    }
}

/**
 * Mostrar notificación toast
 */
function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    const toastId = 'toast-' + Date.now();
    const toastHTML = `
        <div id="${toastId}" class="toast" role="alert">
            <div class="toast-header bg-${type} text-white">
                <i class="fas fa-${getToastIcon(type)} me-2"></i>
                <strong class="me-auto">${getToastTitle(type)}</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;

    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement);
    toast.show();

    toastElement.addEventListener('hidden.bs.toast', function () {
        toastElement.remove();
    });
}

/**
 * Crear contenedor de toasts si no existe
 */
function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

/**
 * Obtener ícono para toast según tipo
 */
function getToastIcon(type) {
    const icons = {
        'success': 'check-circle',
        'danger': 'exclamation-triangle',
        'warning': 'exclamation-circle',
        'info': 'info-circle'
    };
    return icons[type] || 'info-circle';
}

/**
 * Obtener título para toast según tipo
 */
function getToastTitle(type) {
    const titles = {
        'success': 'Éxito',
        'danger': 'Error',
        'warning': 'Advertencia',
        'info': 'Información'
    };
    return titles[type] || 'Notificación';
}

/**
 * Mostrar/ocultar contraseña
 */
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    if (!passwordInput || !toggleIcon) return;
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

/**
 * Cierre automático de alertas (5 segundos)
 */
document.addEventListener('DOMContentLoaded', function () {
    setTimeout(function () {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function (alert) {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        });
    }, 5000);
});

/**
 * Enviar formulario con Enter desde inputs
 */
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                form.submit();
            }
        });
    }
});

/**
 * Utilidades adicionales
 */
const Utils = {
    formatCurrency: function (amount) {
        return new Intl.NumberFormat('es-AR', {
            style: 'currency',
            currency: 'ARS'
        }).format(amount);
    },
    formatDate: function (date) {
        return new Date(date).toLocaleDateString('es-AR');
    },
    capitalize: function (str) {
        return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
    }
};
