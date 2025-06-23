document.addEventListener('DOMContentLoaded', function() {
    
    const searchBtn = document.getElementById('search-btn');
    const searchInput = document.getElementById('search-input');

    if (searchBtn && searchInput) {
        searchBtn.addEventListener('click', function() {
            const query = searchInput.value.trim();
            if (query) {
                window.location.href = `browse.php?search=${encodeURIComponent(query)}`;
            }
        });

        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const query = searchInput.value.trim();
                if (query) {
                    window.location.href = `browse.php?search=${encodeURIComponent(query)}`;
                }
            }
        });
    }

    const authWrapper = document.querySelector('.auth-wrapper');
    const registerLink = document.querySelector('.register-link');
    const loginLink = document.querySelector('.login-link');

    if (authWrapper && registerLink && loginLink) {
        registerLink.addEventListener('click', (e) => {
            e.preventDefault();
            authWrapper.classList.add('active');
        });

        loginLink.addEventListener('click', (e) => {
            e.preventDefault();
            authWrapper.classList.remove('active');
        });
    }

    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    const dropdownTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
    dropdownTriggerList.map(function (dropdownTriggerEl) {
        return new bootstrap.Dropdown(dropdownTriggerEl);
    });

    const applyModal = document.getElementById('applyModal');
    if (applyModal) {
        applyModal.addEventListener('shown.bs.modal', function () {

        });
    }

    function validateForm(formId) {
        const form = document.getElementById(formId);
        if (form) {
            form.addEventListener('submit', function(e) {
                let valid = true;
                const inputs = form.querySelectorAll('input[required], textarea[required]');

                inputs.forEach(input => {
                    if (!input.value.trim()) {
                        input.classList.add('is-invalid');
                        valid = false;
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });

                if (!valid) {
                    e.preventDefault();
                    const firstInvalid = form.querySelector('.is-invalid');
                    if (firstInvalid) {
                        firstInvalid.focus();
                    }
                }
            });
        }
    }

    // Initialize validations for your forms
    validateForm('loginForm');
    validateForm('registerForm');
    validateForm('applyForm');
});
