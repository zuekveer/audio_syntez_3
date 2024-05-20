//resetPassword.js
document.getElementById("resetForm").addEventListener("submit", function(event) {
    event.preventDefault();

    let formData = {
        email: document.getElementById("email").value
    };
    let url = "/api/reset";

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
                default:
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
            }
        })
        .then(data => {

            console.log(data);
            alert("Reset successful!");
        })
        .catch(error => {

            console.error('There was a problem with your fetch operation:', error);
            switch (error.message) {
                case "Not found":
                    alert("Email not found. Please use a different email.");
                    break;
                default:
                    alert("Reset failed. Please try again.");
                    break;
            }
        });
});