<?php
include('../middleware/checkSession.php');
include('../middleware/cache.php');
require_once '../config/db.php';
?>


<!DOCTYPE html>
<html>
<?php includeAndCache('../includes/head.php'); ?>
<link rel="stylesheet" href="../public/css/user.index.css">

<body>
    <?php includeAndCache('../includes/sidebar.php'); ?>
    <main>
        <div class="container">

        </div>
    </main>


    <?php includeAndCache('../includes/footer.php'); ?>
</body>

</html>