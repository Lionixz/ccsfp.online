<?php
include('../middleware/checkSession.php');
include('../middleware/cache.php');
require_once '../config/db.php';

// -----------------------------
// 1. Get logged-in user ID from Google OAuth
// -----------------------------
$users_id = $_SESSION['user_id'] ?? null;
if (!$users_id) {
    die("User not logged in.");
}

// -----------------------------
// 2. CHECK IF USER HAS ALREADY REGISTERED
// -----------------------------
$checkStmt = $conn->prepare("SELECT id FROM applicants WHERE users_id = ?");
$checkStmt->bind_param("s", $id); // "s" = string
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    // User already registered → redirect immediately
    header("Location: view.php");
    exit;
}
$checkStmt->close();

// -----------------------------
// 3. Initialize form variables
// -----------------------------
$startYear = date("Y");
$endYear = date("Y") + 1;

// -----------------------------
// 4. HANDLE FORM SUBMISSION
// -----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Collect form data
    $course_first     = $_POST['course_first'] ?? '';
    $course_second    = $_POST['course_second'] ?? '';
    $last_name        = $_POST['last_name'] ?? '';
    $first_name       = $_POST['first_name'] ?? '';
    $middle_name      = $_POST['middle_name'] ?? '';
    $age              = $_POST['age'] ?? null;
    $gender           = $_POST['gender'] ?? '';
    $dob              = $_POST['dob'] ?? null;
    $birth_place      = $_POST['birth_place'] ?? '';
    $marital_status   = $_POST['marital_status'] ?? '';
    $contact          = $_POST['contact'] ?? '';
    $religion         = $_POST['religion'] ?? '';
    $email            = $_POST['email'] ?? '';
    $home_address     = $_POST['home_address'] ?? '';
    $relative_name    = $_POST['relative_name'] ?? '';
    $relative_address = $_POST['relative_address'] ?? '';

    // Handle photo upload
    $photo_path = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $uploadDir = '../public/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = time() . '_' . basename($_FILES['photo']['name']);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
            $photo_path = 'uploads/' . $fileName;
        } else {
            echo "<p style='color:red;'>Failed to upload photo.</p>";
        }
    }

    // Prepare insert query
    $sql = "
        INSERT INTO applicants (
            users_id, course_first, course_second, photo,
            last_name, first_name, middle_name,
            age, gender, dob, birth_place, marital_status,
            contact, religion, email, home_address,
            relative_name, relative_address
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) die("Prepare failed: " . $conn->error);

    $stmt->bind_param(
        "ssssssssssssssssss",
        $users_id,
        $course_first,
        $course_second,
        $photo_path,
        $last_name,
        $first_name,
        $middle_name,
        $age,
        $gender,
        $dob,
        $birth_place,
        $marital_status,
        $contact,
        $religion,
        $email,
        $home_address,
        $relative_name,
        $relative_address
    );

    if ($stmt->execute()) {
        // Success → redirect to view.php
        header("Location: view.php");
        exit;
    } else {
        echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<?php includeAndCache('../includes/head.php'); ?>
<link rel="stylesheet" href="../public/css/user.index.css">

<body>
    <?php includeAndCache('../includes/sidebar.php'); ?>
    <main>
        <div class="container">
            <form method="POST" enctype="multipart/form-data">
                <table>
                    <tbody>
                        <!-- Header Section -->
                        <tr>
                            <td colspan="3" rowspan="4">
                                <img src="../public/images/system/logo.png" alt="CCSFP Logo" width="120">
                            </td>
                            <td colspan="16">City College of San Fernando Pampanga</td>
                            <td colspan="5">CCSFP Admission Form 001</td>
                        </tr>
                        <tr>
                            <td colspan="17">City of San Fernando, Pampanga</td>
                            <td colspan="4" rowspan="2">Application No: 0000</td>
                        </tr>
                        <tr>
                            <td colspan="18">Email: citycollegesfp@gmail.com</td>
                        </tr>
                        <tr>
                            <td colspan="16">APPLICATION FORM FOR FIRST YEAR</td>
                            <td colspan="5" rowspan="7" class="photo-td">
                                <div class="photo-wrapper">
                                    <label for="photo" class="preview-container">
                                        <span class="overlay-text">
                                            Upload 1.5 x 1.5 Colored Picture<br>
                                            (white background with name tag)
                                        </span>
                                        <img id="preview" src="" alt="Image Preview" class="preview-img">
                                    </label>
                                    <input type="file" name="photo" id="photo" accept="image/*" onchange="previewImage(event)" class="file-input">
                                </div>
                            </td>
                        </tr>

                        <!-- Instructions -->
                        <tr>
                            <td colspan="3">INSTRUCTIONS</td>
                            <td colspan="16">A. Y. <?= $startYear ?>—<?= $endYear ?></td>
                        </tr>
                        <tr>
                            <td colspan="19">1. Fill out all required information in this admission form.</td>
                        </tr>
                        <tr>
                            <td colspan="20">2. Print all entries legibly and only fully accomplished forms (CCSFP-Admission Form 001) will be processed.</td>
                        </tr>

                        <!-- Courses -->
                        <tr>
                            <td colspan="8">COURSE APPLIED FOR:</td>
                            <td colspan="11"></td>
                        </tr>
                        <tr>
                            <td colspan="5"><label for="course_first">1st Choice:</label></td>
                            <td colspan="15">
                                <select name="course_first" id="course_first" class="form-select">
                                    <option value="">-- Select Course --</option>
                                    <option value="BSIT">Bachelor of Science in Information Technology (BSIT)</option>
                                    <option value="BSCS">Bachelor of Science in Computer Science (BSCS)</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5"><label for="course_second">2nd Choice:</label></td>
                            <td colspan="15">
                                <select name="course_second" id="course_second" class="form-select">
                                    <option value="">-- Select Course --</option>
                                    <option value="BSIT">Bachelor of Science in Information Technology (BSIT)</option>
                                    <option value="BSCS">Bachelor of Science in Computer Science (BSCS)</option>
                                </select>
                            </td>
                        </tr>

                        <!-- Personal Information -->
                        <tr>
                            <td colspan="24" style="text-align:left; font-weight:bold;">I. PERSONAL INFORMATION</td>
                        </tr>

                        <!-- Row 1: Full Name -->
                        <tr>
                            <td colspan="3">1. Last Name:</td>
                            <td colspan="5"><input type="text" name="last_name" class="form-input" style="width:100%;"></td>
                            <td colspan="3">First Name:</td>
                            <td colspan="5"><input type="text" name="first_name" class="form-input" style="width:100%;"></td>
                            <td colspan="3">Middle Name:</td>
                            <td colspan="5"><input type="text" name="middle_name" class="form-input" style="width:100%;"></td>
                        </tr>

                        <!-- Row 2: Age, Gender, DOB -->
                        <tr>
                            <td colspan="2">2. Age:</td>
                            <td colspan="3"><input type="number" name="age" class="form-input" style="width:100%;"></td>
                            <td colspan="3">3. Gender:</td>
                            <td colspan="3"><input type="text" name="gender" class="form-input" style="width:100%;"></td>
                            <td colspan="4">4. Date of Birth:</td>
                            <td colspan="9"><input type="date" name="dob" class="form-input" style="width:100%;"></td>
                        </tr>

                        <!-- Row 3: Birthplace and Marital Status -->
                        <tr>
                            <td colspan="4">5. Place of Birth:</td>
                            <td colspan="12"><input type="text" name="birth_place" class="form-input" style="width:100%;"></td>
                            <td colspan="4">6. Marital Status:</td>
                            <td colspan="4">
                                <select name="marital_status" class="form-select" style="width:100%;">
                                    <option value="">-- Select --</option>
                                    <option value="Single">Single</option>
                                    <option value="Married">Married</option>
                                    <option value="Widowed">Widowed</option>
                                    <option value="Separated">Separated</option>
                                    <option value="Others">Others</option>
                                </select>
                            </td>
                        </tr>

                        <!-- Row 4: Contact, Religion, Email -->
                        <tr>
                            <td colspan="4">7. Contact Number/s:</td>
                            <td colspan="5"><input type="number" name="contact" class="form-input" style="width:100%;"></td>
                            <td colspan="2">8. Religion:</td>
                            <td colspan="3"><input type="text" name="religion" class="form-input" style="width:100%;"></td>
                            <td colspan="3">9. Email Address:</td>
                            <td colspan="7"><input type="email" name="email" class="form-input" style="width:100%;"></td>
                        </tr>

                        <!-- Row 5: Complete Home Address -->
                        <tr>
                            <td colspan="6">10. Complete Home Address:</td>
                            <td colspan="18"><input type="text" name="home_address" class="form-input" style="width:100%;"></td>
                        </tr>

                        <!-- Row 6: Relative Name -->
                        <tr>
                            <td colspan="7">11. Applicant is living with Relative:</td>
                            <td colspan="17"><input type="text" name="relative_name" class="form-input" style="width:100%;"></td>
                        </tr>

                        <!-- Row 7: Relative Address -->
                        <tr>
                            <td colspan="3">12. Address:</td>
                            <td colspan="21"><input type="text" name="relative_address" class="form-input" style="width:100%;"></td>
                        </tr>

                        <!-- Submit -->
                        <tr>
                            <td colspan="24" style="text-align:center;">
                                <button type="submit" class="btn-submit">Submit Application</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </main>


    <script src="../public/js/user.index.js"></script>
    <?php includeAndCache('../includes/footer.php'); ?>
</body>

</html>