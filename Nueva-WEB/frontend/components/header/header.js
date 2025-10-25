// frontend/components/header/header.js
document.addEventListener("DOMContentLoaded", function() {
    const userMenuButton = document.getElementById("user-menu-button");
    const userMenu = document.getElementById("user-menu");

    userMenuButton.addEventListener("click", function() {
        userMenu.classList.toggle("show");
    });

    // Close the user menu if clicked outside
    window.addEventListener("click", function(event) {
        if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
            userMenu.classList.remove("show");
        }
    });

    // Function to update user info in the header
    function updateUserInfo(user) {
        const userName = document.getElementById("user-name");
        const userAvatar = document.getElementById("user-avatar");

        if (user) {
            userName.textContent = user.name;
            userAvatar.src = user.avatar || 'default-avatar.png'; // Default avatar if none provided
        } else {
            userName.textContent = "Invitado";
            userAvatar.src = 'default-avatar.png';
        }
    }

    // Simulate user login for demonstration
    const loggedInUser = {
        name: "Juan Pérez",
        avatar: "path/to/avatar.jpg"
    };
    updateUserInfo(loggedInUser);
});