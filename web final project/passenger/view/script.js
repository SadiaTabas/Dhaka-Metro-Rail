function validateForm() {
    let fullname = document.getElementById("fullname").value.trim();
    let mobile = document.getElementById("mobile").value.trim();
    let nid = document.getElementById("nid").value.trim();
    let dob = document.getElementById("dob").value.trim();
    let gender = document.getElementById("gender").value.trim();
    let password = document.getElementById("password").value;
    let confirm = document.getElementById("confirm_password").value;

    if (!fullname || !mobile || !nid || !dob || !gender || !password || !confirm) {
        alert("Please fill in all required fields.");
        return false;
    }

    let mobileRegex = /^01[3-9]\d{8}$/;
    if (!mobileRegex.test(mobile)) {
        alert("Mobile number must be a valid 11-digit Bangladeshi number (01XXXXXXXXX).");
        return false;
    }

    if (isNaN(nid) || nid.length < 10) {
        alert("NID number must be a numeric value of at least 10 digits.");
        return false;
    }

    if (password.length < 6) {
        alert("Password must be at least 6 characters long.");
        return false;
    }

    if (password !== confirm) {
        alert("Passwords do not match. Please re-enter.");
        return false;
    }

    return true;
}
