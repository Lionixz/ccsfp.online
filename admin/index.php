<?php

include_once('../middleware/checkSession.php'); // ensures session check
include_once('../middleware/cache.php');        // include cache once
include(__DIR__ . "/../config/db.php");

/* ===============================
   TOTAL APPLICANTS COUNT
================================= */
$totalQuery = $conn->query("SELECT COUNT(*) as total FROM applicants");
$totalRow = $totalQuery->fetch_assoc();
$totalApplicants = $totalRow['total'] ?? 0;
$uploadBaseUrl = '/ccsfp/public/images/uploads/';

/* ===============================
   FILTERS
================================= */
$search = $_GET['search'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$isAjax = isset($_GET['ajax']) && $_GET['ajax'] == 1;

$where = "WHERE 1=1";

if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $where .= " AND (
        id LIKE '%$search%'
        OR first_name LIKE '%$search%'
        OR last_name LIKE '%$search%'
        OR email LIKE '%$search%'
        OR contact LIKE '%$search%'
    )";
}

if (!empty($date_from) && !empty($date_to)) {
    $where .= " AND created_at BETWEEN '$date_from 00:00:00' AND '$date_to 23:59:59'";
}

/* ===============================
   FETCH APPLICANTS
================================= */
$query = $conn->query("SELECT id, course_first, course_second, photo, last_name, first_name, middle_name, contact, email, created_at FROM applicants $where ORDER BY id DESC LIMIT 50");
$applicants = $query->fetch_all(MYSQLI_ASSOC);

/* ===============================
   TABLE RENDER FUNCTION
================================= */
function renderApplicantsTable($applicants, $uploadBaseUrl) {
    ?>
    <table border="1" width="100%" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Photo</th>
                <th>Full Name</th>
                <th>Course 1</th>
                <th>Course 2</th>
                <th>Contact</th>
                <th>Email</th>
                <th>Applied Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($applicants)) : ?>
                <?php foreach ($applicants as $row) : ?>
                    <?php $photoFile = str_replace('uploads/', '', $row['photo']); ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td>
                            <?php if (!empty($row['photo'])) : ?>
                                <img src="<?= $uploadBaseUrl . urlencode($photoFile) ?>" width="40" height="40" style="object-fit:cover; border-radius:50%;">
                            <?php else : ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['last_name']) ?>, <?= htmlspecialchars($row['first_name']) ?> <?= htmlspecialchars($row['middle_name']) ?></td>
                        <td><?= htmlspecialchars($row['course_first']) ?></td>
                        <td><?= htmlspecialchars($row['course_second']) ?></td>
                        <td><?= htmlspecialchars($row['contact']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                        <td>
                            <a href="view.php?id=<?= $row['id'] ?>">View</a>
                            <a href="update.php?id=<?= $row['id'] ?>">update</a>
                            <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this applicant?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="9" style="text-align:center;">No applicants found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php
}

/* ===============================
   RETURN TABLE ONLY FOR AJAX
================================= */
if ($isAjax) {
    renderApplicantsTable($applicants, $uploadBaseUrl);
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<?php includeAndCache('../includes/admin_head.php'); ?>

<body>
<?php includeAndCache('../includes/admin_sidebar.php'); ?>

<main>
    <div class="container">

        <!-- SUMMARY CARD -->
        <div class="dashboard-summary">
            <div class="summary-card">
                <h3>Total Applicants</h3>
                <p><?= $totalApplicants; ?></p>
            </div>
        </div>

        <!-- FILTERS -->
        <div style="margin:20px 0; display:flex; gap:10px; flex-wrap:wrap;">
            <input type="text" id="searchInput" placeholder="Search name, email, contact" style="padding:5px; width:250px;" value="<?= htmlspecialchars($search) ?>">
            <input type="date" id="dateFrom" style="padding:5px;" value="<?= htmlspecialchars($date_from) ?>">
            <input type="date" id="dateTo" style="padding:5px;" value="<?= htmlspecialchars($date_to) ?>">
        </div>

        <!-- TABLE -->
        <div id="applicantsTable">
            <?php renderApplicantsTable($applicants, $uploadBaseUrl); ?>
        </div>

    </div>
</main>

<script>
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
</script>

<?php includeAndCache('../includes/admin_footer.php'); ?>
</body>
</html>