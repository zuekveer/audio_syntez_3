//verifyCode.js
document.getElementById("verifyCodeForm").addEventListener("submit", function(event) {
    event.preventDefault();

    let formData = {
        email: document.getElementById("email").value,
        token: document.getElementById("token").value
    };
    let url = "/api/verify-code";

    fetch(url, {
        method: "POST",
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
                case 400:
                    throw new Error("Bad request");
                default:
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
            }
        })
        .then(data => {

            console.log(data);
            alert("Verification successful!");
            window.location.href = "/login";
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
                case "Bad request":
                    alert("Bad request. Please try again.");
                    break;
                default:
                    alert("Verification failed. Please try again.");
                    break;
            }
        });
});
