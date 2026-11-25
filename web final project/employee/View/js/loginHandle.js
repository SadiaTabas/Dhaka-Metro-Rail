function validateForm() {
    let phoneInput = document.getElementById("phone").value.trim();
    let password = document.getElementById("password").value.trim();
    let phoneError = "";
    let passwordError = "";
    let isValid = true;

    // Normalize phone input
    let clean = phoneInput.replace(/[\s\-\(\)]+/g, '');
    if (clean.indexOf("+88") === 0) clean = clean.substring(3);
    else if (clean.indexOf("880") === 0) clean = clean.substring(2);

    let isMobile = /^01[3-9]\d{8}$/.test(clean);
    let isUserId = /^\d{1,8}$/.test(clean);

    if (phoneInput === "") {
        phoneError = "Mobile number or User ID is required.";
        isValid = false;
    } else if (!isMobile && !isUserId) {
        phoneError = "Please enter a valid 11-digit mobile (01XXXXXXXXX) or User ID.";
        isValid = false;
    }

    if (password === "") {
        passwordError = "Password is required.";
        isValid = false;
    }

    // Display errors
    document.getElementById("phoneError").textContent = phoneError;
    document.getElementById("passwordError").textContent = passwordError;

    return isValid;
}
