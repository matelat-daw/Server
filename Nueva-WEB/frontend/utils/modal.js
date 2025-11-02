// Modal utility - Sistema global de modales
window.showModal = function(message, type = 'error', onClose) {
    const existingModal = document.getElementById('global-modal');
    if (existingModal) {
        existingModal.remove();
    }

    const modal = document.createElement('div');
    modal.id = 'global-modal';
    modal.className = 'modal-overlay';
    
    const iconMap = {
        'success': '✅',
        'error': '❌',
        'warning': '⚠️',
        'info': 'ℹ️'
    };
    
    const colorMap = {
        'success': '#48bb78',
        'error': '#e53e3e',
        'warning': '#ed8936',
        'info': '#4299e1'
    };

    modal.innerHTML = `
        <div class="modal-content" style="background:#fff;padding:2rem;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,0.15);text-align:center;max-width:90vw;animation:modalFadeIn 0.3s ease;">
            <div style="font-size:3rem;margin-bottom:1rem;">${iconMap[type] || iconMap['error']}</div>
            <h3 style="margin-bottom:1rem;color:${colorMap[type] || colorMap['error']};">${type === 'success' ? 'Éxito' : type === 'error' ? 'Error' : 'Aviso'}</h3>
            <p style="margin-bottom:2rem;color:#4a5568;">${message}</p>
            <button id="modal-close-btn" style="background:${colorMap[type] || colorMap['error']};color:#fff;padding:0.75rem 2rem;border:none;border-radius:5px;cursor:pointer;font-size:1rem;font-weight:600;">Entendido</button>
        </div>
    `;

    Object.assign(modal.style, {
        position: 'fixed',
        top: '0',
        left: '0',
        width: '100vw',
        height: '100vh',
        background: 'rgba(0,0,0,0.5)',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        zIndex: '9999'
    });

    document.body.appendChild(modal);

    const closeModal = () => {
        modal.remove();
        if (onClose && typeof onClose === 'function') {
            onClose();
        }
    };

    document.getElementById('modal-close-btn').addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });
};

// Añadir animación CSS si no existe
if (!document.getElementById('modal-styles')) {
    const style = document.createElement('style');
    style.id = 'modal-styles';
    style.textContent = `
        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
    `;
    document.head.appendChild(style);
}
