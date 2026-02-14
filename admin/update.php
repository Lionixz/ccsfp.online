<?php
include('../middleware/checkSession.php');   // admin must be logged in
include('../middleware/cache.php');
require_once '../config/db.php';

$startYear = date("Y");
$endYear   = date("Y") + 1;

// 1. Get applicant ID from URL (admin editing)
$applicant_id = $_GET['id'] ?? 0;
if (!$applicant_id) {
    die("No applicant ID provided.");
}

// 2. Fetch applicant record
$stmt = $conn->prepare("SELECT * FROM applicants WHERE id = ?");
$stmt->bind_param("i", $applicant_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Applicant not found.");
}
$applicant = $result->fetch_assoc();
$stmt->close();

// 3. Decode sibling JSON for pre‑filling
$sibling_names       = json_decode($applicant['sibling_names'] ?? '[]', true);
$sibling_educations  = json_decode($applicant['sibling_educations'] ?? '[]', true);
$sibling_occupations = json_decode($applicant['sibling_occupations'] ?? '[]', true);

// 4. Handle form submission (UPDATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ---------------- I. PERSONAL INFO ---------------- */
    $course_first     = $_POST['course_first'] ?? '';
    $course_second    = $_POST['course_second'] ?? '';
    $last_name        = $_POST['last_name'] ?? '';
    $first_name       = $_POST['first_name'] ?? '';
    $middle_name      = $_POST['middle_name'] ?? '';
    $age              = $_POST['age'] !== '' ? (int)$_POST['age'] : null;
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
    $shs_year         = $_POST['shs_year'] !== '' ? (int)$_POST['shs_year'] : null;
    $shs_address      = $_POST['shs_address'] ?? '';
    $shs_lrn          = $_POST['shs_lrn'] ?? '';
    $shs_awards       = $_POST['shs_awards'] ?? '';
    $jhs              = $_POST['jhs'] ?? '';
    $jhs_year         = $_POST['jhs_year'] !== '' ? (int)$_POST['jhs_year'] : null;
    $jhs_address      = $_POST['jhs_address'] ?? '';
    $jhs_awards       = $_POST['jhs_awards'] ?? '';
    $primary_school   = $_POST['primary_school'] ?? '';
    $primary_year     = $_POST['primary_year'] !== '' ? (int)$_POST['primary_year'] : null;
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

    $sibling_names_json       = json_encode($_POST['sibling_name'] ?? []);
    $sibling_educations_json  = json_encode($_POST['sibling_education'] ?? []);
    $sibling_occupations_json = json_encode($_POST['sibling_occupation'] ?? []);

    /* ---------------- PHOTO ---------------- */
    $photo_path = $applicant['photo']; 

 // Only process if a new file was actually uploaded
    if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === 0) {
        $dir = '../public/images/uploads/';
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        // ✅ DELETE THE OLD IMAGE FILE (if it exists)
        if (!empty($applicant['photo'])) {
            $oldPhotoPath = $dir . $applicant['photo']; // stored as 'uploads/filename.jpg'
            if (file_exists($oldPhotoPath)) {
                unlink($oldPhotoPath);
            }
        }

        // Upload the new file
        $file = time() . '_' . basename($_FILES['photo']['name']);
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $dir . $file)) {
            $photo_path = '' . $file; // new path for database
        }
    }

    /* ---------------- UPDATE ---------------- */
    $sql = "
        UPDATE applicants SET
            course_first = ?, course_second = ?, photo = ?,
            last_name = ?, first_name = ?, middle_name = ?,
            age = ?, gender = ?, dob = ?, birth_place = ?, marital_status = ?,
            contact = ?, religion = ?, email = ?, home_address = ?,
            relative_name = ?, relative_address = ?,
            college = ?, college_course = ?, college_address = ?, college_year = ?,
            shs = ?, shs_year = ?, shs_address = ?, shs_lrn = ?, shs_awards = ?,
            jhs = ?, jhs_year = ?, jhs_address = ?, jhs_awards = ?,
            primary_school = ?, primary_year = ?, skills = ?, sports = ?,
            father_name = ?, father_occupation = ?, father_employer = ?,
            mother_name = ?, mother_occupation = ?, mother_employer = ?,
            guardian_name = ?, guardian_occupation = ?, guardian_employer = ?, guardian_address = ?, guardian_contact = ?,
            family_income = ?, how_heard = ?,
            sibling_names = ?, sibling_educations = ?, sibling_occupations = ?
        WHERE id = ?
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) die("Prepare failed: " . $conn->error);

    $types = str_repeat('s', 50) . 'i';   // 50 strings + 1 integer (id)
    $stmt->bind_param(
        $types,
        $course_first, $course_second, $photo_path,
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
        $sibling_names_json, $sibling_educations_json, $sibling_occupations_json,
        $applicant_id   // integer
    );

    if ($stmt->execute()) {
        // ✅ FIX: Redirect to view.php WITH the applicant ID
        header("Location: view.php?id=" . $applicant_id);
        exit;
    } else {
        echo "<p style='color:red;'>Update Error: {$stmt->error}</p>";
    }
    $stmt->close();
}

?>


<!DOCTYPE html>
<html>
<?php includeAndCache('../includes/admin_head.php'); ?>
<link rel="stylesheet" href="../public/css/admin_update.css">

<body>
    <?php includeAndCache('../includes/admin_sidebar.php'); ?>
    <main>
        <div class="container">
            <form method="POST" enctype="multipart/form-data">
                <table>
                    <tbody>
                        <!-- Header -->
                        <tr>
                            <td colspan="3" rowspan="4">
                                <img src="../public/images/system/logo.png" alt="CCSFP Logo" width="120">
                            </td>
                            <td colspan="16">City College of San Fernando Pampanga</td>
                            <td colspan="5">CCSFP Admission Form 001</td>
                        </tr>
                        <tr>
                            <td colspan="17">City of San Fernando, Pampanga</td>
                            <td colspan="4" rowspan="2">Application No: <?= htmlspecialchars($applicant['id']) ?></td>
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
                                        <img id="preview"
                                             src="<?= !empty($applicant['photo']) ? '../public/images/uploads/' . htmlspecialchars($applicant['photo']) : '' ?>"
                                             alt="Image Preview"
                                             class="preview-img"
                                             style="<?= !empty($applicant['photo']) ? 'display:block;' : 'display:none;' ?>">
                                   <input type="file" 
                                        name="photo" 
                                        id="photo" 
                                        accept="image/*" 
                                        onchange="previewImage(event)" 
                                        class="file-input"
                                        data-existing="<?= !empty($applicant['photo']) ? '1' : '0' ?>">
                                </div>
                            </td>
                        </tr>

                        <!-- Instructions -->
                        <tr><td colspan="3">INSTRUCTIONS</td><td colspan="16">A. Y. <?= $startYear ?>—<?= $endYear ?></td></tr>
                        <tr><td colspan="19">1. Fill out all required information in this admission form.</td></tr>
                        <tr><td colspan="20">2. Print all entries legibly and only fully accomplished forms (CCSFP-Admission Form 001) will be processed.</td></tr>

                        <!-- Courses (pre‑fill) -->
                        <tr><td colspan="8">COURSE APPLIED FOR:</td><td colspan="11"></td></tr>
                        <tr>
                            <td colspan="5"><label>1st Choice:</label></td>
                            <td colspan="15">
                                <select name="course_first" id="course_first" class="form-select">
                                    <option value="">-- Select Course --</option>
                                    <option value="BSIT" <?= $applicant['course_first'] == 'BSIT' ? 'selected' : '' ?>>Bachelor of Science in Information Technology (BSIT)</option>
                                    <option value="BSCS" <?= $applicant['course_first'] == 'BSCS' ? 'selected' : '' ?>>Bachelor of Science in Computer Science (BSCS)</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5"><label>2nd Choice:</label></td>
                            <td colspan="15">
                                <select name="course_second" id="course_second" class="form-select">
                                    <option value="">-- Select Course --</option>
                                    <option value="BSIT" <?= $applicant['course_second'] == 'BSIT' ? 'selected' : '' ?>>Bachelor of Science in Information Technology (BSIT)</option>
                                    <option value="BSCS" <?= $applicant['course_second'] == 'BSCS' ? 'selected' : '' ?>>Bachelor of Science in Computer Science (BSCS)</option>
                                </select>
                            </td>
                        </tr>

                        <!-- I. PERSONAL INFORMATION (all fields pre‑filled) -->
                        <tr><td colspan="24" style="text-align:left; font-weight:bold;">I. PERSONAL INFORMATION</td></tr>
                        <tr>
                            <td colspan="3">1. Last Name:</td>
                            <td colspan="5"><input type="text" name="last_name" class="form-input" value="<?= htmlspecialchars($applicant['last_name']) ?>"></td>
                            <td colspan="3">First Name:</td>
                            <td colspan="5"><input type="text" name="first_name" class="form-input" value="<?= htmlspecialchars($applicant['first_name']) ?>"></td>
                            <td colspan="3">Middle Name:</td>
                            <td colspan="5"><input type="text" name="middle_name" class="form-input" value="<?= htmlspecialchars($applicant['middle_name']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="2">2. Age:</td>
                            <td colspan="3"><input type="number" name="age" class="form-input" value="<?= htmlspecialchars($applicant['age']) ?>"></td>
                            <td colspan="3">3. Gender:</td>
                            <td colspan="3"><input type="text" name="gender" class="form-input" value="<?= htmlspecialchars($applicant['gender']) ?>"></td>
                            <td colspan="4">4. Date of Birth:</td>
                            <td colspan="9"><input type="date" name="dob" class="form-input" value="<?= htmlspecialchars($applicant['dob']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="4">5. Place of Birth:</td>
                            <td colspan="12"><input type="text" name="birth_place" class="form-input" value="<?= htmlspecialchars($applicant['birth_place']) ?>"></td>
                            <td colspan="4">6. Marital Status:</td>
                            <td colspan="4">
                                <select name="marital_status" class="form-select">
                                    <option value="">-- Select --</option>
                                    <option value="Single" <?= $applicant['marital_status'] == 'Single' ? 'selected' : '' ?>>Single</option>
                                    <option value="Married" <?= $applicant['marital_status'] == 'Married' ? 'selected' : '' ?>>Married</option>
                                    <option value="Widowed" <?= $applicant['marital_status'] == 'Widowed' ? 'selected' : '' ?>>Widowed</option>
                                    <option value="Separated" <?= $applicant['marital_status'] == 'Separated' ? 'selected' : '' ?>>Separated</option>
                                    <option value="Others" <?= $applicant['marital_status'] == 'Others' ? 'selected' : '' ?>>Others</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">7. Contact Number/s:</td>
                            <td colspan="5"><input type="text" name="contact" class="form-input" value="<?= htmlspecialchars($applicant['contact']) ?>"></td>
                            <td colspan="2">8. Religion:</td>
                            <td colspan="3"><input type="text" name="religion" class="form-input" value="<?= htmlspecialchars($applicant['religion']) ?>"></td>
                            <td colspan="3">9. Email Address:</td>
                            <td colspan="7"><input type="email" name="email" class="form-input" value="<?= htmlspecialchars($applicant['email']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="6">10. Complete Home Address:</td>
                            <td colspan="18"><input type="text" name="home_address" class="form-input" value="<?= htmlspecialchars($applicant['home_address']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="7">11. Applicant is living with Relative:</td>
                            <td colspan="17"><input type="text" name="relative_name" class="form-input" value="<?= htmlspecialchars($applicant['relative_name']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="3">12. Address:</td>
                            <td colspan="21"><input type="text" name="relative_address" class="form-input" value="<?= htmlspecialchars($applicant['relative_address']) ?>"></td>
                        </tr>

                        <!-- II. SCHOLASTIC BACKGROUND (pre‑filled) -->
                        <tr><td colspan="24" style="text-align:left; font-weight:bold;">II. SCHOLASTIC BACKGROUND</td></tr>
                        <!-- College -->
                        <tr>
                            <td colspan="6">13. College (Undergraduate):</td>
                            <td colspan="9"><input type="text" name="college" class="form-input" value="<?= htmlspecialchars($applicant['college']) ?>"></td>
                            <td colspan="2">Course:</td>
                            <td colspan="7"><input type="text" name="college_course" class="form-input" value="<?= htmlspecialchars($applicant['college_course']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="4">School Address:</td>
                            <td colspan="10"><input type="text" name="college_address" class="form-input" value="<?= htmlspecialchars($applicant['college_address']) ?>"></td>
                            <td colspan="6">Last Sem/Year Attended:</td>
                            <td colspan="4"><input type="text" name="college_year" class="form-input" value="<?= htmlspecialchars($applicant['college_year']) ?>"></td>
                        </tr>
                        <!-- SHS -->
                        <tr>
                            <td colspan="7">14. Senior High School (Grade 12):</td>
                            <td colspan="9"><input type="text" name="shs" class="form-input" value="<?= htmlspecialchars($applicant['shs']) ?>"></td>
                            <td colspan="4">Year Graduated:</td>
                            <td colspan="4"><input type="number" name="shs_year" class="form-input" value="<?= htmlspecialchars($applicant['shs_year']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="4">School Address:</td>
                            <td colspan="13"><input type="text" name="shs_address" class="form-input" value="<?= htmlspecialchars($applicant['shs_address']) ?>"></td>
                            <td colspan="2">LRN #:</td>
                            <td colspan="5"><input type="text" name="shs_lrn" class="form-input" value="<?= htmlspecialchars($applicant['shs_lrn']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="6">Awards/Honors Received:</td>
                            <td colspan="18"><input type="text" name="shs_awards" class="form-input" value="<?= htmlspecialchars($applicant['shs_awards']) ?>"></td>
                        </tr>
                        <!-- JHS -->
                        <tr>
                            <td colspan="7">15. Junior High School (Grade 10):</td>
                            <td colspan="9"><input type="text" name="jhs" class="form-input" value="<?= htmlspecialchars($applicant['jhs']) ?>"></td>
                            <td colspan="4">Year Graduated:</td>
                            <td colspan="4"><input type="number" name="jhs_year" class="form-input" value="<?= htmlspecialchars($applicant['jhs_year']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="4">School Address:</td>
                            <td colspan="20"><input type="text" name="jhs_address" class="form-input" value="<?= htmlspecialchars($applicant['jhs_address']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="6">Awards/Honors Received:</td>
                            <td colspan="18"><input type="text" name="jhs_awards" class="form-input" value="<?= htmlspecialchars($applicant['jhs_awards']) ?>"></td>
                        </tr>
                        <!-- Primary -->
                        <tr>
                            <td colspan="3">16. Primary:</td>
                            <td colspan="13"><input type="text" name="primary_school" class="form-input" value="<?= htmlspecialchars($applicant['primary_school']) ?>"></td>
                            <td colspan="4">Year Graduated:</td>
                            <td colspan="4"><input type="number" name="primary_year" class="form-input" value="<?= htmlspecialchars($applicant['primary_year']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="5">17. Special Skills/Talents:</td>
                            <td colspan="19"><input type="text" name="skills" class="form-input" value="<?= htmlspecialchars($applicant['skills']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="6">18. Interest Sports/Affiliations:</td>
                            <td colspan="18"><input type="text" name="sports" class="form-input" value="<?= htmlspecialchars($applicant['sports']) ?>"></td>
                        </tr>

                        <!-- III. FAMILY BACKGROUND -->
                        <tr><td colspan="24" style="text-align:left; font-weight:bold;">III. FAMILY BACKGROUND</td></tr>
                        <tr>
                            <td colspan="3">Father:</td>
                            <td colspan="4"><input type="text" name="father_name" value="<?= htmlspecialchars($applicant['father_name']) ?>"></td>
                            <td colspan="3">Occupation:</td>
                            <td colspan="3"><input type="text" name="father_occupation" value="<?= htmlspecialchars($applicant['father_occupation']) ?>"></td>
                            <td colspan="7">Employer/Employer’s Address:</td>
                            <td colspan="4"><input type="text" name="father_employer" value="<?= htmlspecialchars($applicant['father_employer']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="3">Mother:</td>
                            <td colspan="4"><input type="text" name="mother_name" value="<?= htmlspecialchars($applicant['mother_name']) ?>"></td>
                            <td colspan="3">Occupation:</td>
                            <td colspan="3"><input type="text" name="mother_occupation" value="<?= htmlspecialchars($applicant['mother_occupation']) ?>"></td>
                            <td colspan="7">Employer/Employer’s Address:</td>
                            <td colspan="4"><input type="text" name="mother_employer" value="<?= htmlspecialchars($applicant['mother_employer']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="3">Guardian:</td>
                            <td colspan="4"><input type="text" name="guardian_name" value="<?= htmlspecialchars($applicant['guardian_name']) ?>"></td>
                            <td colspan="3">Occupation:</td>
                            <td colspan="3"><input type="text" name="guardian_occupation" value="<?= htmlspecialchars($applicant['guardian_occupation']) ?>"></td>
                            <td colspan="7">Employer/Employer’s Address:</td>
                            <td colspan="4"><input type="text" name="guardian_employer" value="<?= htmlspecialchars($applicant['guardian_employer']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="5">Guardian’s Address:</td>
                            <td colspan="10"><input type="text" name="guardian_address" value="<?= htmlspecialchars($applicant['guardian_address']) ?>"></td>
                            <td colspan="3">Contact Nos.:</td>
                            <td colspan="6"><input type="text" name="guardian_contact" value="<?= htmlspecialchars($applicant['guardian_contact']) ?>"></td>
                        </tr>

                        <!-- IV. MONTHLY FAMILY INCOME -->
                        <tr><td colspan="24" style="text-align:left; font-weight:bold;">IV. MONTHLY FAMILY INCOME</td></tr>
                        <tr>
                            <td colspan="6"><label><input type="radio" name="family_income" value="219140 above" <?= $applicant['family_income'] == '219140 above' ? 'checked' : '' ?>> ₱219,140 and above</label></td>
                            <td colspan="6"><label><input type="radio" name="family_income" value="131483 219140" <?= $applicant['family_income'] == '131483 219140' ? 'checked' : '' ?>> ₱131,483 – ₱219,140</label></td>
                            <td colspan="6"><label><input type="radio" name="family_income" value="76669 131484" <?= $applicant['family_income'] == '76669 131484' ? 'checked' : '' ?>> ₱76,669 – ₱131,484</label></td>
                            <td colspan="6"><label><input type="radio" name="family_income" value="43828 76669" <?= $applicant['family_income'] == '43828 76669' ? 'checked' : '' ?>> ₱43,828 – ₱76,669</label></td>
                        </tr>
                        <tr>
                            <td colspan="6"><label><input type="radio" name="family_income" value="21914_43828" <?= $applicant['family_income'] == '21914_43828' ? 'checked' : '' ?>> ₱21,914 – ₱43,828</label></td>
                            <td colspan="6"><label><input type="radio" name="family_income" value="10957_21914" <?= $applicant['family_income'] == '10957_21914' ? 'checked' : '' ?>> ₱10,957 – ₱21,914</label></td>
                            <td colspan="6"><label><input type="radio" name="family_income" value="below_10957" <?= $applicant['family_income'] == 'below_10957' ? 'checked' : '' ?>> Below ₱10,957</label></td>
                            <td colspan="6"></td>
                        </tr>

                        <!-- V. HOW DID YOU KNOW -->
                        <tr><td colspan="24" style="text-align:left; font-weight:bold;">V. How did you know about City College?</td></tr>
                        <?php
                            $how_heard_value = $applicant['how_heard'] ?? '';
                            $is_other = !in_array($how_heard_value, ['Tarpaulin', 'Word of mouth', 'Internet/Social Media']);
                        ?>
                        <tr>
                            <td colspan="3"><label><input type="radio" name="how_heard" value="Tarpaulin" <?= $how_heard_value == 'Tarpaulin' ? 'checked' : '' ?>> Tarpaulin</label></td>
                            <td colspan="4"><label><input type="radio" name="how_heard" value="Word of mouth" <?= $how_heard_value == 'Word of mouth' ? 'checked' : '' ?>> Word of mouth</label></td>
                            <td colspan="6"><label><input type="radio" name="how_heard" value="Internet/Social Media" <?= $how_heard_value == 'Internet/Social Media' ? 'checked' : '' ?>> Internet/Social Media</label></td>
                            <td colspan="11">
                                <label><input type="radio" name="how_heard" value="other" id="how_heard_other_radio" <?= $is_other ? 'checked' : '' ?>> Other (Please Specify):</label>
                                <input type="text" name="how_heard_other" id="how_heard_other_text" class="form-input" style="width:100%; margin-top:5px;" value="<?= $is_other ? htmlspecialchars($how_heard_value) : '' ?>" <?= $is_other ? '' : 'disabled' ?>>
                            </td>
                        </tr>

                        <!-- SIBLINGS -->
                        <tr><td colspan="24" style="text-align:left; font-weight:bold;">SIBLINGS</td></tr>
                        <?php
                            $sibling_count = max(count($sibling_names), count($sibling_educations), count($sibling_occupations));
                            if ($sibling_count == 0) $sibling_count = 1;
                            for ($i = 0; $i < $sibling_count; $i++):
                        ?>
                        <tr class="sibling-row">
                            <td colspan="3">Name:</td>
                            <td colspan="11"><input type="text" name="sibling_name[]" class="form-input" value="<?= htmlspecialchars($sibling_names[$i] ?? '') ?>"></td>
                            <td colspan="5">Educational Attainment:</td>
                            <td colspan="5"><input type="text" name="sibling_education[]" class="form-input" value="<?= htmlspecialchars($sibling_educations[$i] ?? '') ?>"></td>
                        </tr>
                        <tr class="sibling-row">
                            <td colspan="9">Occupation / Employer / School Attending:</td>
                            <td colspan="12"><input type="text" name="sibling_occupation[]" class="form-input" value="<?= htmlspecialchars($sibling_occupations[$i] ?? '') ?>"></td>
                            <td colspan="3" style="text-align:center;">
                                <?php if ($i > 0): ?>
                                    <button type="button" onclick="removeSibling(this)">Remove</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endfor; ?>
                        <tr class="add-sibling-row">
                            <td colspan="24" style="text-align:left;">
                                <button type="button" class="btn-add" onclick="addSibling()">Add Sibling</button>
                            </td>
                        </tr>

                        <!-- Submit -->
                      <tr>
        <td colspan="24" style="text-align:center;">
            <a href="view.php?id=<?= $row['id'] ?>" class="btn btn-view">View</a>
            
            <!-- Submit Button -->
            <button type="submit" class="btn-submit">Submit</button>

            <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this applicant?');">Delete</a>
        </td>
    </tr>
                        


                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </main>

    <script src="../public/js/admin_update.js"></script>
    <?php includeAndCache('../includes/admin_footer.php'); ?>
</body>
</html>