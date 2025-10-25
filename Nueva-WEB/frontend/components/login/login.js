// frontend/components/login/login.js
document.addEventListener("DOMContentLoaded", function() {
    const loginForm = document.getElementById("loginForm");
    const messageBox = document.getElementById("messageBox");

    loginForm.addEventListener("submit", function(event) {
        event.preventDefault();
        
        const formData = new FormData(loginForm);
        const data = {
            username: formData.get("username"),
            password: formData.get("password")
        };

        fetch("/api/auth/login", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageBox.textContent = "Login successful!";
                messageBox.style.color = "green";
                // Redirect or update UI after successful login
            } else {
                messageBox.textContent = data.message || "Login failed. Please try again.";
                messageBox.style.color = "red";
            }
        })
        .catch(error => {
            messageBox.textContent = "An error occurred. Please try again.";
            messageBox.style.color = "red";
        });
    });
});