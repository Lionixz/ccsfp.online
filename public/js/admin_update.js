document.addEventListener('DOMContentLoaded', function () {
    // ===== ELEMENT REFERENCES =====
    const courseFirst = document.getElementById('course_first');
    const courseSecond = document.getElementById('course_second');
    const photoInput = document.getElementById('photoInput');
    const preview = document.getElementById('preview');
    const overlay = document.querySelector('.overlay-text');
    const howHeardRadios = document.querySelectorAll('input[name="how_heard"]');
    const howHeardOtherText = document.getElementById('how_heard_other_text');
    const updateForm = document.getElementById('updateForm');

    // ===== COURSE CHOICE LOGIC (disable same selection) =====
    if (courseFirst && courseSecond) {
        function updateSecondChoice() {
            const firstValue = courseFirst.value;
            Array.from(courseSecond.options).forEach(option => {
                option.disabled = (option.value === firstValue && firstValue !== "");
                if (courseSecond.value === firstValue) courseSecond.value = "";
            });
        }
        courseFirst.addEventListener('change', updateSecondChoice);
        updateSecondChoice(); // run on page load
    }

    // ===== IMAGE PREVIEW =====
    if (photoInput && preview) {
        photoInput.addEventListener('change', function (event) {
            const input = event.target;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    if (overlay) overlay.style.opacity = '0';
                };
                reader.readAsDataURL(input.files[0]);
            }
        });
    }

    // ===== HOW HEARD "OTHER" LOGIC =====
    if (howHeardRadios.length && howHeardOtherText) {
        function handleHowHeard() {
            const otherChecked = document.querySelector('input[name="how_heard"][value="others"]')?.checked;
            if (otherChecked) {
                howHeardOtherText.disabled = false;
            } else {
                howHeardOtherText.disabled = true;
                howHeardOtherText.value = '';
            }
        }
        howHeardRadios.forEach(radio => radio.addEventListener('change', handleHowHeard));
        handleHowHeard(); // run on page load
    }

    // ===== SIBLING ADD/REMOVE =====
    window.addSibling = function () {
        const siblingNames = document.querySelectorAll('input[name="sibling_name[]"]');
        const siblingEducations = document.querySelectorAll('input[name="sibling_education[]"]');
        const siblingOccupations = document.querySelectorAll('input[name="sibling_occupation[]"]');

        // Optional: check if last row is filled before adding
        if (siblingNames.length > 0) {
            const lastIndex = siblingNames.length - 1;
            if (!siblingNames[lastIndex].value.trim() ||
                !siblingEducations[lastIndex].value.trim() ||
                !siblingOccupations[lastIndex].value.trim()) {
                alert('Please fill in all sibling details before adding another sibling.');
                return;
            }
        }

        const tbody = document.querySelector('#applicant-table tbody');
        const addButtonRow = document.querySelector('.add-sibling-row');

        const row1 = document.createElement('tr');
        row1.classList.add('sibling-row');
        row1.innerHTML = `
            <td colspan="3">Name:</td>
            <td colspan="11"><input type="text" name="sibling_name[]" class="form-input" style="width:100%;"></td>
            <td colspan="5">Educational Attainment:</td>
            <td colspan="5"><input type="text" name="sibling_education[]" class="form-input" style="width:100%;"></td>
        `;

        const row2 = document.createElement('tr');
        row2.classList.add('sibling-row');
        row2.innerHTML = `
            <td colspan="9">Occupation / Employer / School Attending:</td>
            <td colspan="12"><input type="text" name="sibling_occupation[]" class="form-input" style="width:100%;"></td>
            <td colspan="3" style="text-align:center;">
                <button type="button" class="remove-sibling-btn" onclick="removeSibling(this)">Remove</button>
            </td>
        `;

        tbody.insertBefore(row1, addButtonRow);
        tbody.insertBefore(row2, addButtonRow);
    };

    window.removeSibling = function (button) {
        const row2 = button.closest('tr');
        const row1 = row2.previousElementSibling;
        if (row1 && row1.classList.contains('sibling-row')) {
            row1.remove();
            row2.remove();
        }
    };

    // ===== FORM VALIDATION =====
    if (updateForm) {
        updateForm.addEventListener('submit', function (e) {
            let valid = true;

            // Helper functions
            function showError(input, message) {
                input.classList.add('error');
                let container = input.parentNode;
                if (!container.querySelector('.error-msg')) {
                    const small = document.createElement('div');
                    small.className = 'error-msg';
                    small.style.color = 'red';
                    small.style.fontSize = '0.9em';
                    small.innerText = message;
                    container.appendChild(small);
                }
            }

            function clearError(input) {
                input.classList.remove('error');
                const next = input.nextElementSibling;
                if (next && next.classList.contains('error-msg')) {
                    next.remove();
                }
            }

            function validateEmail(email) {
                const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return pattern.test(email);
            }

            // Clear previous errors
            document.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
            document.querySelectorAll('.error-msg').forEach(el => el.remove());

            // --- Photo validation ---

            if (!photoInput.files.length && !hasExistingPhoto) {
                valid = false;
                showError(photoInput, 'Please upload your 1.5 x 1.5 colored picture.');
            } else if (photoInput.files.length) {
                const file = photoInput.files[0];
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                const maxSize = 2 * 1024 * 1024; // 2MB
                if (!allowedTypes.includes(file.type)) {
                    valid = false;
                    showError(photoInput, 'Only JPG or PNG images are allowed.');
                } else if (file.size > maxSize) {
                    valid = false;
                    showError(photoInput, 'Image size must be less than 2MB.');
                }
            }

            // --- Course validation ---
            if (!courseFirst.value) {
                valid = false;
                showError(courseFirst, 'First course choice is required.');
            }
            if (!courseSecond.value) {
                valid = false;
                showError(courseSecond, 'Second course choice is required.');
            }

            // --- Required fields (excluding photo, course, and special cases) ---
            const requiredFields = [
                'last_name', 'first_name', 'middle_name', 'age', 'gender', 'dob',
                'birth_place', 'marital_status', 'contact', 'religion', 'email',
                'home_address', 'relative_name', 'relative_address', 'college', 'college_course',
                'college_address', 'college_year', 'shs', 'shs_year', 'shs_address', 'shs_lrn',
                'shs_awards', 'jhs', 'jhs_year', 'jhs_address', 'jhs_awards',
                'primary_school', 'primary_year', 'skills', 'sports', 'father_name',
                'father_occupation', 'father_employer', 'mother_name', 'mother_occupation', 'mother_employer',
                'guardian_name', 'guardian_occupation', 'guardian_employer', 'guardian_address', 'guardian_contact'
            ];

            requiredFields.forEach(name => {
                const input = document.querySelector(`[name="${name}"]`);
                if (input && !input.value.trim()) {
                    valid = false;
                    showError(input, 'This field is required.');
                }
            });

            // --- Family income required ---
            const incomeChecked = document.querySelector('input[name="family_income"]:checked');
            if (!incomeChecked) {
                valid = false;
                // Show error on the first radio button
                const firstIncomeRadio = document.querySelector('input[name="family_income"]');
                if (firstIncomeRadio) showError(firstIncomeRadio, 'Please select family income.');
            }

            // --- How heard required ---
            const howHeardChecked = document.querySelector('input[name="how_heard"]:checked');
            if (!howHeardChecked) {
                valid = false;
                showError(howHeardRadios[0], 'Please select an option.');
            } else {
                if (howHeardChecked.value === 'others' && !howHeardOtherText.value.trim()) {
                    valid = false;
                    showError(howHeardOtherText, 'Please specify how you heard about us.');
                }
            }

            // --- Email format ---
            const email = document.querySelector('[name="email"]');
            if (email && email.value && !validateEmail(email.value)) {
                valid = false;
                showError(email, 'Invalid email format.');
            }

            if (!valid) {
                e.preventDefault();
                const firstError = document.querySelector('.error');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }
});