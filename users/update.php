<?php
include('../middleware/checkSession.php');
include('../middleware/cache.php');
require_once '../config/db.php';

$startYear = date("Y");
$endYear   = date("Y") + 1;

$users_id = $_SESSION['user_id'] ?? null;
if (!$users_id) {
    die("User not logged in.");
}

/* ================================
   1. FETCH EXISTING DATA
================================ */
$stmt = $conn->prepare("SELECT * FROM applicants WHERE users_id = ?");
$stmt->bind_param("s", $users_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No application found.");
}

$applicant = $result->fetch_assoc();
$stmt->close();

/* ================================
   2. HANDLE UPDATE
================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* PERSONAL INFO */
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

    /* SCHOLASTIC */
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

    /* FAMILY */
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

    $family_income = $_POST['family_income'] ?? '';
    $how_heard     = ($_POST['how_heard'] === 'other')
        ? ($_POST['how_heard_other'] ?? '')
        : ($_POST['how_heard'] ?? '');

    $sibling_names       = json_encode($_POST['sibling_name'] ?? []);
    $sibling_educations  = json_encode($_POST['sibling_education'] ?? []);
    $sibling_occupations = json_encode($_POST['sibling_occupation'] ?? []);

    /* PHOTO UPDATE */
    $photo_path = $applicant['photo']; // keep old by default

    if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === 0) {
        $dir = '../public/uploads/';
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $file = time() . '_' . basename($_FILES['photo']['name']);
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $dir . $file)) {
            $photo_path = 'uploads/' . $file;
        }
    }

    /* ================================
       UPDATE QUERY
    ================================= */
    $sql = "
        UPDATE applicants SET
            course_first=?, course_second=?, photo=?,
            last_name=?, first_name=?, middle_name=?,
            age=?, gender=?, dob=?, birth_place=?, marital_status=?,
            contact=?, religion=?, email=?, home_address=?,
            relative_name=?, relative_address=?,
            college=?, college_course=?, college_address=?, college_year=?,
            shs=?, shs_year=?, shs_address=?, shs_lrn=?, shs_awards=?,
            jhs=?, jhs_year=?, jhs_address=?, jhs_awards=?,
            primary_school=?, primary_year=?, skills=?, sports=?,
            father_name=?, father_occupation=?, father_employer=?,
            mother_name=?, mother_occupation=?, mother_employer=?,
            guardian_name=?, guardian_occupation=?, guardian_employer=?, guardian_address=?, guardian_contact=?,
            family_income=?, how_heard=?,
            sibling_names=?, sibling_educations=?, sibling_occupations=?
        WHERE users_id=?
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) die("Prepare failed: " . $conn->error);

    $stmt->bind_param(
        str_repeat('s', 51),
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
        $users_id
    );

    if ($stmt->execute()) {
        header("Location: view.php");
        exit;
    } else {
        echo "<p style='color:red;'>Update Error: {$stmt->error}</p>";
    }

    $stmt->close();
}
?>









<!DOCTYPE html>
<html>
<?php includeAndCache('../includes/head.php'); ?>
<link rel="stylesheet" href="../public/css/user.index.css">

<style>
    .error {
        border: 2px solid #e74c3c !important;
        background-color: #fff5f5;
    }

    .error-msg {
        color: #e74c3c;
        font-size: 13px;
        margin-top: 4px;
    }

    
</style>


<body>
    <?php includeAndCache('../includes/sidebar.php'); ?>
    <main>
        <div class="container">
            <form method="POST" enctype="multipart/form-data">
                <table>
                    <tbody>
                        Header Section
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

                        Courses
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

                        <!-- /////////////////////////////// Part 1. I. PERSONAL INFORMATION /////////////////////////////// -->
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

                        <!-- /////////////////////////////// Part 2. II. SCHOLASTIC BACKGROUND /////////////////////////////// -->
                        <!-- Section Title -->
                        <tr>
                            <td colspan="24" style="text-align:left; font-weight:bold;">II. SCHOLASTIC BACKGROUND</td>
                        </tr>

                        <!-- Row 13: College (Undergraduate) -->
                        <tr>
                            <td colspan="6">13. College (Undergraduate):</td>
                            <td colspan="9"><input type="text" name="college" class="form-input" style="width:100%;"></td>

                            <td colspan="2">Course:</td>
                            <td colspan="7"><input type="text" name="college_course" class="form-input" style="width:100%;"></td>
                        </tr>
                        <tr>
                            <td colspan="4">School Address:</td>
                            <td colspan="10"><input type="text" name="college_address" class="form-input" style="width:100%;"></td>

                            <td colspan="6">Last Sem/Year Attended:</td>
                            <td colspan="4"><input type="text" name="college_year" class="form-input" style="width:100%;"></td>
                        </tr>

                        <!-- Row 14: Senior High School (Grade 12) -->
                        <tr>
                            <td colspan="7">14. Senior High School (Grade 12):</td>
                            <td colspan="9"><input type="text" name="shs" class="form-input" style="width:100%;"></td>
                            <td colspan="4">Year Graduated:</td>
                            <td colspan="4"><input type="number" name="shs_year" class="form-input" style="width:100%;" min="1900" max="2099" step="1" placeholder="YYYY"></td>
                        </tr>
                        <tr>
                            <td colspan="4">School Address:</td>
                            <td colspan="13"><input type="text" name="shs_address" class="form-input" style="width:100%;"></td>
                            <td colspan="2">LRN #:</td>
                            <td colspan="5"><input type="text" name="shs_lrn" class="form-input" style="width:100%;"></td>
                        </tr>
                        <tr>
                            <td colspan="6">Awards/Honors Received:</td>
                            <td colspan="18"><input type="text" name="shs_awards" class="form-input" style="width:100%;"></td>
                        </tr>

                        <!-- Row 15: Junior High School (Grade 10) -->
                        <tr>
                            <td colspan="7">15. Junior High School (Grade 10):</td>
                            <td colspan="9"><input type="text" name="jhs" class="form-input" style="width:100%;"></td>
                            <td colspan="4">Year Graduated:</td>
                            <td colspan="4"><input type="number" name="jhs_year" class="form-input" style="width:100%;" min="1900" max="2099" step="1" placeholder="YYYY"></td>
                        </tr>
                        <tr>
                            <td colspan="4">School Address:</td>
                            <td colspan="20"><input type="text" name="jhs_address" class="form-input" style="width:100%;"></td>
                        </tr>
                        <tr>
                            <td colspan="6">Awards/Honors Received:</td>
                            <td colspan="18"><input type="text" name="jhs_awards" class="form-input" style="width:100%;"></td>
                        </tr>

                        <!-- Row 16: Primary -->
                        <tr>
                            <td colspan="3">16. Primary:</td>
                            <td colspan="13"><input type="text" name="primary_school" class="form-input" style="width:100%;"></td>
                            <td colspan="4">Year Graduated:</td>
                            <td colspan="4"><input type="number" name="primary_year" class="form-input" style="width:100%;" min="1900" max="2099" step="1" placeholder="YYYY"></td>
                        </tr>

                        <!-- Row 17: Special Skills/Talents -->
                        <tr>
                            <td colspan="5">17. Special Skills/Talents:</td>
                            <td colspan="19"><input type="text" name="skills" class="form-input" style="width:100%;"></td>
                        </tr>

                        <!-- Row 18: Interest Sports/Affiliations -->
                        <tr>
                            <td colspan="6">18. Interest Sports/Affiliations:</td>
                            <td colspan="18"><input type="text" name="sports" class="form-input" style="width:100%;"></td>
                        </tr>

                        <!-- /////////////////////////////// Part 3 III. FAMILY BACKGROUND /////////////////////////////// -->
                        <!-- Section Title -->
                        <tr>
                            <td colspan="24" style="text-align:left; font-weight:bold;">III. FAMILY BACKGROUND</td>
                        </tr>

                        <!-- Row 1: Father -->
                        <tr>
                            <td colspan="3">Father:</td>
                            <td colspan="4"><input type="text" name="father_name" class="form-input" style="width:100%;"></td>
                            <td colspan="3">Occupation:</td>
                            <td colspan="3"><input type="text" name="father_occupation" class="form-input" style="width:100%;"></td>
                            <td colspan="7">Employer/Employer’s Address:</td>
                            <td colspan="4"><input type="text" name="father_employer" class="form-input" style="width:100%;"></td>
                        </tr>

                        <!-- Row 2: Mother -->
                        <tr>
                            <td colspan="3">Mother:</td>
                            <td colspan="4"><input type="text" name="mother_name" class="form-input" style="width:100%;"></td>
                            <td colspan="3">Occupation:</td>
                            <td colspan="3"><input type="text" name="mother_occupation" class="form-input" style="width:100%;"></td>
                            <td colspan="7">Employer/Employer’s Address:</td>
                            <td colspan="4"><input type="text" name="mother_employer" class="form-input" style="width:100%;"></td>
                        </tr>

                        <!-- Row 3: Guardian -->
                        <tr>
                            <td colspan="3">Guardian:</td>
                            <td colspan="4"><input type="text" name="guardian_name" class="form-input" style="width:100%;"></td>
                            <td colspan="3">Occupation:</td>
                            <td colspan="3"><input type="text" name="guardian_occupation" class="form-input" style="width:100%;"></td>
                            <td colspan="7">Employer/Employer’s Address:</td>
                            <td colspan="4"><input type="text" name="guardian_employer" class="form-input" style="width:100%;"></td>
                        </tr>

                        <!-- Row 4: Guardian Address & Contact -->
                        <tr>
                            <td colspan="5">Guardian’s Address:</td>
                            <td colspan="10"><input type="text" name="guardian_address" class="form-input" style="width:100%;"></td>
                            <td colspan="3">Contact Nos.:</td>
                            <td colspan="6"><input type="text" name="guardian_contact" class="form-input" style="width:100%;"></td>
                        </tr>

                        <!-- /////////////////////////////// Part 4 IV. MONTHLY FAMILY INCOME /////////////////////////////// -->
                        <!-- Section Title -->
                        <tr>
                            <td colspan="24" style="text-align:left; font-weight:bold;">IV. MONTHLY FAMILY INCOME</td>
                        </tr>

                        <!-- Row 1: First three options -->
                        <tr>
                            <td colspan="6">
                                <label><input type="radio" name="family_income" value="219140_above"> ₱ 219,140 and above</label>
                            </td>
                            <td colspan="6">
                                <label><input type="radio" name="family_income" value="131483_219140"> ₱ 131,483 to ₱ 219,140</label>
                            </td>
                            <td colspan="6">
                                <label><input type="radio" name="family_income" value="76669_131484"> ₱ 76,669 to ₱ 131,484</label>
                            </td>
                            <td colspan="6">
                                <label><input type="radio" name="family_income" value="43828_76669"> ₱ 43,828 to ₱ 76,669</label>
                            </td>
                        </tr>

                        <!-- Row 2: Last three options -->
                        <tr>
                            <td colspan="6">
                                <label><input type="radio" name="family_income" value="21914_43828"> ₱ 21,914 to ₱ 43,828</label>
                            </td>
                            <td colspan="6">
                                <label><input type="radio" name="family_income" value="10957_21914"> ₱ 10,957 to ₱ 21,914</label>
                            </td>
                            <td colspan="6">
                                <label><input type="radio" name="family_income" value="below_10957"> Below ₱ 10,957</label>
                            </td>
                            <td colspan="6"></td>
                        </tr>

                        <!-- /////////////////////////////// Part 5 V. HOW DID YOU KNOW ABOUT CITY COLLEGE? /////////////////////////////// -->
                        <!-- Section V: How did you know about City College? -->
                        <tr>
                            <td colspan="24" style="text-align:left; font-weight:bold;">V. How did you know about City College? (Shade the circle of your answer)</td>
                        </tr>
                       <tr>
                            <td colspan="3">
                                <label><input type="radio" name="how_heard" value="Tarpaulin"> Tarpaulin</label>
                            </td>
                            <td colspan="4">
                                <label><input type="radio" name="how_heard" value="Word of mouth"> Word-of-mouth</label>
                            </td>
                            <td colspan="6">
                                <label><input type="radio" name="how_heard" value="Internet/Social Media"> Internet/Social Media</label>
                            </td>

                            <td colspan="11">
                                <label>
                                    <input type="radio"
                                        name="how_heard"
                                        value="other"
                                        id="how_heard_other_radio">
                                    Other (Please Specify):
                                </label>
                                <input type="text"
                                    name="how_heard_other"
                                    id="how_heard_other_text"
                                    class="form-input"
                                    style="width:100%; margin-top:5px;"
                                    disabled>
                            </td>
                        </tr>

                        <!-- /////////////////////////////// Part 6. SIBLINGS /////////////////////////////// -->
                        <!-- Section Title -->
                        <tr>
                            <td colspan="24" style="text-align:left; font-weight:bold;">SIBLINGS</td>
                        </tr>

                        <!-- Initial sibling -->
                        <tr class="sibling-row">
                            <td colspan="3">Name:</td>
                            <td colspan="11">
                                <input type="text" name="sibling_name[]" class="form-input" style="width:100%;">
                            </td>
                            <td colspan="5">Educational Attainment:</td>
                            <td colspan="5">
                                <input type="text" name="sibling_education[]" class="form-input" style="width:100%;">
                            </td>
                        </tr>

                        <tr class="sibling-row">
                            <td colspan="9">Occupation / Employer / School Attending:</td>
                            <td colspan="12">
                                <input type="text" name="sibling_occupation[]" class="form-input" style="width:100%;">
                            </td>
                            <td colspan="3" style="text-align:center;">
                                <button type="button" onclick="removeSibling(this)">Remove</button>
                            </td>
                        </tr>

                        <!-- Add Sibling button row -->
                        <tr class="add-sibling-row">
                            <td colspan="24" style="text-align:left;">
                                <button type="button" class="btn-add" onclick="addSibling()">Add Sibling</button>
                            </td>
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
