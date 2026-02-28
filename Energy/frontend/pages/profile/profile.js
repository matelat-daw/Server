// Profile Page
var profilePage = {
    currentUser: null,
    
    init: function() {
        // Verificar si está logueado
        if (!window.authService || !window.authService.isLoggedIn()) {
            window.app.loadPage('login');
            return;
        }
        
        this.currentUser = window.authService.getUser();
        this.loadUserData();
        this.loadContractData();
        this.setupForms();
    },
    
    loadUserData: function() {
        if (!this.currentUser) return;
        
        // Llenar formulario con datos del usuario
        document.getElementById('profile-first-name').value = this.currentUser.first_name || '';
        document.getElementById('profile-last-name').value = this.currentUser.last_name || '';
        document.getElementById('profile-second-last-name').value = this.currentUser.second_last_name || '';
        document.getElementById('profile-username').value = this.currentUser.username || '';
        document.getElementById('profile-email').value = this.currentUser.email || '';
        document.getElementById('profile-phone').value = this.currentUser.phone || '';
        
        // Mostrar rol
        var roleBadge = document.getElementById('user-role-badge');
        var roleText = this.currentUser.roles && this.currentUser.roles[0] || 'usuario';
        var roleEmoji = roleText === 'admin' ? '👑' : roleText === 'seller' ? '💼' : '👤';
        roleBadge.textContent = roleEmoji + ' ' + roleText.charAt(0).toUpperCase() + roleText.slice(1);
        
        // Mostrar pestaña de gestión solo para admin
        if (this.currentUser.roles && this.currentUser.roles.includes('admin')) {
            var managementTab = document.getElementById('tab-management-btn');
            if (managementTab) {
                managementTab.style.display = 'inline-block';
            }
        }
    },
    
    loadContractData: function() {
        // Cargar contratos reales desde la API
        if (!this.currentUser) return;
        
        var self = this;
        
        // Obtener contratos del usuario
        window.apiService.get('/contracts/my')
            .then(function(response) {
                
                if (response.data && response.data.length > 0) {
                    // Tomar el primer contrato activo o el más reciente
                    var contract = response.data.find(c => c.status === 'active') || response.data[0];
                    self.displayContractData(contract);
                } else {
                    // No hay contratos, mostrar datos por defecto o de calculadora
                    self.displayNoContract();
                }
            })
            .catch(function(error) {
                // Si hay error, intentar cargar desde sessionStorage
                self.loadContractFromStorage();
            });
    },
    
    displayContractData: function(contract) {
        // Actualizar elementos con datos del contrato real
        var contractNumberEl = document.getElementById('contract-number');
        var previousCompanyEl = document.getElementById('previous-company');
        var providerNameEl = document.getElementById('provider-name');
        var monthlySavingsEl = document.getElementById('monthly-savings');
        var statusEl = document.querySelector('.status-active');
        
        // Información del contrato
        if (contractNumberEl) {
            contractNumberEl.textContent = 'CT-' + contract.id.toString().padStart(6, '0');
        }
        
        // Compañía anterior (por ahora desde sessionStorage o por defecto)
        var calculatorData = sessionStorage.getItem('calculatorData');
        var previousCompany = 'Anterior proveedor';
        if (calculatorData) {
            var data = JSON.parse(calculatorData);
            previousCompany = data.companyName || previousCompany;
        }
        if (previousCompanyEl) {
            previousCompanyEl.textContent = previousCompany;
        }
        
        // Nombre del proveedor
        var providerName = contract.provider_name || 'Proveedor';
        if (providerNameEl) {
            providerNameEl.textContent = providerName;
        }
        
        // Actualizar todas las referencias al proveedor
        var providerNameElements = [
            document.getElementById('provider-name-text'),
            document.getElementById('provider-name-text2'),
            document.getElementById('provider-name-text3'),
            document.getElementById('provider-name-text4')
        ];
        providerNameElements.forEach(function(el) {
            if (el) el.textContent = providerName;
        });
        
        // Ahorro estimado (por ahora usar dato de sessionStorage o por defecto)
        var savings = '30%';
        if (calculatorData) {
            var data = JSON.parse(calculatorData);
            savings = data.savings || savings;
        }
        if (monthlySavingsEl) {
            monthlySavingsEl.textContent = savings;
        }
        
        // Estado del contrato
        if (statusEl) {
            var statusMap = {
                'active': '✓ Activo',
                'pending': '⏳ Pendiente',
                'cancelled': '✗ Cancelado',
                'completed': '✓ Completado'
            };
            statusEl.textContent = statusMap[contract.status] || contract.status;
            statusEl.className = 'detail-value status-' + contract.status;
        }
        
        // Guardar contrato actual para referencia
        this.currentContract = contract;
    },
    
    displayNoContract: function() {
        // No hay contratos, intentar cargar desde sessionStorage
        this.loadContractFromStorage();
    },
    
    loadContractFromStorage: function() {
        // Cargar datos de cotización desde sessionStorage
        var calculatorData = sessionStorage.getItem('calculatorData');
        var contractNumber = 'No disponible';
        var previousCompany = 'No disponible';
        var providerName = 'Proveedor';
        
        if (calculatorData) {
            var data = JSON.parse(calculatorData);
            previousCompany = data.companyName || previousCompany;
            providerName = data.selectedProvider || providerName;
        }
        
        // Actualizar elementos
        var contractNumberEl = document.getElementById('contract-number');
        var previousCompanyEl = document.getElementById('previous-company');
        var providerNameEl = document.getElementById('provider-name');
        
        if (contractNumberEl) {
            contractNumberEl.textContent = contractNumber;
        }
        if (previousCompanyEl) {
            previousCompanyEl.textContent = previousCompany;
        }
        if (providerNameEl) {
            providerNameEl.textContent = providerName;
        }
        
        // Actualizar todas las referencias al proveedor
        var providerNameElements = [
            document.getElementById('provider-name-text'),
            document.getElementById('provider-name-text2'),
            document.getElementById('provider-name-text3'),
            document.getElementById('provider-name-text4')
        ];
        providerNameElements.forEach(function(el) {
            if (el) el.textContent = providerName;
        });
    },
    
    setupForms: function() {
        this.setupProfileForm();
        this.setupPasswordForm();
        this.setupAddSellerForm();
    },
    
    setupProfileForm: function() {
        var form = document.getElementById('profile-form');
        var messageDiv = document.getElementById('profile-message');
        var saveBtn = document.getElementById('save-btn');
        
        if (!form) return;
        
        var self = this;
        
        form.onsubmit = function(e) {
            e.preventDefault();
            
            var formData = {
                first_name: document.getElementById('profile-first-name').value.trim(),
                last_name: document.getElementById('profile-last-name').value.trim(),
                second_last_name: document.getElementById('profile-second-last-name').value.trim() || null,
                username: document.getElementById('profile-username').value.trim(),
                email: document.getElementById('profile-email').value.trim(),
                phone: document.getElementById('profile-phone').value.trim() || null
            };
            
            messageDiv.style.display = 'none';
            saveBtn.classList.add('loading');
            saveBtn.disabled = true;
            
            // Actualizar perfil
            window.apiService.put('/auth/profile', formData)
                .then(function(response) {
                    
                    // Actualizar datos en authService y localStorage
                    if (response.data) {
                        window.authService.user = response.data;
                        localStorage.setItem('user', JSON.stringify(response.data));
                        self.currentUser = response.data;
                    }
                    
                    // Mostrar éxito
                    messageDiv.className = 'message success';
                    messageDiv.textContent = '✓ Perfil actualizado exitosamente';
                    messageDiv.style.display = 'block';
                    
                    // Actualizar header
                    if (window.headerComponent) {
                        window.headerComponent.updateUserMenu();
                    }
                    
                    saveBtn.classList.remove('loading');
                    saveBtn.disabled = false;
                    
                    // Ocultar mensaje después de 3 segundos
                    setTimeout(function() {
                        messageDiv.style.display = 'none';
                    }, 3000);
                })
                .catch(function(error) {
                    
                    messageDiv.className = 'message error';
                    messageDiv.textContent = error.message || 'Error al actualizar el perfil';
                    messageDiv.style.display = 'block';
                    
                    saveBtn.classList.remove('loading');
                    saveBtn.disabled = false;
                });
        };
    },
    
    setupPasswordForm: function() {
        var form = document.getElementById('password-form');
        var messageDiv = document.getElementById('password-message');
        var passwordBtn = document.getElementById('password-btn');
        
        if (!form) return;
        
        form.onsubmit = function(e) {
            e.preventDefault();
            
            var currentPassword = document.getElementById('current-password').value;
            var newPassword = document.getElementById('new-password').value;
            var confirmPassword = document.getElementById('confirm-password').value;
            
            // Validar que las contraseñas coincidan
            if (newPassword !== confirmPassword) {
                messageDiv.className = 'message error';
                messageDiv.textContent = 'Las contraseñas no coinciden';
                messageDiv.style.display = 'block';
                return;
            }
            
            if (newPassword.length < 6) {
                messageDiv.className = 'message error';
                messageDiv.textContent = 'La contraseña debe tener al menos 6 caracteres';
                messageDiv.style.display = 'block';
                return;
            }
            
            messageDiv.style.display = 'none';
            passwordBtn.classList.add('loading');
            passwordBtn.disabled = true;
            
            // Cambiar contraseña
            var userId = self.currentUser.id;
            window.apiService.put('/users/' + userId + '/password', {
                current_password: currentPassword,
                new_password: newPassword
            })
                .then(function(response) {
                    
                    messageDiv.className = 'message success';
                    messageDiv.textContent = '✓ Contraseña actualizada exitosamente';
                    messageDiv.style.display = 'block';
                    
                    // Limpiar formulario
                    form.reset();
                    
                    passwordBtn.classList.remove('loading');
                    passwordBtn.disabled = false;
                    
                    setTimeout(function() {
                        messageDiv.style.display = 'none';
                    }, 3000);
                })
                .catch(function(error) {
                    
                    messageDiv.className = 'message error';
                    messageDiv.textContent = error.message || 'Error al cambiar la contraseña';
                    messageDiv.style.display = 'block';
                    
                    passwordBtn.classList.remove('loading');
                    passwordBtn.disabled = false;
                });
        };
    },
    
    switchTab: function(tabName) {
        // Desactivar todos los tabs
        var tabBtns = document.querySelectorAll('.tab-btn');
        var tabContents = document.querySelectorAll('.tab-content');
        
        tabBtns.forEach(function(btn) { btn.classList.remove('active'); });
        tabContents.forEach(function(content) { content.classList.remove('active'); });
        
        // Activar tab seleccionado - buscar el botón correspondiente
        tabBtns.forEach(function(btn) {
            if (btn.onclick && btn.onclick.toString().includes("'" + tabName + "'")) {
                btn.classList.add('active');
            }
        });
        
        var tabContent = document.getElementById('tab-' + tabName);
        if (tabContent) {
            tabContent.classList.add('active');
        }
    },
    
    confirmDelete: function() {
        document.getElementById('delete-modal').style.display = 'flex';
    },
    
    closeDeleteModal: function() {
        document.getElementById('delete-modal').style.display = 'none';
    },
    
    deleteAccount: function() {
        var userId = this.currentUser.id;
        
        window.apiService.delete('/users/' + userId)
            .then(function(response) {
                
                // Cerrar sesión
                window.authService.logout();
                
                // Redirigir a home
                window.app.loadPage('home');
                
                alert('Tu cuenta ha sido eliminada exitosamente');
            })
            .catch(function(error) {
                alert('Error al eliminar la cuenta: ' + (error.message || 'Error desconocido'));
            });
    },
    
    setupAddSellerForm: function() {
        var form = document.getElementById('add-seller-form');
        var messageDiv = document.getElementById('seller-message');
        var addSellerBtn = document.getElementById('add-seller-btn');
        
        if (!form) return;
        
        var self = this;
        
        form.onsubmit = function(e) {
            e.preventDefault();
            
            // Verificar que el usuario sea admin
            if (!self.currentUser.roles || !self.currentUser.roles.includes('admin')) {
                messageDiv.className = 'message error';
                messageDiv.textContent = 'No tienes permisos para realizar esta acción';
                messageDiv.style.display = 'block';
                return;
            }
            
            var password = document.getElementById('seller-password').value;
            var passwordConfirm = document.getElementById('seller-password-confirm').value;
            
            // Validar contraseña
            if (password.length < 6) {
                messageDiv.className = 'message error';
                messageDiv.textContent = 'La contraseña debe tener al menos 6 caracteres';
                messageDiv.style.display = 'block';
                return;
            }
            
            // Validar que las contraseñas coincidan
            if (password !== passwordConfirm) {
                messageDiv.className = 'message error';
                messageDiv.textContent = 'Las contraseñas no coinciden';
                messageDiv.style.display = 'block';
                return;
            }
            
            var formData = {
                first_name: document.getElementById('seller-first-name').value.trim(),
                last_name: document.getElementById('seller-last-name').value.trim(),
                second_last_name: document.getElementById('seller-second-last-name').value.trim() || null,
                email: document.getElementById('seller-email').value.trim(),
                username: document.getElementById('seller-username').value.trim(),
                password: password,
                phone: document.getElementById('seller-phone').value.trim() || null,
                role: 'seller',
                is_active: true // Vendedor activo inmediatamente
            };
            
            messageDiv.style.display = 'none';
            addSellerBtn.classList.add('loading');
            addSellerBtn.disabled = true;
            
            // Crear vendedor
            window.apiService.post('/admin/create-seller', formData)
                .then(function(response) {
                    
                    messageDiv.className = 'message success';
                    messageDiv.textContent = '✓ Vendedor creado exitosamente';
                    messageDiv.style.display = 'block';
                    
                    // Limpiar formulario
                    form.reset();
                    
                    addSellerBtn.classList.remove('loading');
                    addSellerBtn.disabled = false;
                    
                    setTimeout(function() {
                        messageDiv.style.display = 'none';
                    }, 3000);
                })
                .catch(function(error) {
                    
                    messageDiv.className = 'message error';
                    messageDiv.textContent = error.message || 'Error al crear vendedor';
                    messageDiv.style.display = 'block';
                    
                    addSellerBtn.classList.remove('loading');
                    addSellerBtn.disabled = false;
                });
        };  }
};

window.profilePage = profilePage;
