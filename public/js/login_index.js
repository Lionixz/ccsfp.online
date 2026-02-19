(function () {
    const form = document.getElementById('registerForm');
    const googleUrl = document.getElementById('googleLoginBtn').href;
    form.addEventListener('submit', function (e) {
        e.preventDefault(); // prevent normal form submission
        const email = document.querySelector('input[name="email"]').value.trim();
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('confirm_password').value;
        // 1. Gmail validation (allow + aliases)
        const gmailPattern = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;
        if (!gmailPattern.test(email)) {
            alert("Please enter a valid Gmail address (example@gmail.com).");
            return;
        }
        // 2. Password validation: at least 8 chars, at least one letter and one number
        //    Special characters are allowed (remove the character class restriction)
        const passwordPattern = /^(?=.*[A-Za-z])(?=.*\d).{8,}$/;
        if (!passwordPattern.test(password)) {
            alert("Password must be at least 8 characters long and include both letters and numbers.");
            return;
        }

        // 3. Confirm password
        if (password !== confirm) {
            alert("Passwords do not match!");
            return;
        }
        // ✅ Validation passed → redirect to Google login
        window.location.href = googleUrl;
    });
})();