<?php
include('../middleware/checkSession.php');
include_once('../middleware/cache.php');
require_once '../vendor/autoload.php';
require_once '../config/db.php';

// -------------------------------------------------------------------
// 1. HANDLE UPDATE SUBMISSION
// -------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $applicant_id = $_POST['id'] ?? null;
    if (!$applicant_id) {
        die("No applicant ID provided for update.");
    }

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

    // Combine how_heard (same logic as insert)
    $how_heard = ($_POST['how_heard'] === 'others')
        ? ($_POST['how_heard_other'] ?? '')
        : ($_POST['how_heard'] ?? '');

    $sibling_names       = json_encode($_POST['sibling_name'] ?? []);
    $sibling_educations  = json_encode($_POST['sibling_education'] ?? []);
    $sibling_occupations = json_encode($_POST['sibling_occupation'] ?? []);

    /* ---------------- PHOTO HANDLING ---------------- */
    // Fetch current photo path from database (in case no new file)
    $stmt = $conn->prepare("SELECT photo FROM applicants WHERE id = ?");
    $stmt->bind_param("i", $applicant_id);
    $stmt->execute();
    $stmt->bind_result($old_photo);
    $stmt->fetch();
    $stmt->close();

    $photo_path = $old_photo; // keep old by default

    if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === 0) {
        $dir = '../public/images/uploads/';
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        // Optionally delete old photo file
        if ($old_photo && file_exists($dir . $old_photo)) {
            unlink($dir . $old_photo);
        }

        $file = time() . '_' . basename($_FILES['photo']['name']);
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $dir . $file)) {
            $photo_path = '' . $file; // store just filename (same as insert)
        }
    }

    /* ---------------- UPDATE QUERY ---------------- */
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

    // Bind parameters – 51 fields + 1 for WHERE = 52 placeholders
$stmt->bind_param(
    str_repeat('s', 50) . 'i',
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
    $sibling_names, $sibling_educations, $sibling_occupations,
    $applicant_id
);

    if ($stmt->execute()) {
        // Redirect back to the view page (or wherever you prefer)
        header("Location: view.php?id=" . $applicant_id);
        exit;
    } else {
        echo "<p style='color:red;'>Update Error: {$stmt->error}</p>";
    }
    $stmt->close();
}

// -------------------------------------------------------------------
// 2. FETCH EXISTING DATA FOR DISPLAY (same as before)
// -------------------------------------------------------------------
$applicant_id = $_GET['id'] ?? null;
if (!$applicant_id) {
    die("No applicant ID provided.");
}

$stmt = $conn->prepare("SELECT * FROM applicants WHERE id = ?");
$stmt->bind_param("i", $applicant_id);
$stmt->execute();
$result = $stmt->get_result();
$applicant = $result->fetch_assoc();
$stmt->close();

if (!$applicant) {
    die("Applicant not found.");
}

// Decode sibling data
$sibling_names = json_decode($applicant['sibling_names'], true) ?? [];
$sibling_educations = json_decode($applicant['sibling_educations'], true) ?? [];
$sibling_occupations = json_decode($applicant['sibling_occupations'], true) ?? [];

// For file upload preview
$uploadBaseUrl = '../public/images/uploads/';
$hasPhoto = !empty($applicant['photo']);

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
<html>
<?php includeAndCache('../includes/admin_head.php'); ?>
<link rel="stylesheet" href="../public/css/admin_update.css">
<style>
 
</style>
<body>
    <?php includeAndCache('../includes/admin_sidebar.php'); ?>
    <main>
        <div class="container">
            <div>
                <h1>Edit Applicant</h1>
                <div class="btn-container">
                    <button type="submit" form="updateForm" class="btn btn-update">Save Changes</button>
                    <button type="button" class="btn btn-delete" onclick="return confirm('Are you sure?') ? window.location.href='delete.php?id=<?= $applicant['id'] ?>' : false;">Delete</button>
                    <button type="button" class="pdf-btn" onclick="window.location.href='view.php?id=<?= $applicant['id'] ?>'">Cancel</button>
                </div>
            </div>

            <form id="updateForm" method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $applicant['id'] ?>">

                <table id="applicant-table">
                    <tbody>
                        <tr>
                            <td colspan="5" rowspan="4">
                                <img src="../public/images/system/logo.png" alt="CCSFP Logo" width="120">
                            </td>
                            <td colspan="15">City College of San Fernando Pampanga</td>
                            <td colspan="4">CCSFP Admission Form 001</td>
                        </tr>
                        <tr>
                            <td colspan="15">City of San Fernando, Pampanga</td>
                            <td colspan="4" rowspan="2">Application No: <br> <?= htmlspecialchars($applicant['id']) ?></td>
                        </tr>
                        <tr>
                            <td colspan="15">Email: citycollegesfp@gmail.com</td>
                        </tr>
                        <tr>
                            <td colspan="15">APPLICATION FORM FOR FIRST YEAR</td>                
                            <td colspan="4" rowspan="4" class="photo-td">
                                <div class="photo-wrapper">
                                    <?php if ($hasPhoto): ?>
                                        <img id="preview" src="<?= htmlspecialchars($uploadBaseUrl . basename($applicant['photo'])) ?>" alt="Applicant Photo" style="display:block;">
                                    <?php else: ?>
                                        <img id="preview" src="" alt="Preview" style="display:none;">
                                        <span class="overlay-text">No Photo</span>
                                    <?php endif; ?>
                                </div>
                                <input type="file" name="photo" id="photoInput" accept="image/jpeg,image/png" style="margin-top:5px; width:100%;">
                            </td>
                        </tr>

                        <!-- Courses -->
                        <tr>
                            <td colspan="20">COURSE APPLIED FOR:</td>
                        </tr>
                        <tr>
                            <td colspan="5">1st Choice:</td>
                            <td colspan="15">
                                <select name="course_first" id="course_first" class="form-select">
                                    <option value="">Select First Choice</option>
                                    <?php foreach ($courses as $course): ?>
                                        <option value="<?= htmlspecialchars($course['code']) ?>"
                                            <?= $applicant['course_first'] == $course['code'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($course['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5">2nd Choice:</td>
                            <td colspan="15">
                                <select name="course_second" id="course_second" class="form-select">
                                    <option value="">Select Second Choice</option>
                                    <?php foreach ($courses as $course): ?>
                                        <option value="<?= htmlspecialchars($course['code']) ?>"
                                            <?= $applicant['course_second'] == $course['code'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($course['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>

                        <!-- I. PERSONAL INFO -->
                        <tr><td colspan="24">I. PERSONAL INFORMATION</td></tr>
                        <tr>
                            <td colspan="3">Last Name:</td>
                            <td colspan="5"><input type="text" name="last_name" class="form-input" value="<?= htmlspecialchars($applicant['last_name']) ?>"></td>
                            <td colspan="3">First Name:</td>
                            <td colspan="5"><input type="text" name="first_name" class="form-input" value="<?= htmlspecialchars($applicant['first_name']) ?>"></td>
                            <td colspan="3">Middle Name:</td>
                            <td colspan="5"><input type="text" name="middle_name" class="form-input" value="<?= htmlspecialchars($applicant['middle_name']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="3">Age:</td>
                            <td colspan="5"><input type="number" name="age" class="form-input" value="<?= htmlspecialchars($applicant['age']) ?>"></td>
                            <td colspan="3">Gender:</td>
                            <td colspan="5">
                                <select name="gender" class="form-select">
                                    <option value="Male" <?= $applicant['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= $applicant['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                                    <option value="Other" <?= $applicant['gender'] == 'Other' ? 'selected' : '' ?>>Other</option>
                                </select>
                            </td>
                            <td colspan="3">Date of Birth:</td>
                            <td colspan="5"><input type="date" name="dob" class="form-input" value="<?= htmlspecialchars($applicant['dob']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="4">Place of Birth:</td>
                            <td colspan="12"><input type="text" name="birth_place" class="form-input" value="<?= htmlspecialchars($applicant['birth_place']) ?>"></td>
                            <td colspan="4">Marital Status:</td>
                            <td colspan="4">
                                <select name="marital_status" class="form-select">
                                    <option value="Single" <?= $applicant['marital_status'] == 'Single' ? 'selected' : '' ?>>Single</option>
                                    <option value="Married" <?= $applicant['marital_status'] == 'Married' ? 'selected' : '' ?>>Married</option>
                                    <option value="Divorced" <?= $applicant['marital_status'] == 'Divorced' ? 'selected' : '' ?>>Divorced</option>
                                    <option value="Widowed" <?= $applicant['marital_status'] == 'Widowed' ? 'selected' : '' ?>>Widowed</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">Contact Number/s:</td>
                            <td colspan="5"><input type="text" name="contact" class="form-input" value="<?= htmlspecialchars($applicant['contact']) ?>"></td>
                            <td colspan="2">Religion:</td>
                            <td colspan="3"><input type="text" name="religion" class="form-input" value="<?= htmlspecialchars($applicant['religion']) ?>"></td>
                            <td colspan="3">Email Address:</td>
                            <td colspan="7"><input type="email" name="email" class="form-input" value="<?= htmlspecialchars($applicant['email']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="6">Complete Home Address:</td>
                            <td colspan="18"><input type="text" name="home_address" class="form-input" value="<?= htmlspecialchars($applicant['home_address']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="6">Applicant is living with Relative:</td>
                            <td colspan="18"><input type="text" name="relative_name" class="form-input" value="<?= htmlspecialchars($applicant['relative_name']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="6">Address:</td>
                            <td colspan="18"><input type="text" name="relative_address" class="form-input" value="<?= htmlspecialchars($applicant['relative_address']) ?>"></td>
                        </tr>

                        <!-- II. SCHOLASTIC BACKGROUND -->
                        <tr><td colspan="24">II. SCHOLASTIC BACKGROUND</td></tr>
                        <tr>
                            <td colspan="5">College (Undergraduate):</td>
                            <td colspan="9"><input type="text" name="college" class="form-input" value="<?= htmlspecialchars($applicant['college']) ?>"></td>
                            <td colspan="2">Course:</td>
                            <td colspan="8"><input type="text" name="college_course" class="form-input" value="<?= htmlspecialchars($applicant['college_course']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="5">School Address:</td>
                            <td colspan="9"><input type="text" name="college_address" class="form-input" value="<?= htmlspecialchars($applicant['college_address']) ?>"></td>
                            <td colspan="6">Last Sem/Year Attended:</td>
                            <td colspan="4"><input type="text" name="college_year" class="form-input" value="<?= htmlspecialchars($applicant['college_year']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="6">Senior High School (Grade 12):</td>
                            <td colspan="9"><input type="text" name="shs" class="form-input" value="<?= htmlspecialchars($applicant['shs']) ?>"></td>
                            <td colspan="4">Year Graduated:</td>
                            <td colspan="5"><input type="text" name="shs_year" class="form-input" value="<?= htmlspecialchars($applicant['shs_year']) ?>"></td>
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
                        <tr>
                            <td colspan="7">Junior High School (Grade 10):</td>
                            <td colspan="9"><input type="text" name="jhs" class="form-input" value="<?= htmlspecialchars($applicant['jhs']) ?>"></td>
                            <td colspan="4">Year Graduated:</td>
                            <td colspan="4"><input type="text" name="jhs_year" class="form-input" value="<?= htmlspecialchars($applicant['jhs_year']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="4">School Address:</td>
                            <td colspan="20"><input type="text" name="jhs_address" class="form-input" value="<?= htmlspecialchars($applicant['jhs_address']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="6">Awards/Honors Received:</td>
                            <td colspan="18"><input type="text" name="jhs_awards" class="form-input" value="<?= htmlspecialchars($applicant['jhs_awards']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="4">Primary:</td>
                            <td colspan="12"><input type="text" name="primary_school" class="form-input" value="<?= htmlspecialchars($applicant['primary_school']) ?>"></td>
                            <td colspan="4">Year Graduated:</td>
                            <td colspan="4"><input type="text" name="primary_year" class="form-input" value="<?= htmlspecialchars($applicant['primary_year']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="5">Special Skills/Talents:</td>
                            <td colspan="19"><input type="text" name="skills" class="form-input" value="<?= htmlspecialchars($applicant['skills']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="6">Interest Sports/Affiliations:</td>
                            <td colspan="18"><input type="text" name="sports" class="form-input" value="<?= htmlspecialchars($applicant['sports']) ?>"></td>
                        </tr>

                        <!-- III. FAMILY BACKGROUND -->
                        <tr><td colspan="24">III. FAMILY BACKGROUND</td></tr>
                        <tr>
                            <td colspan="3">Father:</td>
                            <td colspan="4"><input type="text" name="father_name" class="form-input" value="<?= htmlspecialchars($applicant['father_name']) ?>"></td>
                            <td colspan="3">Occupation:</td>
                            <td colspan="4"><input type="text" name="father_occupation" class="form-input" value="<?= htmlspecialchars($applicant['father_occupation']) ?>"></td>
                            <td colspan="6">Employer/Employer’s Address:</td>
                            <td colspan="4"><input type="text" name="father_employer" class="form-input" value="<?= htmlspecialchars($applicant['father_employer']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="3">Mother:</td>
                            <td colspan="4"><input type="text" name="mother_name" class="form-input" value="<?= htmlspecialchars($applicant['mother_name']) ?>"></td>
                            <td colspan="3">Occupation:</td>
                            <td colspan="4"><input type="text" name="mother_occupation" class="form-input" value="<?= htmlspecialchars($applicant['mother_occupation']) ?>"></td>
                            <td colspan="6">Employer/Employer’s Address:</td>
                            <td colspan="4"><input type="text" name="mother_employer" class="form-input" value="<?= htmlspecialchars($applicant['mother_employer']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="3">Guardian:</td>
                            <td colspan="4"><input type="text" name="guardian_name" class="form-input" value="<?= htmlspecialchars($applicant['guardian_name']) ?>"></td>
                            <td colspan="3">Occupation:</td>
                            <td colspan="4"><input type="text" name="guardian_occupation" class="form-input" value="<?= htmlspecialchars($applicant['guardian_occupation']) ?>"></td>
                            <td colspan="6">Employer/Employer’s Address:</td>
                            <td colspan="4"><input type="text" name="guardian_employer" class="form-input" value="<?= htmlspecialchars($applicant['guardian_employer']) ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="5">Guardian’s Address:</td>
                            <td colspan="10"><input type="text" name="guardian_address" class="form-input" value="<?= htmlspecialchars($applicant['guardian_address']) ?>"></td>
                            <td colspan="3">Contact Nos.:</td>
                            <td colspan="6"><input type="text" name="guardian_contact" class="form-input" value="<?= htmlspecialchars($applicant['guardian_contact']) ?>"></td>
                        </tr>

                        <!-- IV. MONTHLY FAMILY INCOME -->
                        <tr><td colspan="24">IV. MONTHLY FAMILY INCOME</td></tr>
                        <tr>
                            <td colspan="24">
                                <div class="radio-group">
                                    <?php foreach ($incomeRanges as $value => $label): ?>
                                        <label>
                                            <input type="radio" name="family_income" value="<?= $value ?>" <?= $applicant['family_income'] == $value ? 'checked' : '' ?>>
                                            <?= $label ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                        </tr>

                        <!-- V. HOW DID YOU KNOW -->
                        <tr><td colspan="24">V. How did you know about City College?</td></tr>
                        <tr>
                            <td colspan="24">
                                <div class="radio-group">
                                    <?php foreach ($howHeardOptions as $value => $label): ?>
                                        <label>
                                            <input type="radio" name="how_heard" value="<?= $value ?>" <?= $applicant['how_heard'] == $value ? 'checked' : '' ?>>
                                            <?= $label ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                                <div style="margin-top:10px;">
                                    <input type="text" name="how_heard_other" id="how_heard_other_text" class="form-input" placeholder="Please specify" value="<?= htmlspecialchars($applicant['how_heard'] ?? '') ?>" style="width:100%;" <?= (!in_array($applicant['how_heard'], ['social media','friend','school visit']) && !empty($applicant['how_heard'])) ? '' : 'disabled' ?>>
                                </div>
                            </td>
                        </tr>

                        <!-- SIBLINGS -->
                        <tr><td colspan="24">SIBLINGS</td></tr>
                        <?php 
                        $siblingCount = count($sibling_names);
                        if ($siblingCount > 0): 
                            for ($i = 0; $i < $siblingCount; $i++): 
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
                                    <button type="button" class="remove-sibling-btn" onclick="removeSibling(this)">Remove</button>
                                </td>
                            </tr>
                        <?php 
                            endfor; 
                        endif; 
                        ?>
                        <!-- Row for adding new siblings -->
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

    <script src="../public/js/admin_update.js"></script>
   <script>
        const hasExistingPhoto = <?= $hasPhoto ? 'true' : 'false' ?>;
    </script>
</body>
<?php includeAndCache('../includes/admin_footer.php'); ?>
</html>

