//login.js
document.getElementById("loginForm").addEventListener("submit", function(event) {
    event.preventDefault();

    let formData = {
        email: document.getElementById("email").value,
        password: document.getElementById("password").value
    };
    let url = "/api/login";

    fetch(url, {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(formData)
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.token) {
                alert("Login successful!");

                let token = data.token;

                document.cookie = `token=${token}; path=/`;
                window.location.href = "/personal-account";
            } else {
                throw new Error('Token not found in response');
            }
        })
        .catch(error => {

            console.error('There was a problem with your fetch operation:', error);
            alert("Login failed. Please check credentials and try again.");
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