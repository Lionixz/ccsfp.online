<?php
    include('../middleware/checkSession.php');
    include('../middleware/cache.php');
    require_once '../config/db.php';

    $startYear = date("Y");
    $endYear   = date("Y") + 1;

    $google_id = $_SESSION['user_id'] ?? null;
    if (!$google_id) {
        die("User not logged in.");
    }

    $checkStmt = $conn->prepare("SELECT id FROM applicants WHERE google_id = ?");
    $checkStmt->bind_param("s", $google_id);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        header("Location: view.php");
        exit;
    }
    $checkStmt->close();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        /* ---------------- I. PERSONAL INFO ---------------- */
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

        /* ---------------- II. SCHOLASTIC ---------------- */
        $college          = $_POST['college'] ?? '';
        $college_course   = $_POST['college_course'] ?? '';
        $college_address  = $_POST['college_address'] ?? '';
        $college_year     = $_POST['college_year'] ?? '';
        $shs              = $_POST['shs'] ?? '';
        $shs_year         = $_POST['shs_year'] ?? null;
        $shs_address      = $_POST['shs_address'] ?? '';
        $shs_lrn          = $_POST['shs_lrn'] ?? '';
        $shs_awards       = $_POST['shs_awards'] ?? '';
        $jhs              = $_POST['jhs'] ?? '';
        $jhs_year         = $_POST['jhs_year'] ?? null;
        $jhs_address      = $_POST['jhs_address'] ?? '';
        $jhs_awards       = $_POST['jhs_awards'] ?? '';
        $primary_school   = $_POST['primary_school'] ?? '';
        $primary_year     = $_POST['primary_year'] ?? null;
        $skills           = $_POST['skills'] ?? '';
        $sports           = $_POST['sports'] ?? '';

        /* ---------------- III. FAMILY ---------------- */
        $father_name       = $_POST['father_name'] ?? '';
        $father_occupation = $_POST['father_occupation'] ?? '';
        $father_employer   = $_POST['father_employer'] ?? '';
        $mother_name       = $_POST['mother_name'] ?? '';
        $mother_occupation = $_POST['mother_occupation'] ?? '';
        $mother_employer   = $_POST['mother_employer'] ?? '';
        $guardian_name       = $_POST['guardian_name'] ?? '';
        $guardian_occupation = $_POST['guardian_occupation'] ?? '';
        $guardian_employer   = $_POST['guardian_employer'] ?? '';
        $guardian_address    = $_POST['guardian_address'] ?? '';
        $guardian_contact    = $_POST['guardian_contact'] ?? '';

        /* ---------------- OTHER ---------------- */
        $family_income = $_POST['family_income'] ?? '';
        $how_heard     = ($_POST['how_heard'] === 'other')
            ? ($_POST['how_heard_other'] ?? '')
            : ($_POST['how_heard'] ?? '');

        $sibling_names       = json_encode($_POST['sibling_name'] ?? []);
        $sibling_educations  = json_encode($_POST['sibling_education'] ?? []);
        $sibling_occupations = json_encode($_POST['sibling_occupation'] ?? []);

        /* ---------------- PHOTO ---------------- */
        $photo_path = null;
        if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === 0) {
            $dir = '../public/images/uploads/';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $file = time() . '_' . basename($_FILES['photo']['name']);
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $dir . $file)) {
                $photo_path = '' . $file;
            }
        }

        /* ---------------- INSERT ---------------- */
        $sql = "
            INSERT INTO applicants (
                google_id, course_first, course_second, photo,
                last_name, first_name, middle_name,
                age, gender, dob, birth_place, marital_status,
                contact, religion, email, home_address,
                relative_name, relative_address,
                college, college_course, college_address, college_year,
                shs, shs_year, shs_address, shs_lrn, shs_awards,
                jhs, jhs_year, jhs_address, jhs_awards,
                primary_school, primary_year, skills, sports,
                father_name, father_occupation, father_employer,
                mother_name, mother_occupation, mother_employer,
                guardian_name, guardian_occupation, guardian_employer, guardian_address, guardian_contact,
                family_income, how_heard,
                sibling_names, sibling_educations, sibling_occupations
            ) VALUES (" . rtrim(str_repeat('?,', 51), ',') . ")
        ";

        $stmt = $conn->prepare($sql);
        if (!$stmt) die("Prepare failed: " . $conn->error);

        $stmt->bind_param(
            str_repeat('s', 51),
            $google_id, $course_first, $course_second, $photo_path,
            $last_name, $first_name, $middle_name,
            $age, $gender, $dob, $birth_place, $marital_status,
            $contact, $religion, $email, $home_address,
            $relative_name, $relative_address,
            $college, $college_course, $college_address, $college_year,
            $shs, $shs_year, $shs_address, $shs_lrn, $shs_awards,
            $jhs, $jhs_year, $jhs_address, $jhs_awards,
            $primary_school, $primary_year, $skills, $sports,
            $father_name, $father_occupation, $father_employer,
            $mother_name, $mother_occupation, $mother_employer,
            $guardian_name, $guardian_occupation, $guardian_employer, $guardian_address, $guardian_contact,
            $family_income, $how_heard,
            $sibling_names, $sibling_educations, $sibling_occupations
        );

        if ($stmt->execute()) {
            header("Location: view.php");
            exit;
        } else {
            echo "<p style='color:red;'>Insert Error: {$stmt->error}</p>";
        }

        $stmt->close();
    }

$courses = [
    ['code' => 'BSBA-MM', 'name' => 'Bachelor of Science in Business Administration major in Marketing Management'],
    ['code' => 'BSBA-FM', 'name' => 'Bachelor of Science in Business Administration major in Financial Management'],
    ['code' => 'BSBA-OM', 'name' => 'Bachelor of Science in Business Administration major in Operations Management'],
    ['code' => 'BSBA-HRM', 'name' => 'Bachelor of Science in Business Administration major in Human Resource Management'],
    ['code' => 'BSENTREP', 'name' => 'Bachelor of Science in Entrepreneurship (BS Entrepreneurship)'],
    ['code' => 'BSED-MATH', 'name' => 'Bachelor of Secondary Education major in Mathematics'],
    ['code' => 'BSED-ENG', 'name' => 'Bachelor of Secondary Education major in English'],
    ['code' => 'BSED-SCI', 'name' => 'Bachelor of Secondary Education major in Science'],
    ['code' => 'BEED', 'name' => 'Bachelor of Elementary Education (BEED)'],
    ['code' => 'BECTE', 'name' => 'Bachelor of Early Childhood Education (BECE)'],
    ['code' => 'BSIT', 'name' => 'Bachelor of Science in Information Technology (BSIT)'],
    ['code' => 'BSAIS', 'name' => 'Bachelor of Science in Accounting Information System (BSAIS)']
];


$incomeRanges = [
    'below 10k' => 'Below ₱10,000',
    '10k 20k'   => '₱10,000 – ₱20,000',
    '20k 30k'   => '₱20,000 – ₱30,000',
    '30k above' => '₱30,000 and above'
];

$howHeardOptions = [
    'social media' => 'Social Media',
    'friend'       => 'Friend / Family',
    'school visit' => 'School Visit',
    'others'       => 'Others'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php includeAndCache('../includes/head.php'); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <link rel="stylesheet" href="../public/css/user_index.css">
 
</head>

<body>
    <?php includeAndCache('../includes/sidebar.php'); ?>
    <main>
        <div class="container">
            <div>
                <h1>Registration Form</h1>
                <div class="btn-container">
                    <button type="submit" form="insertForm" class="btn btn-update">Save Application </button>
                    <button type="button" class="pdf-btn" onclick="window.location.href='index.php'">Cancel</button>
                </div>
            </div>

            <form id="insertForm" method="POST" action="" enctype="multipart/form-data">
                <table id="applicant-table">
                    <tbody>
                        <!-- Header Row -->
                        <tr>
                            <td colspan="5" rowspan="4" class="logo-cell">
                                <img src="../public/images/system/logo.png" alt="CCSFP Logo" width="120" class="mobile-logo">
                            </td>
                            <td colspan="15" class="school-name-header">City College of San Fernando Pampanga</td>
                            <td colspan="4" class="form-number">CCSFP Admission Form 001</td>
                        </tr>
                        <tr>
                            <td colspan="15" class="school-address">City of San Fernando, Pampanga</td>
                            <td colspan="4" rowspan="2" class="app-no-cell">Application No: <br> (New)</td>
                        </tr>
                        <tr>
                            <td colspan="15" class="school-email">Email: citycollegesfp@gmail.com</td>
                        </tr>
                        <tr>
                            <td colspan="15" class="form-title">APPLICATION FORM FOR FIRST YEAR</td>                
                            <td colspan="4" rowspan="4" class="photo-td">
                                <div class="photo-wrapper">
                                    <img id="preview" src="" alt="Preview" style="display:none;">
                                    <span class="overlay-text">No Photo</span>
                                </div>
                                <input type="file" name="photo" id="photoInput" accept="image/jpeg,image/png" class="mobile-file-input">
                            </td>
                        </tr>

                        <!-- Courses -->
                        <tr>
                            <td colspan="20" class="section-header">COURSE APPLIED FOR:</td>
                        </tr>
                        <tr class="course-row">
                            <td colspan="5" class="label-cell">1st Choice:</td>
                            <td colspan="15" class="input-cell">
                                <select name="course_first" id="course_first" class="form-select">
                                    <option value="">Select First Choice</option>
                                    <?php foreach ($courses as $course): ?>
                                        <option value="<?= htmlspecialchars($course['code']) ?>">
                                            <?= htmlspecialchars($course['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr class="course-row">
                            <td colspan="5" class="label-cell">2nd Choice:</td>
                            <td colspan="15" class="input-cell">
                                <select name="course_second" id="course_second" class="form-select">
                                    <option value="">Select Second Choice</option>
                                    <?php foreach ($courses as $course): ?>
                                        <option value="<?= htmlspecialchars($course['code']) ?>">
                                            <?= htmlspecialchars($course['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>

                        <!-- I. PERSONAL INFO -->
                        <tr><td colspan="24" class="section-header">I. PERSONAL INFORMATION</td></tr>
                        <tr class="name-row">
                            <td colspan="3" class="label-cell">Last Name:</td>
                            <td colspan="5" class="input-cell"><input type="text" name="last_name" class="form-input" value=""></td>
                            <td colspan="3" class="label-cell">First Name:</td>
                            <td colspan="5" class="input-cell"><input type="text" name="first_name" class="form-input" value=""></td>
                            <td colspan="3" class="label-cell">Middle Name:</td>
                            <td colspan="5" class="input-cell"><input type="text" name="middle_name" class="form-input" value=""></td>
                        </tr>
                        <tr class="age-gender-row">
                            <td colspan="3" class="label-cell">Age:</td>
                            <td colspan="5" class="input-cell"><input type="number" name="age" class="form-input" value=""></td>
                            <td colspan="3" class="label-cell">Gender:</td>
                            <td colspan="5" class="input-cell">
                                <select name="gender" class="form-select">
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </td>
                            <td colspan="3" class="label-cell">Date of Birth:</td>
                            <td colspan="5" class="input-cell"><input type="date" name="dob" class="form-input" value=""></td>
                        </tr>
                        <tr class="birth-marital-row">
                            <td colspan="4" class="label-cell">Place of Birth:</td>
                            <td colspan="12" class="input-cell"><input type="text" name="birth_place" class="form-input" value=""></td>
                            <td colspan="4" class="label-cell">Marital Status:</td>
                            <td colspan="4" class="input-cell">
                                <select name="marital_status" class="form-select">
                                    <option value="">Select</option>
                                    <option value="Single">Single</option>
                                    <option value="Married">Married</option>
                                    <option value="Divorced">Divorced</option>
                                    <option value="Widowed">Widowed</option>
                                </select>
                            </td>
                        </tr>
                        <tr class="contact-row">
                            <td colspan="4" class="label-cell">Contact Number/s:</td>
                            <td colspan="5" class="input-cell"><input type="text" name="contact" class="form-input" value=""></td>
                            <td colspan="2" class="label-cell">Religion:</td>
                            <td colspan="3" class="input-cell"><input type="text" name="religion" class="form-input" value=""></td>
                            <td colspan="3" class="label-cell">Email Address:</td>
                            <td colspan="7" class="input-cell"><input type="email" name="email" class="form-input" value=""></td>
                        </tr>
                        <tr class="address-row">
                            <td colspan="6" class="label-cell">Complete Home Address:</td>
                            <td colspan="18" class="input-cell"><input type="text" name="home_address" class="form-input" value=""></td>
                        </tr>
                        <tr class="relative-row">
                            <td colspan="6" class="label-cell">Applicant is living with Relative:</td>
                            <td colspan="18" class="input-cell"><input type="text" name="relative_name" class="form-input" value=""></td>
                        </tr>
                        <tr class="relative-address-row">
                            <td colspan="6" class="label-cell">Address:</td>
                            <td colspan="18" class="input-cell"><input type="text" name="relative_address" class="form-input" value=""></td>
                        </tr>

                        <!-- II. SCHOLASTIC BACKGROUND -->
                        <tr><td colspan="24" class="section-header">II. SCHOLASTIC BACKGROUND</td></tr>
                        <tr class="college-row">
                            <td colspan="5" class="label-cell">College (Undergraduate):</td>
                            <td colspan="9" class="input-cell"><input type="text" name="college" class="form-input" value=""></td>
                            <td colspan="2" class="label-cell">Course:</td>
                            <td colspan="8" class="input-cell"><input type="text" name="college_course" class="form-input" value=""></td>
                        </tr>
                        <tr class="college-address-row">
                            <td colspan="5" class="label-cell">School Address:</td>
                            <td colspan="9" class="input-cell"><input type="text" name="college_address" class="form-input" value=""></td>
                            <td colspan="6" class="label-cell">Last Sem/Year Attended:</td>
                            <td colspan="4" class="input-cell"><input type="text" name="college_year" class="form-input" value=""></td>
                        </tr>
                        <tr class="shs-row">
                            <td colspan="6" class="label-cell">Senior High School (Grade 12):</td>
                            <td colspan="9" class="input-cell"><input type="text" name="shs" class="form-input" value=""></td>
                            <td colspan="4" class="label-cell">Year Graduated:</td>
                            <td colspan="5" class="input-cell"><input type="number" name="shs_year" class="form-input" value=""></td>
                        </tr>
                        <tr class="shs-address-row">
                            <td colspan="4" class="label-cell">School Address:</td>
                            <td colspan="13" class="input-cell"><input type="text" name="shs_address" class="form-input" value=""></td>
                            <td colspan="2" class="label-cell">LRN #:</td>
                            <td colspan="5" class="input-cell"><input type="text" name="shs_lrn" class="form-input" value=""></td>
                        </tr>
                        <tr class="shs-awards-row">
                            <td colspan="6" class="label-cell">Awards/Honors Received:</td>
                            <td colspan="18" class="input-cell"><input type="text" name="shs_awards" class="form-input" value=""></td>
                        </tr>
                        <tr class="jhs-row">
                            <td colspan="7" class="label-cell">Junior High School (Grade 10):</td>
                            <td colspan="9" class="input-cell"><input type="text" name="jhs" class="form-input" value=""></td>
                            <td colspan="4" class="label-cell">Year Graduated:</td>
                            <td colspan="4" class="input-cell"><input type="number" name="jhs_year" class="form-input" value=""></td>
                        </tr>
                        <tr class="jhs-address-row">
                            <td colspan="4" class="label-cell">School Address:</td>
                            <td colspan="20" class="input-cell"><input type="text" name="jhs_address" class="form-input" value=""></td>
                        </tr>
                        <tr class="jhs-awards-row">
                            <td colspan="6" class="label-cell">Awards/Honors Received:</td>
                            <td colspan="18" class="input-cell"><input type="text" name="jhs_awards" class="form-input" value=""></td>
                        </tr>
                        <tr class="primary-row">
                            <td colspan="4" class="label-cell">Primary:</td>
                            <td colspan="12" class="input-cell"><input type="text" name="primary_school" class="form-input" value=""></td>
                            <td colspan="4" class="label-cell">Year Graduated:</td>
                            <td colspan="4" class="input-cell"><input type="number" name="primary_year" class="form-input" value=""></td>
                        </tr>
                        <tr class="skills-row">
                            <td colspan="5" class="label-cell">Special Skills/Talents:</td>
                            <td colspan="19" class="input-cell"><input type="text" name="skills" class="form-input" value=""></td>
                        </tr>
                        <tr class="sports-row">
                            <td colspan="6" class="label-cell">Interest Sports/Affiliations:</td>
                            <td colspan="18" class="input-cell"><input type="text" name="sports" class="form-input" value=""></td>
                        </tr>

                        <!-- III. FAMILY BACKGROUND -->
                        <tr><td colspan="24" class="section-header">III. FAMILY BACKGROUND</td></tr>
                        <tr class="father-row">
                            <td colspan="3" class="label-cell">Father:</td>
                            <td colspan="4" class="input-cell"><input type="text" name="father_name" class="form-input" value=""></td>
                            <td colspan="3" class="label-cell">Occupation:</td>
                            <td colspan="4" class="input-cell"><input type="text" name="father_occupation" class="form-input" value=""></td>
                            <td colspan="6" class="label-cell">Employer/Address:</td>
                            <td colspan="4" class="input-cell"><input type="text" name="father_employer" class="form-input" value=""></td>
                        </tr>
                        <tr class="mother-row">
                            <td colspan="3" class="label-cell">Mother:</td>
                            <td colspan="4" class="input-cell"><input type="text" name="mother_name" class="form-input" value=""></td>
                            <td colspan="3" class="label-cell">Occupation:</td>
                            <td colspan="4" class="input-cell"><input type="text" name="mother_occupation" class="form-input" value=""></td>
                            <td colspan="6" class="label-cell">Employer/Address:</td>
                            <td colspan="4" class="input-cell"><input type="text" name="mother_employer" class="form-input" value=""></td>
                        </tr>
                        <tr class="guardian-row">
                            <td colspan="3" class="label-cell">Guardian:</td>
                            <td colspan="4" class="input-cell"><input type="text" name="guardian_name" class="form-input" value=""></td>
                            <td colspan="3" class="label-cell">Occupation:</td>
                            <td colspan="4" class="input-cell"><input type="text" name="guardian_occupation" class="form-input" value=""></td>
                            <td colspan="6" class="label-cell">Employer/Address:</td>
                            <td colspan="4" class="input-cell"><input type="text" name="guardian_employer" class="form-input" value=""></td>
                        </tr>
                        <tr class="guardian-contact-row">
                            <td colspan="5" class="label-cell">Guardian’s Address:</td>
                            <td colspan="10" class="input-cell"><input type="text" name="guardian_address" class="form-input" value=""></td>
                            <td colspan="3" class="label-cell">Contact Nos.:</td>
                            <td colspan="6" class="input-cell"><input type="text" name="guardian_contact" class="form-input" value=""></td>
                        </tr>

                        <!-- IV. MONTHLY FAMILY INCOME -->
                        <tr><td colspan="24" class="section-header">IV. MONTHLY FAMILY INCOME</td></tr>
                        <tr>
                            <td colspan="24">
                                <div class="radio-group">
                                    <?php foreach ($incomeRanges as $value => $label): ?>
                                        <label>
                                            <input type="radio" name="family_income" value="<?= $value ?>">
                                            <?= $label ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                        </tr>

                        <!-- V. HOW DID YOU KNOW -->
                        <tr><td colspan="24" class="section-header">V. How did you know about City College?</td></tr>
                        <tr>
                            <td colspan="24">
                                <div class="radio-group">
                                    <?php foreach ($howHeardOptions as $value => $label): ?>
                                        <label>
                                            <input type="radio" name="how_heard" value="<?= $value ?>">
                                            <?= $label ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                                <div class="other-input" style="margin-top:10px;">
                                    <input type="text" name="how_heard_other" id="how_heard_other_text" class="form-input" placeholder="Please specify" value="" style="width:100%;" disabled>
                                </div>
                            </td>
                        </tr>

                        <!-- SIBLINGS -->
                        <tr><td colspan="24" class="section-header">SIBLINGS</td></tr>
                        <!-- Sibling 1 (Name & Educational Attainment) -->
                        <tr class="sibling-row">
                            <td colspan="3" class="label-cell">Name:</td>
                            <td colspan="11" class="input-cell"><input type="text" name="sibling_name[]" class="form-input" value=""></td>
                            <td colspan="5" class="label-cell">Educational Attainment:</td>
                            <td colspan="5" class="input-cell"><input type="text" name="sibling_education[]" class="form-input" value=""></td>
                        </tr>
                        <tr class="sibling-row">
                            <td colspan="9" class="label-cell">Occupation/Employer/School:</td>
                            <td colspan="12" class="input-cell"><input type="text" name="sibling_occupation[]" class="form-input" value=""></td>
                            <td colspan="3" class="action-cell" style="text-align:center;">
                                <button type="button" class="remove-sibling-btn" onclick="removeSibling(this)">Remove</button>
                            </td>
                        </tr>
                        <!-- Add Sibling button row -->
                        <tr class="add-sibling-row">
                            <td colspan="24" style="text-align:center;">
                                <button type="button" class="add-sibling-btn" onclick="addSibling()">+ Add Sibling</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </main>
    <script src="../public/js/user_index.js">
        const hasExistingPhoto = false;
    </script>
</body>
<?php includeAndCache('../includes/footer.php'); ?>
</html>