function validateForm() {

    let fullName = document.forms["profileForm"]["full_name"].value.trim();
    let dob = document.forms["profileForm"]["dob"].value;
    let gender = document.forms["profileForm"]["gender"].value;


    if (fullName == "") {
        alert("Full Name must be filled out");
        return false;
    }

    if (!/^[a-zA-Z ]+$/.test(fullName)) {
        alert("Full Name can only contain letters and spaces");
        return false;
    }


    if (dob == "") {
        alert("Date of Birth must be selected");
        return false;
    }


    if (gender == "") {
        alert("Please select a Gender");
        return false;
    }


    return true;
}


function cancelForm() {
    window.location.href = "dashboard.php";
}
