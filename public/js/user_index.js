document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('insertForm');

    // Field references
    const photo = document.getElementById('photoInput');          // corrected ID
    const courseFirst = document.getElementById('course_first');
    const courseSecond = document.getElementById('course_second');
    const familyIncomeRadios = form.querySelectorAll('input[name="family_income"]');
    const howHeardRadios = form.querySelectorAll('input[name="how_heard"]');
    const howHeardOtherText = document.getElementById('how_heard_other_text');

    // ===== Helper Functions =====
    function showError(input, message) {
        clearError(input);

        // Determine container to place error message
        let container;
        if (input.type === 'radio') {
            // For radio groups, find the parent container (td or div with radio-group class)
            container = input.closest('.radio-group') || input.closest('td');
        } else {
            container = input.parentNode;
        }

        input.classList.add('error');

        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-msg';
        errorDiv.style.color = 'red';
        errorDiv.style.fontSize = '0.9em';
        errorDiv.style.marginTop = '5px';
        errorDiv.innerText = message;
        container.appendChild(errorDiv);
    }

    function clearError(input) {
        input.classList.remove('error');

        // Remove error message from appropriate container
        let container;
        if (input.type === 'radio') {
            container = input.closest('.radio-group') || input.closest('td');
        } else {
            container = input.parentNode;
        }

        const errorMsg = container.querySelector('.error-msg');
        if (errorMsg) errorMsg.remove();
    }

    function validateEmail(email) {
        const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return pattern.test(email);
    }

    function isRadioGroupValid(radioName) {
        return form.querySelector(`input[name="${radioName}"]:checked`) !== null;
    }

    // ===== COURSE CHOICE LOGIC =====
    function updateSecondChoice() {
        const firstValue = courseFirst.value;
        for (let option of courseSecond.options) {
            option.disabled = (option.value === firstValue && firstValue !== "");
            if (courseSecond.value === firstValue) courseSecond.value = "";
        }
    }
    courseFirst.addEventListener('change', updateSecondChoice);
    updateSecondChoice();

    // ===== IMAGE PREVIEW =====
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('preview');
        const overlay = document.querySelector('.overlay-text');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                overlay.style.opacity = '0';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    photo.addEventListener('change', previewImage);

    // ===== HOW HEARD "OTHER" LOGIC =====
    function handleHowHeard() {
        const selectedHowHeard = form.querySelector('input[name="how_heard"]:checked');
        if (selectedHowHeard && selectedHowHeard.value === 'others') {
            howHeardOtherText.disabled = false;
            howHeardOtherText.required = true;   // optionally add HTML5 required
        } else {
            howHeardOtherText.disabled = true;
            howHeardOtherText.required = false;
            howHeardOtherText.value = '';
        }
    }
    howHeardRadios.forEach(radio => radio.addEventListener('change', handleHowHeard));
    handleHowHeard(); // initial state

    // ===== SIBLING ADD/REMOVE =====
    window.addSibling = function () {
        const siblingNames = document.querySelectorAll('input[name="sibling_name[]"]');
        const siblingEducations = document.querySelectorAll('input[name="sibling_education[]"]');
        const siblingOccupations = document.querySelectorAll('input[name="sibling_occupation[]"]');

        // Check that all existing sibling fields are filled before adding new row
        for (let i = 0; i < siblingNames.length; i++) {
            if (!siblingNames[i].value.trim() || !siblingEducations[i].value.trim() || !siblingOccupations[i].value.trim()) {
                alert('Please fill in all sibling details before adding another sibling.');
                return;
            }
        }

        const tbody = document.querySelector('table tbody');
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
    <td colspan="12">
        <input type="text" name="sibling_occupation[]" class="form-input" style="width:100%;">
    </td>
    <td colspan="3" style="text-align:center;">
        <button type="button" class="remove-sibling-btn" onclick="removeSibling(this)">Remove</button>
    </td>
`;


        tbody.insertBefore(row1, addButtonRow);
        tbody.insertBefore(row2, addButtonRow);
    };

    window.removeSibling = function (button) {
        const row1 = button.closest('tr').previousElementSibling;
        const row2 = button.closest('tr');
        row1.remove();
        row2.remove();
    };

    // ===== FORM SUBMISSION VALIDATION =====
    form.addEventListener('submit', function (e) {
        let valid = true;

        // Clear previous errors on all relevant fields
        const allFields = [
            photo, courseFirst, courseSecond,
            ...form.querySelectorAll('input:not([type="radio"]):not([type="file"])'),
            ...form.querySelectorAll('select'),
            ...form.querySelectorAll('textarea')
        ];
        allFields.forEach(clearError);
        // Also clear radio group errors (they don't have individual clear, we'll clear via first radio)
        familyIncomeRadios.forEach(radio => clearError(radio));
        howHeardRadios.forEach(radio => clearError(radio));

        // ===== PHOTO VALIDATION =====
        if (!photo.files.length) {
            valid = false;
            showError(photo, 'Please upload your 1.5 x 1.5 colored picture.');
        } else {
            const file = photo.files[0];
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            const maxSize = 2 * 1024 * 1024; // 2MB
            if (!allowedTypes.includes(file.type)) {
                valid = false;
                showError(photo, 'Only JPG or PNG images are allowed.');
            } else if (file.size > maxSize) {
                valid = false;
                showError(photo, 'Image size must be less than 2MB.');
            }
        }

        // ===== COURSE VALIDATION =====
        if (!courseFirst.value) {
            valid = false;
            showError(courseFirst, 'First course choice is required.');
        }
        if (!courseSecond.value) {
            valid = false;
            showError(courseSecond, 'Second course choice is required.');
        }

        // ===== PERSONAL INFO FIELDS =====
        // List of required fields (excluding radio groups, file, and sibling arrays)
        const requiredFieldNames = [
            'last_name', 'first_name', 'age', 'gender', 'dob', 'birth_place',
            'marital_status', 'contact', 'religion', 'email', 'home_address',
            'relative_name', 'relative_address', 'college', 'college_course',
            'college_address', 'college_year', 'shs', 'shs_year', 'shs_address',
            'shs_lrn', 'shs_awards', 'jhs', 'jhs_year', 'jhs_address', 'jhs_awards',
            'primary_school', 'primary_year', 'skills', 'sports', 'father_name',
            'father_occupation', 'father_employer', 'mother_name', 'mother_occupation',
            'mother_employer', 'guardian_name', 'guardian_occupation', 'guardian_employer',
            'guardian_address', 'guardian_contact'
        ];

        requiredFieldNames.forEach(name => {
            const input = form.querySelector(`[name="${name}"]`);
            if (input && !input.value.trim()) {
                valid = false;
                showError(input, 'This field is required.');
            }
        });

        // ===== RADIO GROUPS =====
        if (!isRadioGroupValid('family_income')) {
            valid = false;
            // show error on the first radio of the group
            showError(familyIncomeRadios[0], 'Please select your family income range.');
        }

        if (!isRadioGroupValid('how_heard')) {
            valid = false;
            showError(howHeardRadios[0], 'Please select how you heard about us.');
        } else {
            // If "others" selected, validate the text field
            const selectedHowHeard = form.querySelector('input[name="how_heard"]:checked');
            if (selectedHowHeard.value === 'others' && !howHeardOtherText.value.trim()) {
                valid = false;
                showError(howHeardOtherText, 'Please specify how you heard about us.');
            }
        }

        // ===== EMAIL FORMAT CHECK =====
        const emailInput = form.querySelector('[name="email"]');
        if (emailInput && emailInput.value && !validateEmail(emailInput.value)) {
            valid = false;
            showError(emailInput, 'Invalid email format.');
        }

        if (!valid) {
            e.preventDefault();
            const firstError = document.querySelector('.error');
            if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
});