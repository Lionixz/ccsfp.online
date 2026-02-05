<!-- C:\xampp\htdocs\x\users\index.php -->
<?php
include('../middleware/checkSession.php');
include('../middleware/cache.php');
?>

<!DOCTYPE html>
<html>
<?php includeAndCache('../includes/head.php'); ?>

<style>
    .user-header {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 20px;
        background: var(--card-bg);
        border: 1px solid var(--line-clr);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        transition: var(--transition);
        margin-bottom: 20px;
    }

    .user-header:hover {
        background: var(--hover-clr);
        transform: translateY(-2px);
    }

    .user-avatar {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        border: 3px solid var(--accent-clr);
        object-fit: cover;
        flex-shrink: 0;
        transition: var(--transition);
    }

    .user-avatar:hover {
        transform: scale(1.05);
        box-shadow: 0 0 12px var(--accent-clr);
    }

    .user-info {
        flex: 1;
        color: var(--text-clr);
    }

    .user-name {
        font-size: 1.6rem;
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--accent-clr);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .user-header {
            flex-direction: column;
            text-align: center;
            gap: 15px;
        }

        .user-avatar {
            width: 80px;
            height: 80px;
        }

        .user-info {
            text-align: center;
        }

        .user-name {
            font-size: 1.3rem;
        }
    }
</style>

<body>
    <?php includeAndCache('../includes/sidebar.php'); ?>
    <main>
        <div class="container">
            <div class="user-header">
                <img src="<?= htmlspecialchars($_SESSION['user_picture']) ?>" alt="User Avatar" class="user-avatar">
                <div class="user-info">
                    <h1 class="user-name"><?= htmlspecialchars($_SESSION['user_name']) ?></h1>
                    <dl class="user-details">
                        <div class="user-detail">
                            <dt>Gmail: <?= htmlspecialchars($_SESSION['user_email']) ?></dt>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </main>
    <?php includeAndCache('../includes/footer.php'); ?>