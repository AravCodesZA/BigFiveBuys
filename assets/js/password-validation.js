function validatePassword() {
    var pass = document.getElementById("password").value;
    var confirm = document.getElementById("confirm_password").value;
    if (pass !== confirm) {
        alert("Passwords do not match.");
        return false;
    }
    if (pass.length < 6) {
        alert("Password must be at least 6 characters.");
        return false;
    }
    return true;
}
