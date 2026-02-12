document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');

    // Fields
    const photo = document.getElementById('photo');
    const courseFirst = document.getElementById('course_first');
    const courseSecond = document.getElementById('course_second');
    const familyIncomeRadios = form.querySelectorAll('input[name="family_income"]');
    const howHeardRadios = form.querySelectorAll('input[name="how_heard"]');
    const howHeardOtherRadio = document.getElementById('how_heard_other_radio');
    const howHeardOtherText = document.getElementById('how_heard_other_text');

    // Required fields
    const requiredFields = [
        'users_id', 'course_first', 'course_second', 'photo', 'last_name',
        'first_name', 'middle_name', 'age', 'gender', 'dob',
        'birth_place', 'marital_status', 'contact', 'religion', 'email',
        'home_address', 'relative_name', 'relative_address', 'college', 'college_course',
        'college_address', 'college_year', 'shs', 'shs_year', 'shs_address', 'shs_lrn',
        'shs_awards', 'jhs', 'jhs_year', 'jhs_address', 'jhs_awards',
        'primary_school', 'primary_year', 'skills', 'sports', 'father_name',
        'father_occupation', 'father_employer', 'mother_name', 'mother_occupation', 'mother_employer',
        'guardian_name', 'guardian_occupation', 'guardian_employer', 'guardian_address', 'guardian_contact',
        'family_income', 'how_heard', 'how_heard_other',
        'sibling_name[]', 'sibling_education[]', 'sibling_occupation[]'
    ];

    // ===== Helper Functions =====
    function showError(input, message) {
        input.classList.add('error');
        let container = input.parentNode;
        if (input.id === 'photo') {
            container = input.parentNode.querySelector('.preview-container'); // visible label
        }

        if (!container.querySelector('.error-msg')) {
            const small = document.createElement('div');
            small.className = 'error-msg';
            small.style.color = 'red';
            small.style.fontSize = '0.9em';
            small.style.marginTop = '5px';
            small.innerText = message;
            container.appendChild(small);
        }
    }

    function clearError(input) {
        input.classList.remove('error');
        if (input.nextElementSibling && input.nextElementSibling.classList.contains('error-msg')) {
            input.nextElementSibling.remove();
        }
    }

    function validateEmail(email) {
        const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return pattern.test(email);
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
        if (howHeardOtherRadio.checked) {
            howHeardOtherText.disabled = false;
        } else {
            howHeardOtherText.disabled = true;
            howHeardOtherText.value = '';
        }
    }
    howHeardRadios.forEach(radio => radio.addEventListener('change', handleHowHeard));
    handleHowHeard();

    // ===== SIBLING ADD/REMOVE =====
    window.addSibling = function () {
        const siblingNames = document.querySelectorAll('input[name="sibling_name[]"]');
        const siblingEducations = document.querySelectorAll('input[name="sibling_education[]"]');
        const siblingOccupations = document.querySelectorAll('input[name="sibling_occupation[]"]');

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
            <td colspan="12"><input type="text" name="sibling_occupation[]" class="form-input" style="width:100%;"></td>
            <td colspan="3" style="text-align:center;"><button type="button" onclick="removeSibling(this)">Remove</button></td>
        `;

        tbody.insertBefore(row1, addButtonRow);
        tbody.insertBefore(row2, addButtonRow);
    }

    window.removeSibling = function (button) {
        const row1 = button.closest('tr').previousElementSibling;
        const row2 = button.closest('tr');
        row1.remove();
        row2.remove();
    }

    // ===== FORM SUBMISSION =====
    form.addEventListener('submit', function (e) {
        let valid = true;

        // Clear previous errors
        [photo, courseFirst, courseSecond, ...form.querySelectorAll('input, select')].forEach(clearError);

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
        if (!courseFirst.value) { valid = false; showError(courseFirst, 'First course choice is required.'); }
        if (!courseSecond.value) { valid = false; showError(courseSecond, 'Second course choice is required.'); }

        // ===== PERSONAL INFO VALIDATION =====
        requiredFields.forEach(name => {
            if (name === 'how_heard') {
                const checked = form.querySelector('input[name="how_heard"]:checked');
                if (!checked) { valid = false; showError(howHeardRadios[0], 'This field is required.'); }
            } else if (name === 'how_heard_other') {
                if (howHeardOtherRadio.checked && !howHeardOtherText.value.trim()) {
                    valid = false; showError(howHeardOtherText, 'Please specify how you heard about us.');
                }
            } else if (name === 'family_income') {
                const checked = form.querySelector('input[name="family_income"]:checked');
                if (!checked) { valid = false; showError(familyIncomeRadios[0], 'This field is required.'); }
            } else {
                const input = form.querySelector(`[name="${name}"]`);
                if (input && !input.value.trim()) { valid = false; showError(input, 'This field is required.'); }
            }
        });

        // ===== EMAIL FORMAT CHECK =====
        const email = form.querySelector('[name="email"]');
        if (email.value && !validateEmail(email.value)) {
            valid = false;
            showError(email, 'Invalid email format.');
        }

        if (!valid) {
            e.preventDefault();
            const firstError = document.querySelector('.error');
            if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
});