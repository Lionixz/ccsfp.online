
const searchInput = document.getElementById('searchInput');
const dateFrom = document.getElementById('dateFrom');
const dateTo = document.getElementById('dateTo');

function updateFilters() {
    const params = new URLSearchParams();
    if (searchInput.value) params.append('search', searchInput.value);
    if (dateFrom.value) params.append('date_from', dateFrom.value);
    if (dateTo.value) params.append('date_to', dateTo.value);

    window.location.href = 'index.php?' + params.toString();
}

searchInput.addEventListener('keyup', () => {
    clearTimeout(window.filterTimeout);
    window.filterTimeout = setTimeout(updateFilters, 300);
});

dateFrom.addEventListener('change', updateFilters);
dateTo.addEventListener('change', updateFilters);
