<?php
// admin/credits_crud.php
include('../middleware/checkSession.php');
include('../middleware/cache.php');
include(__DIR__ . "/../config/db.php");
?>


<!DOCTYPE html>
<html lang="en">
<?php includeAndCache('../includes/admin_head.php'); ?>


<body>
    <?php includeAndCache('../includes/admin_sidebar.php'); ?>

    <main>
        <div class="container">



            <style>
                .toc-container {
                    background: var(--card-bg);
                    border: 1px solid var(--line-clr);
                    border-radius: 12px;
                    padding: 20px 25px;
                    margin: 30px auto;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
                    transition: var(--transition);
                }

                .toc-container:hover {
                    background: var(--hover-clr);
                    transform: translateY(-2px);
                }

                .toc {
                    font-family: Poppins, sans-serif;
                    color: var(--text-clr);
                    line-height: 1.8;
                }

                .toc h2 {
                    color: var(--accent-clr);
                    text-align: center;
                }

                .toc .category-btn,
                .toc .type-btn,
                .toc .sub-btn {
                    display: block;
                    width: 100%;
                    text-align: left;
                    background: none;
                    border: none;
                    outline: none;
                    cursor: pointer;
                    font-weight: bold;
                    transition: var(--transition);
                    margin-top: 10px;
                    padding: 5px 0;
                }

                .toc .category-btn {
                    color: var(--success-clr);
                }

                .toc .category-btn:hover {
                    color: var(--accent-clr);
                }

                .toc .type-btn {
                    color: var(--accent-clr);
                    font-weight: 500;
                    margin-left: 20px;
                }

                .toc .type-btn:hover {
                    color: var(--success-clr);
                }

                .toc .sub-btn {
                    font-weight: 400;
                    margin-left: 40px;
                    color: var(--secondary-text-clr);
                    font-style: italic;
                }

                .toc .sub-btn:hover {
                    color: var(--success-clr);
                }

                .toc .category-content,
                .toc .type-content,
                .toc .sub-content {
                    display: none;
                    padding-left: 15px;
                    border-left: 2px solid var(--line-clr);
                }

                .toc .sub-sub-type {
                    margin-left: 60px;
                    color: var(--text-clr);
                    font-size: 0.9em;
                }
            </style>

            <div class="toc-container">
                <div class="toc">
                    <h2>List of Contents</h2>
                    <?php
                    $categories = [
                        'verbal' => 'Verbal Ability',
                        'numerical' => 'Numerical Ability',
                        'analytical' => 'Analytical Ability',
                        'general' => 'General Ability'
                    ];

                    foreach ($categories as $table => $categoryName) {
                        $sql = "SELECT DISTINCT category, type, sub_type, sub_sub_type
                    FROM $table
                    ORDER BY category, type, sub_type, sub_sub_type";
                        $result = $mysqli->query($sql);

                        if ($result && $result->num_rows > 0) {
                            echo "<button class='category-btn'>{$categoryName} ▼</button>";
                            echo "<div class='category-content'>";

                            // Build hierarchical array
                            $toc = [];
                            while ($row = $result->fetch_assoc()) {
                                $cat = $row['category'] ?: 'Miscellaneous';
                                $type = $row['type'] ?: 'Miscellaneous';
                                $sub = $row['sub_type'] ?: '';
                                $sub_sub = $row['sub_sub_type'] ?: '';

                                if (!isset($toc[$cat][$type][$sub])) {
                                    $toc[$cat][$type][$sub] = [];
                                }
                                if (!empty($sub_sub)) {
                                    $toc[$cat][$type][$sub][] = $sub_sub;
                                }
                            }

                            // Render categories, types, sub-types, sub-sub-types
                            foreach ($toc as $cat => $types) {
                                echo "<div class='category'>{$cat}</div>";
                                foreach ($types as $type_name => $subs) {
                                    echo "<button class='type-btn'>{$type_name} ▼</button>";
                                    echo "<div class='type-content'>";
                                    foreach ($subs as $sub => $subsubs) {
                                        if (!empty($sub)) {
                                            echo "<button class='sub-btn'>{$sub} ▼</button>";
                                            echo "<div class='sub-content'>";
                                            foreach ($subsubs as $subsub) {
                                                echo "<div class='sub-sub-type'>• {$subsub}</div>";
                                            }
                                            echo "</div>"; // close sub-content
                                        }
                                    }
                                    echo "</div>"; // close type-content
                                }
                            }

                            echo "</div>"; // close category-content
                        } else {
                            echo "<p>No data found for $categoryName.</p>";
                        }
                    }
                    ?>
                </div>
            </div>

            <script>
                // Toggle category
                document.querySelectorAll('.category-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const content = btn.nextElementSibling;
                        if (content.style.display === "block") {
                            content.style.display = "none";
                            btn.textContent = btn.textContent.replace('▲', '▼');
                        } else {
                            content.style.display = "block";
                            btn.textContent = btn.textContent.replace('▼', '▲');
                        }
                    });
                });

                // Toggle type
                document.querySelectorAll('.type-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const content = btn.nextElementSibling;
                        if (content.style.display === "block") {
                            content.style.display = "none";
                            btn.textContent = btn.textContent.replace('▲', '▼');
                        } else {
                            content.style.display = "block";
                            btn.textContent = btn.textContent.replace('▼', '▲');
                        }
                    });
                });

                // Toggle sub-type
                document.querySelectorAll('.sub-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const content = btn.nextElementSibling;
                        if (content.style.display === "block") {
                            content.style.display = "none";
                            btn.textContent = btn.textContent.replace('▲', '▼');
                        } else {
                            content.style.display = "block";
                            btn.textContent = btn.textContent.replace('▼', '▲');
                        }
                    });
                });
            </script>







        </div>
    </main>



    <?php includeAndCache('../includes/admin_footer.php'); ?>
</body>

</html>