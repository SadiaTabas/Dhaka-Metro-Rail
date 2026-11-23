function validateForm() {
    let fullNameInput = document.getElementById("full_name") || (document.forms["profileForm"] && document.forms["profileForm"]["full_name"]);
    let dobInput = document.getElementById("dob") || (document.forms["profileForm"] && document.forms["profileForm"]["dob"]);
    let genderInput = document.getElementById("gender") || (document.forms["profileForm"] && document.forms["profileForm"]["gender"]);

    let fullName = fullNameInput ? fullNameInput.value.trim() : "";
    let dob = dobInput ? dobInput.value : "";
    let gender = genderInput ? genderInput.value : "";

    if (!fullName) {
        alert("Full Name must be filled out");
        return false;
    }

    if (!dob) {
        alert("Date of Birth must be selected");
        return false;
    }

    if (!gender) {
        alert("Please select a Gender");
        return false;
    }

    return true;
}
