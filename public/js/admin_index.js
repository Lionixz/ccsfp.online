const searchInput = document.getElementById('searchInput');
const dateFrom = document.getElementById('dateFrom');
const dateTo = document.getElementById('dateTo');
const tableDiv = document.getElementById('applicantsTable');
let timeout = null;

function fetchTable() {
    const params = new URLSearchParams();
    if (searchInput.value) params.append('search', searchInput.value);
    if (dateFrom.value) params.append('date_from', dateFrom.value);
    if (dateTo.value) params.append('date_to', dateTo.value);
    params.append('ajax', '1'); // flag for AJAX

    fetch('index.php?' + params.toString())
        .then(res => res.text())
        .then(html => {
            tableDiv.innerHTML = html;
        });
}

// debounce typing
searchInput.addEventListener('keyup', () => {
    clearTimeout(timeout);
    timeout = setTimeout(fetchTable, 300);
});

dateFrom.addEventListener('change', fetchTable);
dateTo.addEventListener('change', fetchTable);
