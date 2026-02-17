<?php
include_once('../middleware/checkSession.php');
include_once('../middleware/cache.php');   
include(__DIR__ . "/../config/db.php");

// Set default values for pagination
$perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// Get all statistics in one optimized query
$statsQuery = $conn->query("
    SELECT 
    SUM(CASE WHEN DATE(created_at) >= CURDATE() - INTERVAL 7 DAY THEN 1 ELSE 0 END) as week_count,
    SUM(CASE WHEN MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) THEN 1 ELSE 0 END) as month_count,
    SUM(CASE WHEN YEAR(created_at) = YEAR(CURDATE()) THEN 1 ELSE 0 END) as year_count,
    COUNT(*) as all_time_count
    FROM applicants
");

if (!$statsQuery) {
    error_log("Stats query failed: " . $conn->error);
    $totalApplicantsWeek = 0;
    $totalApplicantsMonth = 0;
    $totalApplicantsYear = 0;
    $totalApplicantsAllTime = 0;
} else {
    $stats = $statsQuery->fetch_assoc();
    $totalApplicantsWeek = $stats['week_count'] ?? 0;
    $totalApplicantsMonth = $stats['month_count'] ?? 0;
    $totalApplicantsYear = $stats['year_count'] ?? 0;
    $totalApplicantsAllTime = $stats['all_time_count'] ?? 0;
}


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

// Improved date filter with validation
if (!empty($date_from) && !empty($date_to)) {
    // Validate date format (simple check)
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_from) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_to)) {
        $date_from = $conn->real_escape_string($date_from);
        $date_to = $conn->real_escape_string($date_to);
        $where .= " AND created_at BETWEEN '$date_from 00:00:00' AND '$date_to 23:59:59'";
    }
}

// Query to get the applicants with LIMIT and OFFSET for pagination
$query = $conn->query("SELECT id, course_first, course_second, photo, last_name, first_name, middle_name, contact, email, created_at 
FROM applicants $where 
ORDER BY created_at DESC 
LIMIT $perPage OFFSET $offset");

if (!$query) {
    error_log("Applicants query failed: " . $conn->error);
    $applicants = [];
} else {
    $applicants = $query->fetch_all(MYSQLI_ASSOC);
}

// Get the total number of applicants for pagination calculation
$totalApplicantsQuery = $conn->query("SELECT COUNT(*) as total FROM applicants $where");
if (!$totalApplicantsQuery) {
    error_log("Total count query failed: " . $conn->error);
    $totalApplicants = 0;
} else {
    $totalApplicantsRow = $totalApplicantsQuery->fetch_assoc();
    $totalApplicants = $totalApplicantsRow['total'] ?? 0;
}

function renderApplicantsTable($applicants, $uploadBaseUrl) {
?>
    <table class="applicants-table">
        <thead>
            <tr>
                <th style="width: 5%;">ID</th>
                <th style="width: 8%;">Photo</th>
                <th style="width: 15%;">Full Name</th>
                <th style="width: 8%;">Course 1</th>
                <th style="width: 8%;">Course 2</th>
                <th style="width: 8%;">Contact</th>
                <th style="width: 20%;">Email</th>
                <th style="width: 8%;">Applied Date</th>
                <th style="width: 15%;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($applicants)) : ?>
                <?php foreach ($applicants as $row) : ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['id']) ?></strong></td>
                        <td class="photo-cell">
                            <?php if (!empty($row['photo'])) : ?>
                                <img src="<?= $uploadBaseUrl . rawurlencode($row['photo']) ?>" 
                                     alt="Applicant Photo" 
                                     class="applicant-photo"
                                     onerror="this.onerror=null; this.src='../public/images/default-avatar.jpg';">
                            <?php else : ?>
                                <span class="no-photo">No Photo</span>
                            <?php endif; ?>
                        </td>
                        <td class="full-name">
                            <?= htmlspecialchars($row['last_name']) ?>, 
                            <?= htmlspecialchars($row['first_name']) ?> 
                            <?= !empty($row['middle_name']) ? htmlspecialchars($row['middle_name']) : '' ?>
                        </td>
                        <td>
                            <span class="course-badge"><?= htmlspecialchars($row['course_first']) ?></span>
                        </td>
                        <td>
                            <?= !empty($row['course_second']) ? '<span class="course-badge">' . htmlspecialchars($row['course_second']) . '</span>' : '-' ?>
                        </td>
                        <td><?= htmlspecialchars($row['contact']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td class="applied-date">
                            <?= $row['created_at'] ? date('M d, Y', strtotime($row['created_at'])) : 'N/A' ?>
                        </td>
                        <td>
                            <div class="button-container">
                                <form action="view.php" method="GET" class="button-form">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>" />
                                    <button type="submit" class="btn-icon" title="View">
                                        <img src="../public/images/icons/view.svg" alt="View" />
                                    </button>
                                </form>
                                <form action="update.php" method="GET" class="button-form">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>" />
                                    <button type="submit" class="btn-icon" title="Update">
                                        <img src="../public/images/icons/update.svg" alt="Update" />
                                    </button>
                                </form>
                                <form action="delete.php" method="GET" class="button-form" onsubmit="return confirm('Are you sure you want to delete this applicant?');">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>" />
                                    <button type="submit" class="btn-icon" title="Delete">
                                        <img src="../public/images/icons/delete.svg" alt="Delete" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="9" class="empty-state">No applicants found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php
}

function renderPagination($totalApplicants, $perPage, $currentPage, $search, $date_from, $date_to) {
    $totalPages = $perPage > 0 ? ceil($totalApplicants / $perPage) : 0;
    if ($totalPages == 0) $totalPages = 1;
    
    // Preserve search and date filters in pagination links
    $queryParams = $_GET;
    unset($queryParams['page']);
    $queryString = http_build_query($queryParams);
?>
<div class="pagination-container">
    <form method="GET" class="per-page-form">
        <label for="per_page">Items per page:</label>
        <select name="per_page" id="per_page" onchange="this.form.submit()">
            <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10</option>
            <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
            <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100</option>
            <option value="500" <?= $perPage == 500 ? 'selected' : '' ?>>500</option>
            <option value="1000" <?= $perPage == 1000 ? 'selected' : '' ?>>1000</option>
        </select>
        <?php if (!empty($search)): ?>
            <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
        <?php endif; ?>
        <?php if (!empty($date_from)): ?>
            <input type="hidden" name="date_from" value="<?= htmlspecialchars($date_from) ?>">
        <?php endif; ?>
        <?php if (!empty($date_to)): ?>
            <input type="hidden" name="date_to" value="<?= htmlspecialchars($date_to) ?>">
        <?php endif; ?>
    </form>
    
    <div class="pagination-links">
        <?php if ($currentPage > 1) : ?>
            <a href="?page=1&per_page=<?= $perPage ?><?= !empty($queryString) ? '&' . $queryString : '' ?>" class="pagination-link">First</a>
            <a href="?page=<?= $currentPage - 1 ?>&per_page=<?= $perPage ?><?= !empty($queryString) ? '&' . $queryString : '' ?>" class="pagination-link">Previous</a>
        <?php else: ?>
            <span class="pagination-link disabled">First</span>
            <span class="pagination-link disabled">Previous</span>
        <?php endif; ?>
        
        <span class="current-page"><?= $currentPage ?></span>
        <span class="page-info">of <?= $totalPages ?></span>
        
        <?php if ($currentPage < $totalPages) : ?>
            <a href="?page=<?= $currentPage + 1 ?>&per_page=<?= $perPage ?><?= !empty($queryString) ? '&' . $queryString : '' ?>" class="pagination-link">Next</a>
            <a href="?page=<?= $totalPages ?>&per_page=<?= $perPage ?><?= !empty($queryString) ? '&' . $queryString : '' ?>" class="pagination-link">Last</a>
        <?php else: ?>
            <span class="pagination-link disabled">Next</span>
            <span class="pagination-link disabled">Last</span>
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
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Applicants List</title>
    <?php includeAndCache('../includes/admin_head.php'); ?>
    <link rel="stylesheet" href="../public/css/admin_index.css">
</head>
<body>
    <?php includeAndCache('../includes/admin_sidebar.php'); ?>

    <main>
        <div class="container">
            <!-- Dashboard Summary -->
            <div class="dashboard-summary">
                <div class="summary-cards">
                    <div class="summary-card">
                        <h3>This Week</h3>
                        <div class="card-body">
                            <div class="card-icon">
                                <img src="../public/images/icons/week.svg" alt="Week Icon" />
                            </div>
                            <p class="card-count"><?= htmlspecialchars($totalApplicantsWeek); ?></p>
                        </div>
                    </div>

                    <div class="summary-card">
                        <h3>This Month</h3>
                        <div class="card-body">
                            <div class="card-icon">
                                <img src="../public/images/icons/month.svg" alt="Month Icon" />
                            </div>
                            <p class="card-count"><?= htmlspecialchars($totalApplicantsMonth); ?></p>
                        </div>
                    </div>

                    <div class="summary-card">
                        <h3>This Year</h3>
                        <div class="card-body">
                            <div class="card-icon">
                                <img src="../public/images/icons/year.svg" alt="Year Icon" />
                            </div>
                            <p class="card-count"><?= htmlspecialchars($totalApplicantsYear); ?></p>
                        </div>
                    </div>

                    <div class="summary-card">
                        <h3>All Time</h3>
                        <div class="card-body">
                            <div class="card-icon">
                                <img src="../public/images/icons/total.svg" alt="Total Icon" />
                            </div>
                            <p class="card-count"><?= htmlspecialchars($totalApplicantsAllTime); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FILTERS -->
            <div class="filters">
                <input type="text" id="searchInput" placeholder="Search by name, email, or contact..." value="<?= htmlspecialchars($search) ?>">
                <input type="date" id="dateFrom" value="<?= htmlspecialchars($date_from) ?>">
                <input type="date" id="dateTo" value="<?= htmlspecialchars($date_to) ?>">
                <button id="clearFilters" class="clear-btn">Clear</button>
            </div>

            <!-- Applicants Table -->
            <div id="applicantsTable">
                <?php renderApplicantsTable($applicants, $uploadBaseUrl); ?>
            </div>

            <!-- Pagination -->
            <?php renderPagination($totalApplicants, $perPage, $page, $search, $date_from, $date_to); ?>
        </div>
    </main>

    <script src="../public/js/admin_index.js"></script>
</body>
</html>