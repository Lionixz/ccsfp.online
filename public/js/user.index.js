
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');

    // Fields
    const photo = document.getElementById('photo');
    const courseFirst = document.getElementById('course_first');
    const courseSecond = document.getElementById('course_second');

    // Required personal info fields
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
        'family_income', 'how_heard','how_heard_other', 
        'sibling_name[]', 'sibling_education[]', 'sibling_occupation[]'

    ];

    function showError(input, message) {
        input.classList.add('error');
        if (!input.nextElementSibling || !input.nextElementSibling.classList.contains('error-msg')) {
            const small = document.createElement('div');
            small.className = 'error-msg';
            small.innerText = message;
            input.parentNode.appendChild(small);
        }
    }

    function clearError(input) {
        input.classList.remove('error');
        if (input.nextElementSibling && input.nextElementSibling.classList.contains('error-msg')) {
            input.nextElementSibling.remove();
        }
    }


    form.addEventListener('submit', function (e) {
        let valid = true;

        // Clear previous errors
        [photo, courseFirst, courseSecond].forEach(clearError);
        requiredFields.forEach(name => {
            const input = form.querySelector(`[name="${name}"]`);
            if (input) clearError(input);
        });

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

        // ===== PERSONAL INFO VALIDATION =====
        requiredFields.forEach(name => {
            const input = form.querySelector(`[name="${name}"]`);
            if (input && !input.value.trim()) {
                valid = false;
                showError(input, 'This field is required.');
            }
            
        });

        // ===== EMAIL FORMAT CHECK =====
        const email = form.querySelector('[name="email"]');
        if (email.value) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email.value)) {
                valid = false;
                showError(email, 'Invalid email format.');
            }
        }


        if (!valid) {
            e.preventDefault();
            // Scroll to first invalid field
            const firstError = document.querySelector('.error');
            if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
});


// Get the select elements
const courseFirst = document.getElementById('course_first');
const courseSecond = document.getElementById('course_second');

// Function to update second choice options
function updateSecondChoice() {
    const firstValue = courseFirst.value;
    // Loop through second course options
    for (let option of courseSecond.options) {
        if (option.value === firstValue && firstValue !== "") {
            option.disabled = true; // Disable the selected first choice
            if (courseSecond.value === firstValue) {
                courseSecond.value = ""; // Reset if already selected
            }
        } else {
            option.disabled = false; // Enable other options
        }
    }
}

// Listen for changes on the first dropdown
courseFirst.addEventListener('change', updateSecondChoice);

// Optional: run on page load in case a value is preselected
window.addEventListener('DOMContentLoaded', updateSecondChoice);


function previewImage(event) {
    const input = event.target;
    const preview = document.getElementById('preview');
    const overlay = document.querySelector('.overlay-text');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = 'block'; // show the image
            overlay.style.opacity = '0'; // hide overlay
        };

        reader.readAsDataURL(input.files[0]);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const howHeardOtherRadio = document.getElementById('how_heard_other_radio');
    const howHeardOtherText = document.getElementById('how_heard_other_text');
    const radioButtons = document.querySelectorAll('input[name="how_heard"]');

    // Initially disable the text input
    howHeardOtherText.disabled = true;

    // Function to handle enabling/disabling the text input based on the selected radio button
    function handleRadioSelection() {
        // Check if "Other" radio button is selected
        if (howHeardOtherRadio.checked) {
            howHeardOtherText.disabled = false; // Enable the text input
        } else {
            howHeardOtherText.disabled = true; // Disable the text input
            howHeardOtherText.value = ''; // Clear the text input when it's disabled
        }
    }

    // Add event listeners to all radio buttons
    radioButtons.forEach(radio => {
        radio.addEventListener('change', handleRadioSelection);
    });

    // Call function on page load to ensure the initial state is correct
    handleRadioSelection();
});

function addSibling() {
    const siblingNames = document.querySelectorAll('input[name="sibling_name[]"]');
    const siblingEducations = document.querySelectorAll('input[name="sibling_education[]"]');
    const siblingOccupations = document.querySelectorAll('input[name="sibling_occupation[]"]');

    // Check if the previous sibling fields are filled out
    for (let i = 0; i < siblingNames.length; i++) {
        if (siblingNames[i].value.trim() === '' || siblingEducations[i].value.trim() === '' || siblingOccupations[i].value.trim() === '') {
            alert('Please fill in all sibling details before adding another sibling.');
            return; // Prevent adding more rows if validation fails
        }
    }

    const tbody = document.querySelector('table tbody');
    const addButtonRow = document.querySelector('.add-sibling-row');

    // Row 1: Name + Educational Attainment
    const row1 = document.createElement('tr');
    row1.classList.add('sibling-row');
    row1.innerHTML = `
        <td colspan="3">Name:</td>
        <td colspan="11">
            <input type="text" name="sibling_name[]" class="form-input" style="width:100%;">
        </td>
        <td colspan="5">Educational Attainment:</td>
        <td colspan="5">
            <input type="text" name="sibling_education[]" class="form-input" style="width:100%;">
        </td>
    `;

    // Row 2: Occupation / Employer / School + Remove button
    const row2 = document.createElement('tr');
    row2.classList.add('sibling-row');
    row2.innerHTML = `
        <td colspan="9">Occupation / Employer / School Attending:</td>
        <td colspan="12">
            <input type="text" name="sibling_occupation[]" class="form-input" style="width:100%;">
        </td>
        <td colspan="3" style="text-align:center;">
            <button type="button" onclick="removeSibling(this)">Remove</button>
        </td>
    `;

    // Insert both rows **above Add Sibling button**
    tbody.insertBefore(row1, addButtonRow);
    tbody.insertBefore(row2, addButtonRow);
}
