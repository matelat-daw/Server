// frontend/components/user-menu/user-menu.js
document.addEventListener('DOMContentLoaded', function() {
    const userMenuButton = document.getElementById('user-menu-button');
    const userMenuDropdown = document.getElementById('user-menu-dropdown');

    userMenuButton.addEventListener('click', function() {
        userMenuDropdown.classList.toggle('show');
    });

    window.addEventListener('click', function(event) {
        if (!event.target.matches('#user-menu-button')) {
            if (userMenuDropdown.classList.contains('show')) {
                userMenuDropdown.classList.remove('show');
            }
        }
    });

    // Function to update user menu with user info
    function updateUserMenu(user) {
        const userName = document.getElementById('user-name');
        const userAvatar = document.getElementById('user-avatar');

        if (user) {
            userName.textContent = user.name;
            userAvatar.src = user.avatar || 'default-avatar.png'; // Default avatar if none provided
        } else {
            userName.textContent = 'Guest';
            userAvatar.src = 'default-avatar.png';
        }
    }

    // Simulated user data for demonstration
    const simulatedUser = {
        name: 'John Doe',
        avatar: 'path/to/avatar.jpg'
    };

    updateUserMenu(simulatedUser);
});