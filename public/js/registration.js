//registration.js
document.getElementById("registrationForm").addEventListener("submit", function(event) {
    event.preventDefault();

    let formData = {
        name: document.getElementById("name").value,
        email: document.getElementById("email").value,
        phone: document.getElementById("phone").value,
        password: document.getElementById("password").value
    };
    let url = "/api/registration";

    fetch(url, {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(formData)
    })
        .then(response => {
            switch (response.status) {
                case 409:
                    throw new Error("Duplicate");
                default:
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
            }
        })
        .then(data => {
            console.log(data);

            alert("Registration successful! Thank you for registering!");
            window.location.href = "/verify-code";
        })
        .catch(error => {
            console.error('There was a problem with your fetch operation:', error);
            switch (error.message) {
                case "Duplicate":
                    alert("Email already exists. Please use a different email.");
                    break;
                default:
                    alert("Registration failed. Please try again.");
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
