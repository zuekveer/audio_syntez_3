//setNewPassword.js
document.getElementById("setNewPasswordForm").addEventListener("submit", function(event) {
    event.preventDefault();

    let formData = {
        email: document.getElementById("email").value,
        token: document.getElementById("token").value,
        password: document.getElementById("password").value
    };
    let url = "/api/reset";

    fetch(url, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(formData)
    })
        .then(response => {
            switch (response.status) {
                case 404:
                    throw new Error("Not found");
                case 403:
                    throw new Error("Access denied");
                default:
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
            }
        })
        .then(data => {

            console.log(data);
            alert("Password update successful!");
        })
        .catch(error => {

            console.error('There was a problem with your fetch operation:', error);
            switch (error.message) {
                case "Not found":
                    alert("Email not found. Please use a different email.");
                    break;
                case "Access denied":
                    alert("Invalid credentials. Please use a different credentials.");
                    break;
                default:
                    alert("Password update failed. Please try again.");
                    break;
            }
        });
});

document.getElementById("showPassword").addEventListener("change", function() {
    let passwordField = document.getElementById("password");
    if (this.checked) {
        passwordField.type = "text";
    } else {
        passwordField.type = "password";
    }
});
