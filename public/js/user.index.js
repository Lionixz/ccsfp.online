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







function addSibling() {
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

// Remove both rows of a sibling
function removeSibling(button) {
    const row2 = button.closest('tr');
    const row1 = row2.previousElementSibling;

    if (row1 && row1.classList.contains('sibling-row')) {
        row1.remove();
    }
    row2.remove();
}









document.addEventListener('DOMContentLoaded', function () {

    const radios = document.querySelectorAll('input[name="how_heard"]');
    const otherRadio = document.getElementById('how_heard_other_radio');
    const otherText = document.getElementById('how_heard_other_text');

    if (!otherRadio || !otherText) return;

    function updateOtherState() {
        if (otherRadio.checked) {
            otherText.disabled = false;
            otherText.required = true;
        } else {
            otherText.disabled = true;
            otherText.required = false;
            otherText.value = '';
        }
    }

    // When any radio changes
    radios.forEach(radio => {
        radio.addEventListener('change', updateOtherState);
    });

    // ⭐ KEY FIX: clicking text selects "Other"
    otherText.addEventListener('focus', function () {
        otherRadio.checked = true;
        updateOtherState();
    });

});
