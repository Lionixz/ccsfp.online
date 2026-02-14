<?php
include_once('../middleware/checkSession.php');
include_once('../middleware/cache.php');   
include(__DIR__ . "/../config/db.php");


// Set default values for pagination
$perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10; // Default 10 per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Default first page
$offset = ($page - 1) * $perPage;

// Query to get total applicants for the week
$weekQuery = $conn->query("SELECT COUNT(*) as total FROM applicants WHERE DATE(created_at) >= CURDATE() - INTERVAL 7 DAY");
$weekRow = $weekQuery->fetch_assoc();
$totalApplicantsWeek = $weekRow['total'] ?? 0;

// Query to get total applicants for the month
$monthQuery = $conn->query("SELECT COUNT(*) as total FROM applicants WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
$monthRow = $monthQuery->fetch_assoc();
$totalApplicantsMonth = $monthRow['total'] ?? 0;

// Query to get total applicants for the year
$yearQuery = $conn->query("SELECT COUNT(*) as total FROM applicants WHERE YEAR(created_at) = YEAR(CURDATE())");
$yearRow = $yearQuery->fetch_assoc();
$totalApplicantsYear = $yearRow['total'] ?? 0;

// Query to get total applicants for all time
$allTimeQuery = $conn->query("SELECT COUNT(*) as total FROM applicants");
$allTimeRow = $allTimeQuery->fetch_assoc();
$totalApplicantsAllTime = $allTimeRow['total'] ?? 0;

$search = $_GET['search'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$isAjax = isset($_GET['ajax']) && $_GET['ajax'] == 1;
$uploadBaseUrl = '../public/images/uploads/';
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

// Query to get the applicants with LIMIT and OFFSET for pagination
$query = $conn->query("SELECT id, course_first, course_second, photo, last_name, first_name, middle_name, contact, email, created_at FROM applicants $where ORDER BY id DESC LIMIT $perPage OFFSET $offset");
$applicants = $query->fetch_all(MYSQLI_ASSOC);

// Get the total number of applicants for pagination calculation
$totalApplicantsQuery = $conn->query("SELECT COUNT(*) as total FROM applicants $where");
$totalApplicantsRow = $totalApplicantsQuery->fetch_assoc();
$totalApplicants = $totalApplicantsRow['total'];

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
                    <tr style="text-align: center;">
                        <td><?= $row['id'] ?></td>
                        <td>
                            <?php if (!empty($row['photo'])) : ?>
                                <img src="<?= $uploadBaseUrl . rawurlencode($row['photo']) ?>" alt="Applicant Photo" width="40" height="40" style="object-fit:cover; border-radius:50%;">
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
                        <td class="button-container">
                            <form action="view.php" method="GET" class="button-form" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>" />
                                <button type="submit" class="btn btn-view" style="background: none; border: none; cursor: pointer;">
                                    <img src="../public/images/icons/view.svg" alt="View" width="24" height="24" />
                                </button>
                            </form>
                            <form action="update.php" method="GET" class="button-form" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>" />
                                <button type="submit" class="btn btn-update" style="background: none; border: none; cursor: pointer;">
                                    <img src="../public/images/icons/update.svg" alt="Update" width="24" height="24" />
                                </button>
                            </form>
                            <form action="delete.php" method="GET" class="button-form" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this applicant?');">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>" />
                                <button type="submit" class="btn btn-delete" style="background: none; border: none; cursor: pointer;">
                                    <img src="../public/images/icons/delete.svg" alt="Delete" width="24" height="24" />
                                </button>
                            </form>
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

function renderPagination($totalApplicants, $perPage, $currentPage) {
    $totalPages = ceil($totalApplicants / $perPage);
    ?>


<div class="pagination" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
    <form method="GET" style="display: flex; align-items: center; margin-right: 20px;">
        <label for="per_page" style="margin-right: 10px;">Items per page:</label>
        <select name="per_page" onchange="this.form.submit()" style="margin-right: 20px;">
            <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10</option>
            <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
            <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100</option>
            <option value="500" <?= $perPage == 500 ? 'selected' : '' ?>>500</option>
            <option value="1000" <?= $perPage == 1000 ? 'selected' : '' ?>>1000</option>
        </select>
    </form>
    <div style="display: flex; align-items: center; justify-content: center; flex-grow: 1;">
        <?php if ($currentPage > 1) : ?>
            <a href="?page=1&per_page=<?= $perPage ?>" style="margin-right: 10px;">First</a>
            <a href="?page=<?= $currentPage - 1 ?>&per_page=<?= $perPage ?>" style="margin-right: 10px;">Previous</a>
        <?php endif; ?>
        <span style="margin-right: 10px;">Page <?= $currentPage ?> of <?= $totalPages ?></span>
        <?php if ($currentPage < $totalPages) : ?>
            <a href="?page=<?= $currentPage + 1 ?>&per_page=<?= $perPage ?>" style="margin-right: 10px;">Next</a>
            <a href="?page=<?= $totalPages ?>&per_page=<?= $perPage ?>">Last</a>
        <?php endif; ?>
    </div>
</div>




    <?php
}

if ($isAjax) {
    renderApplicantsTable($applicants, $uploadBaseUrl);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<?php includeAndCache('../includes/admin_head.php'); ?>
<link rel="stylesheet" href="../public/css/admin_index.css">

<body>
<?php includeAndCache('../includes/admin_sidebar.php'); ?>



<main>
    <div class="container">
        <!-- Dashboard Summary and Filters go here -->
  <div class="dashboard-summary">
            <div class="summary-cards">
                <!-- Total Applicants per Week -->
                <div class="summary-card">
                    <h3>Week</h3>
                    <div class="card-body">
                        <div class="card-icon">
                            <img src="../public/images/icons/week.svg" alt="Total Applicants Icon" />
                        </div>
                        <p class="card-count"><?= $totalApplicantsWeek; ?></p>
                    </div>
                </div>

                <!-- Total Applicants per Month -->
                <div class="summary-card">
                    <h3>Month</h3>
                    <div class="card-body">
                        <div class="card-icon">
                            <img src="../public/images/icons/month.svg" alt="Total Applicants Icon" />
                        </div>
                        <p class="card-count"><?= $totalApplicantsMonth; ?></p>
                    </div>
                </div>

                <!-- Total Applicants per Year -->
                <div class="summary-card">
                    <h3>Year</h3>
                    <div class="card-body">
                        <div class="card-icon">
                            <img src="../public/images/icons/year.svg" alt="Total Applicants Icon" />
                        </div>
                        <p class="card-count"><?= $totalApplicantsYear; ?></p>
                    </div>
                </div>

                <!-- Total Applicants -->
                <div class="summary-card">
                    <h3>All Time</h3>
                    <div class="card-body">
                        <div class="card-icon">
                            <img src="../public/images/icons/total.svg" alt="Total Applicants Icon" />
                        </div>
                        <p class="card-count"><?= $totalApplicantsAllTime; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTERS -->
        <div style="margin:20px 0; display:flex; gap:10px; flex-wrap:wrap;">
            <input type="text" id="searchInput" placeholder="Search name, email, contact" style="padding:5px; width:250px;" value="<?= htmlspecialchars($search) ?>">
            <input type="date" id="dateFrom" style="padding:5px;" value="<?= htmlspecialchars($date_from) ?>">
            <input type="date" id="dateTo" style="padding:5px;" value="<?= htmlspecialchars($date_to) ?>">
        </div>


        <!-- Applicants Table -->
        <div id="applicantsTable">
            <?php renderApplicantsTable($applicants, $uploadBaseUrl); ?>
        </div>

        <!-- Pagination -->
        <div>
            <?php renderPagination($totalApplicants, $perPage, $page); ?>
        </div>

    </div>
</main>

<script src="../public/js/admin_index.js"></script>
</html>







