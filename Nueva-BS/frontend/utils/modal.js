// Modal Utility - Sistema global de modales con Bootstrap
window.showModal = function(message, type = 'error', onClose) {
    const existingModal = document.getElementById('global-modal');
    if (existingModal) {
        existingModal.remove();
    }

    const iconMap = {
        'success': '<i class="fas fa-check-circle fa-3x text-success"></i>',
        'error': '<i class="fas fa-exclamation-circle fa-3x text-danger"></i>',
        'warning': '<i class="fas fa-exclamation-triangle fa-3x text-warning"></i>',
        'info': '<i class="fas fa-info-circle fa-3x text-info"></i>'
    };

    const titleMap = {
        'success': 'Éxito',
        'error': 'Error',
        'warning': 'Advertencia',
        'info': 'Información'
    };

    const colorMap = {
        'success': 'success',
        'error': 'danger',
        'warning': 'warning',
        'info': 'info'
    };

    const modalHTML = `
        <div class="modal fade" id="global-modal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title">${titleMap[type]}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="mb-3">${iconMap[type]}</div>
                        <p class="mb-0">${message}</p>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-${colorMap[type]}" data-bs-dismiss="modal">Entendido</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    const modalElement = document.getElementById('global-modal');
    const modal = new bootstrap.Modal(modalElement);
    
    modalElement.addEventListener('hidden.bs.modal', () => {
        modalElement.remove();
        if (onClose) onClose();
    });
    
    modal.show();
};

window.showConfirm = function(message, title = 'Confirmar', onConfirm, onCancel) {
    const existingModal = document.getElementById('confirm-modal');
    if (existingModal) {
        existingModal.remove();
    }

    const modalHTML = `
        <div class="modal fade" id="confirm-modal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title">${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">${message}</p>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancel-btn">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="confirm-btn">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    const modalElement = document.getElementById('confirm-modal');
    const modal = new bootstrap.Modal(modalElement);
    
    document.getElementById('confirm-btn').onclick = () => {
        modal.hide();
        if (onConfirm) onConfirm();
    };
    
    document.getElementById('cancel-btn').onclick = () => {
        modal.hide();
        if (onCancel) onCancel();
    };
    
    modalElement.addEventListener('hidden.bs.modal', () => {
        modalElement.remove();
    });
    
    modal.show();
};
