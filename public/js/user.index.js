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
    const addButtonRow = document.querySelector('.btn-add').closest('tr');

    // Row 1: Name + Educational Attainment
    const row1 = document.createElement('tr');
    row1.classList.add('sibling-row');
    row1.innerHTML = `
    <td colspan="3">Name:</td>
    <td colspan="11"><input type="text" name="sibling_name[]" class="form-input" style="width:100%;"></td>
    <td colspan="5">Educational Attainment:</td>
    <td colspan="5"><input type="text" name="sibling_education[]" class="form-input" style="width:100%;"></td>
    `;

    // Row 2: Occupation / Employer / School
    const row2 = document.createElement('tr');
    row2.classList.add('sibling-row');
    row2.innerHTML = `
    <td colspan="7">Occupation/Employer/School Attending:</td>
    <td colspan="15"><input type="text" name="sibling_occupation[]" class="form-input" style="width:100%;"></td>
    <td colspan="2">
        <button type="button" onclick="removeSibling(this)">Remove</button>
    </td>
    `;

    // Insert before Add Sibling button row
    tbody.insertBefore(row1, addButtonRow);
    tbody.insertBefore(row2, addButtonRow);
}

// Remove a sibling row
function removeSibling(button) {
    const row2 = button.closest('tr'); // row with button
    const row1 = row2.previousElementSibling; // row above it (Name + Education)
    row2.remove();
    row1.remove();
}

