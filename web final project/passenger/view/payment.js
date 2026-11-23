document.getElementById("paymentForm").addEventListener("submit", function(e) {
    let amount = document.getElementById("amount").value.trim();
    let method = document.getElementById("payment_method").value;

    if (amount === "" || isNaN(amount) || Number(amount) <= 0) {
        e.preventDefault();
        alert("Please enter a valid fare amount.");
        return;
    }

    if (method === "") {
        e.preventDefault();
        alert("Please select a payment method.");
        return;
    }
});

function updateBalance() {
    const xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
        if (this.status === 200) {
            try {
                const data = JSON.parse(xhttp.responseText);
                if (data && typeof data.balance !== 'undefined') {
                    const span = document.getElementById("balanceSpan");
                    if (span) {
                        span.textContent = Number(data.balance).toFixed(2) + " BDT";
                    }
                }
            } catch (err) {
                // Ignore parse errors
            }
        }
    };
    xhttp.open("GET", "Balance.php", true);
    xhttp.send();
}

// Update balance periodically
updateBalance();
setInterval(updateBalance, 5000);
