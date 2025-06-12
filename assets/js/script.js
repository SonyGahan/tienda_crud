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

    // Preview de imagen en formularios
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    imageInputs.forEach(input => {
        input.addEventListener('change', previewImage);
    });

    // Auto-format de precios
    const priceInputs = document.querySelectorAll('input[name="precio"]');
    priceInputs.forEach(input => {
        input.addEventListener('blur', formatPrice);
    });

    // Preview de imagen para crear.php con ID personalizado
    const previewInput = document.getElementById('imagen');
    const previewImage = document.getElementById('preview-image');
    const previewContainer = document.getElementById('preview-container');

    if (previewInput && previewImage && previewContainer) {
        previewInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImage.src = e.target.result;
                    previewContainer.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Limpiar imagen cargada
    const clearBtn = document.querySelector('.preview-container .btn-outline-danger');
    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            previewInput.value = '';
            previewContainer.classList.add('d-none');
        });
    }

    // funcionalidad de autocompletar el código de producto a partir del nombre
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
 * Configurar validaciones de formularios
 */
function setupFormValidations() {
    // Validación de formularios con Bootstrap
    const forms = document.querySelectorAll('.needs-validation');

    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();

                // Mostrar primera campo con error
                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }

            form.classList.add('was-validated');
        }, false);
    });

    // Validación personalizada para códigos de producto
    const codigoInputs = document.querySelectorAll('input[name="codigo_producto"]');
    codigoInputs.forEach(input => {
        input.addEventListener('blur', validateProductCode);
    });
}

/**
 * Configurar alertas auto-dismiss
 */
function setupAlerts() {
    // Auto-cerrar alertas después de 5 segundos
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

    // Limpiar modal al cerrar
    document.getElementById('deleteModal').addEventListener('hidden.bs.modal', function () {
        document.body.removeChild(modal);
    });
}

/**
 * Preview de imagen seleccionada
 */
function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('image-preview');

    if (file && preview) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}

/**
 * Formatear precio automáticamente
 */
function formatPrice(event) {
    let value = event.target.value;

    // Remover caracteres no numéricos excepto punto
    value = value.replace(/[^0-9.]/g, '');

    // Asegurar solo un punto decimal
    const parts = value.split('.');
    if (parts.length > 2) {
        value = parts[0] + '.' + parts.slice(1).join('');
    }

    // Limitar a 2 decimales
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

    // Resetear validación personalizada
    event.target.setCustomValidity('');

    // Aquí podrías agregar una validación AJAX para verificar unicidad
    // checkProductCodeUnique(codigo);
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

    // Limpiar toast después de que se oculte
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
 * Utilidades adicionales
 */
const Utils = {
    // Formatear moneda
    formatCurrency: function (amount) {
        return new Intl.NumberFormat('es-AR', {
            style: 'currency',
            currency: 'ARS'
        }).format(amount);
    },

    // Formatear fecha
    formatDate: function (date) {
        return new Date(date).toLocaleDateString('es-AR');
    },

    // Capitalizar primera letra
    capitalize: function (str) {
        return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
    }
};