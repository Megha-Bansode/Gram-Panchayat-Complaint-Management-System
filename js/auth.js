document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('[data-auth-form]');

    forms.forEach((form) => {
        form.addEventListener('submit', (event) => {
            const requiredFields = form.querySelectorAll('[data-required]');
            let isValid = true;
            let firstInvalidField = null;

            requiredFields.forEach((field) => {
                const value = field.value.trim();
                if (!value) {
                    isValid = false;
                    field.style.borderColor = '#b45309';
                    if (!firstInvalidField) {
                        firstInvalidField = field;
                    }
                } else {
                    field.style.borderColor = '#d9ceb3';
                }
            });

            const passwordField = form.querySelector('input[type="password"]');
            if (passwordField && passwordField.value.length > 0 && passwordField.value.length < 8) {
                isValid = false;
                passwordField.style.borderColor = '#b45309';
                if (!firstInvalidField) {
                    firstInvalidField = passwordField;
                }
            }

            if (form.querySelector('#confirm_password')) {
                const password = form.querySelector('#password');
                const confirm = form.querySelector('#confirm_password');
                if (password && confirm && confirm.value.trim() !== '' && password.value !== confirm.value) {
                    isValid = false;
                    confirm.style.borderColor = '#b45309';
                    if (!firstInvalidField) {
                        firstInvalidField = confirm;
                    }
                }
            }

            if (!isValid) {
                event.preventDefault();
                if (firstInvalidField) {
                    firstInvalidField.focus();
                }
                const alertBox = form.querySelector('.alert');
                if (alertBox) {
                    alertBox.innerText = 'Please complete the highlighted fields correctly.';
                }
            }
        });
    });
});
