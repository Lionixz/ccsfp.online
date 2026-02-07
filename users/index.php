<?php
include('../middleware/checkSession.php');
include('../middleware/cache.php');

$messageHtml = ''; // This will be inserted in the form table

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    function clean($data)
    {
        return htmlspecialchars(strip_tags(trim($data)));
    }

    $errors = [];

    // Example required fields
    $last_name = clean($_POST['last_name'] ?? '');
    $first_name = clean($_POST['first_name'] ?? '');
    $age = clean($_POST['age'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $course_first = clean($_POST['course_first'] ?? '');

    // Validation
    if (!$last_name) $errors[] = "Last name is required.";
    if (!$first_name) $errors[] = "First name is required.";
    if (!$age || !is_numeric($age) || $age < 10 || $age > 100) $errors[] = "Valid age is required.";
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if (!$course_first) $errors[] = "First choice course is required.";

    // File upload handling
    $photo_file = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['photo']['tmp_name']);
        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = "Invalid file type. Only JPG, PNG, GIF are allowed.";
        } elseif ($_FILES['photo']['size'] > 2 * 1024 * 1024) {
            $errors[] = "File too large. Maximum size is 2MB.";
        } else {
            $uploadDir = realpath(__DIR__ . '/../public/images/uploads/') . '/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $fileName = time() . '_' . preg_replace("/[^a-zA-Z0-9\._-]/", "_", basename($_FILES['photo']['name']));
            $targetFile = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
                $photo_file = 'uploads/' . $fileName;
            } else {
                $errors[] = "Error uploading photo.";
            }
        }
    }

    // Display message in a table row
    if (!empty($errors)) {
        $messageHtml = '<tr><td colspan="24" style="background:#f8d7da;color:#842029;padding:10px;border-radius:5px;">'
            . implode('<br>', $errors) .
            '</td></tr>';
    } else {
        $messageHtml = '<tr><td colspan="24" style="background:#d1e7dd;color:#0f5132;padding:10px;border-radius:5px;">'
            . 'Form submitted successfully.' .
            '</td></tr>';
    }



    if (empty($errors)) {
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Prepare main applicant insert
        $stmt = $conn->prepare("
        INSERT INTO applicants (
            last_name, first_name, middle_name, age, gender, dob, birth_place, marital_status,
            contact, religion, email, home_address, relative_name, relative_address,
            course_first, course_second, college, college_course, college_address, college_year,
            shs, shs_year, shs_address, shs_lrn, shs_awards,
            jhs, jhs_year, jhs_address, jhs_awards,
            primary_school, primary_year, skills, sports,
            father_name, father_occupation, father_employer,
            mother_name, mother_occupation, mother_employer,
            guardian_name, guardian_occupation, guardian_employer, guardian_address, guardian_contact,
            family_income, how_heard, how_heard_other, photo_file
        ) VALUES (
            ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
        )
    ");

        $stmt->bind_param(
            "ssssssssssssssssssssssssssssssssssssssssssssss",
            $last_name,
            $first_name,
            $_POST['middle_name'] ?? '',
            $age,
            $_POST['gender'] ?? '',
            $_POST['dob'] ?? '',
            $_POST['birth_place'] ?? '',
            $_POST['marital_status'] ?? '',
            $_POST['contact'] ?? '',
            $_POST['religion'] ?? '',
            $email,
            $_POST['home_address'] ?? '',
            $_POST['relative_name'] ?? '',
            $_POST['relative_address'] ?? '',
            $course_first,
            $_POST['course_second'] ?? '',
            $_POST['college'] ?? '',
            $_POST['college_course'] ?? '',
            $_POST['college_address'] ?? '',
            $_POST['college_year'] ?? '',
            $_POST['shs'] ?? '',
            $_POST['shs_year'] ?? '',
            $_POST['shs_address'] ?? '',
            $_POST['shs_lrn'] ?? '',
            $_POST['shs_awards'] ?? '',
            $_POST['jhs'] ?? '',
            $_POST['jhs_year'] ?? '',
            $_POST['jhs_address'] ?? '',
            $_POST['jhs_awards'] ?? '',
            $_POST['primary'] ?? '',
            $_POST['primary_year'] ?? '',
            $_POST['skills'] ?? '',
            $_POST['sports'] ?? '',
            $_POST['father_name'] ?? '',
            $_POST['father_occupation'] ?? '',
            $_POST['father_employer'] ?? '',
            $_POST['mother_name'] ?? '',
            $_POST['mother_occupation'] ?? '',
            $_POST['mother_employer'] ?? '',
            $_POST['guardian_name'] ?? '',
            $_POST['guardian_occupation'] ?? '',
            $_POST['guardian_employer'] ?? '',
            $_POST['guardian_address'] ?? '',
            $_POST['guardian_contact'] ?? '',
            $_POST['family_income'] ?? '',
            $_POST['how_heard'] ?? '',
            $_POST['how_heard_other'] ?? '',
            $photo_file ?? ''
        );

        if ($stmt->execute()) {
            $applicant_id = $stmt->insert_id;

            // Insert siblings if any
            if (!empty($_POST['sibling_name'])) {
                $sibling_stmt = $conn->prepare("
                INSERT INTO siblings (applicant_id, name, education, occupation)
                VALUES (?, ?, ?, ?)
            ");

                for ($i = 0; $i < count($_POST['sibling_name']); $i++) {
                    $sibling_name = $_POST['sibling_name'][$i] ?? '';
                    $sibling_education = $_POST['sibling_education'][$i] ?? '';
                    $sibling_occupation = $_POST['sibling_occupation'][$i] ?? '';

                    if ($sibling_name) { // Only insert if name is provided
                        $sibling_stmt->bind_param("isss", $applicant_id, $sibling_name, $sibling_education, $sibling_occupation);
                        $sibling_stmt->execute();
                    }
                }

                $sibling_stmt->close();
            }

            $messageHtml = '<tr><td colspan="24" style="background:#d1e7dd;color:#0f5132;padding:10px;border-radius:5px;">
            Form submitted successfully and data saved.
        </td></tr>';
        } else {
            $messageHtml = '<tr><td colspan="24" style="background:#f8d7da;color:#842029;padding:10px;border-radius:5px;">
            Error saving data: ' . $conn->error . '
        </td></tr>';
        }

        $stmt->close();
        $conn->close();
    }
}

$startYear = date('Y');
$endYear = $startYear + 1;
?>

<!DOCTYPE html>
<html>
<?php includeAndCache('../includes/head.php'); ?>
<link rel="stylesheet" href="../public/css/user.index.css">


<body>
    <?php includeAndCache('../includes/sidebar.php'); ?>
    <main>
        <div class="container">
            <div class="user-header">
                <img src="<?= htmlspecialchars($_SESSION['user_picture']) ?>" alt="User Avatar" class="user-avatar">
                <div class="user-info">
                    <p class="user-name"><?= htmlspecialchars($_SESSION['user_name']) ?></p>
                    <dl class="user-details">
                        <div class="user-detail">
                            <dt>Gmail: <?= htmlspecialchars($_SESSION['user_email']) ?></dt>
                        </div>
                    </dl>
                </div>
            </div>


            <form method="POST" enctype="multipart/form-data">
                <table>
                    <tbody>
                        <?= $messageHtml ?>
                        <tr>
                            <td colspan="3" rowspan="4">
                                <img src="../public/images/system/logo.png" alt="CCSFP Logo" width="120">
                            </td>
                            <td colspan="16">
                                City College of San Fernando Pampanga
                            </td>
                            <td colspan="5">
                                CCSFP Admission Form 001
                            </td>
                        </tr>

                        <tr>
                            <td colspan="17">
                                City of San Fernando, Pampanga
                            </td>
                            <td colspan="4" rowspan="2">
                                Application No: 0000
                        </tr>

                        <tr>
                            <td colspan="18">
                                Email: citycollegesfp@gmail.com
                            </td>
                        </tr>

                        <tr>
                            <td colspan="16">
                                APPLICATION FORM FOR FIRST YEAR
                            </td>

                            <td colspan="5" rowspan="7" class="photo-td">
                                <div class="photo-wrapper">
                                    <!-- Clickable preview with overlay text -->
                                    <label for="photo" class="preview-container">
                                        <span class="overlay-text">
                                            Upload 1.5 x 1.5 Colored Picture<br>
                                            (white background with name tag)
                                        </span>
                                        <img id="preview" src="" alt="Image Preview" class="preview-img">

                                    </label>

                                    <!-- Hidden file input -->
                                    <input type="file" name="photo" id="photo" accept="image/*" onchange="previewImage(event)" class="file-input">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                INSTRUCTIONS
                            </td>
                            <td colspan="16">
                                A. Y. <?= $startYear ?>—<?= $endYear ?>
                            </td>

                        </tr>

                        <tr>
                            <td colspan="19">
                                1. Fill out all required information in this admission form.
                            </td>
                        </tr>

                        <tr>
                            <td colspan="20">
                                2. Print all entries legibly and only fully accomplished forms (CCSFP-Admission Form 001) will be processed.
                            </td>
                        </tr>

                        <tr>
                            <td colspan="8">
                                COURSE APPLIED FOR:
                            </td>
                            <td colspan="11"></td>
                        </tr>

                        <tr>
                            <td colspan="19">
                                <label for="course_first">1st Choice:</label>
                                <select name="course_first" id="course_first" class="form-select">
                                    <option value="">-- Select Course --</option>
                                    <option value="BSIT">Bachelor of Science in Information Technology (BSIT)</option>
                                    <option value="BSCS">Bachelor of Science in Computer Science (BSCS)</option>
                                    <option value="BSBA">Bachelor of Science in Business Administration (BSBA)</option>
                                    <option value="BSA">Bachelor of Science in Accountancy (BSA)</option>
                                    <option value="BSED">Bachelor of Secondary Education (BSED)</option>
                                    <option value="BEED">Bachelor of Elementary Education (BEED)</option>
                                    <option value="BSHM">Bachelor of Science in Hospitality Management (BSHM)</option>
                                    <option value="BSOA">Bachelor of Science in Office Administration (BSOA)</option>
                                    <option value="BSENT">Bachelor of Science in Entrepreneurship (BSENT)</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="19">
                                <label for="course_second">2nd Choice:</label>
                                <select name="course_second" id="course_second" class="form-select">
                                    <option value="">-- Select Course --</option>
                                    <option value="BSIT">Bachelor of Science in Information Technology (BSIT)</option>
                                    <option value="BSCS">Bachelor of Science in Computer Science (BSCS)</option>
                                    <option value="BSBA">Bachelor of Science in Business Administration (BSBA)</option>
                                    <option value="BSA">Bachelor of Science in Accountancy (BSA)</option>
                                    <option value="BSED">Bachelor of Secondary Education (BSED)</option>
                                    <option value="BEED">Bachelor of Elementary Education (BEED)</option>
                                    <option value="BSHM">Bachelor of Science in Hospitality Management (BSHM)</option>
                                    <option value="BSOA">Bachelor of Science in Office Administration (BSOA)</option>
                                    <option value="BSENT">Bachelor of Science in Entrepreneurship (BSENT)</option>
                                </select>
                            </td>
                        </tr>


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

                            <td colspan="2">3. Gender:</td>
                            <td colspan="3"><input type="text" name="gender" class="form-input" style="width:100%;"></td>

                            <td colspan="3">4. Date of Birth:</td>
                            <td colspan="11"><input type="date" name="dob" class="form-input" style="width:100%;"></td>
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
                            <td colspan="5"><input type="text" name="contact" class="form-input" style="width:100%;"></td>

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
                            <td colspan="2">12. Address:</td>
                            <td colspan="22"><input type="text" name="relative_address" class="form-input" style="width:100%;"></td>
                        </tr>


                        <!-- Section Title -->
                        <tr>
                            <td colspan="24" style="text-align:left; font-weight:bold;">II. SCHOLASTIC BACKGROUND</td>
                        </tr>

                        <!-- Row 13: College (Undergraduate) -->
                        <tr>
                            <td colspan="5">13. College (Undergraduate):</td>
                            <td colspan="10"><input type="text" name="college" class="form-input" style="width:100%;"></td>

                            <td colspan="2">Course:</td>
                            <td colspan="7"><input type="text" name="college_course" class="form-input" style="width:100%;"></td>
                        </tr>

                        <tr>
                            <td colspan="3">School Address:</td>
                            <td colspan="13"><input type="text" name="college_address" class="form-input" style="width:100%;"></td>

                            <td colspan="4">Last Sem/Year Attended:</td>
                            <td colspan="4"><input type="text" name="college_year" class="form-input" style="width:100%;"></td>
                        </tr>

                        <!-- Row 14: Senior High School (Grade 12) -->
                        <tr>
                            <td colspan="5">14. Senior High School (Grade 12):</td>
                            <td colspan="12"><input type="text" name="shs" class="form-input" style="width:100%;"></td>

                            <td colspan="3">Year Graduated:</td>
                            <td colspan="4"><input type="number" name="shs_year" class="form-input" style="width:100%;" min="1900" max="2099" step="1" placeholder="YYYY"></td>
                        </tr>

                        <tr>
                            <td colspan="3">School Address:</td>
                            <td colspan="14"><input type="text" name="shs_address" class="form-input" style="width:100%;"></td>

                            <td colspan="2">LRN #:</td>
                            <td colspan="5"><input type="text" name="shs_lrn" class="form-input" style="width:100%;"></td>
                        </tr>

                        <tr>
                            <td colspan="4">Awards/Honors Received:</td>
                            <td colspan="20"><input type="text" name="shs_awards" class="form-input" style="width:100%;"></td>
                        </tr>

                        <!-- Row 15: Junior High School (Grade 10) -->
                        <tr>
                            <td colspan="5">15. Junior High School (Grade 10):</td>
                            <td colspan="12"><input type="text" name="jhs" class="form-input" style="width:100%;"></td>

                            <td colspan="3">Year Graduated:</td>
                            <td colspan="4"><input type="number" name="jhs_year" class="form-input" style="width:100%;" min="1900" max="2099" step="1" placeholder="YYYY"></td>
                        </tr>

                        <tr>
                            <td colspan="3">School Address:</td>
                            <td colspan="21"><input type="text" name="jhs_address" class="form-input" style="width:100%;"></td>
                        </tr>

                        <tr>
                            <td colspan="4">Awards/Honors Received:</td>
                            <td colspan="20"><input type="text" name="jhs_awards" class="form-input" style="width:100%;"></td>
                        </tr>

                        <!-- Row 16: Primary -->
                        <tr>
                            <td colspan="2">16. Primary:</td>
                            <td colspan="15"><input type="text" name="primary" class="form-input" style="width:100%;"></td>

                            <td colspan="3">Year Graduated:</td>
                            <td colspan="4"><input type="number" name="primary_year" class="form-input" style="width:100%;" min="1900" max="2099" step="1" placeholder="YYYY"></td>
                        </tr>

                        <!-- Row 17: Special Skills/Talents -->
                        <tr>
                            <td colspan="4">17. Special Skills/Talents:</td>
                            <td colspan="20"><input type="text" name="skills" class="form-input" style="width:100%;"></td>
                        </tr>

                        <!-- Row 18: Interest Sports/Affiliations -->
                        <tr>
                            <td colspan="5">18. Interest Sports/Affiliations:</td>
                            <td colspan="19"><input type="text" name="sports" class="form-input" style="width:100%;"></td>
                        </tr>


                        <!-- Section Title -->
                        <tr>
                            <td colspan="24" style="text-align:left; font-weight:bold;">III. FAMILY BACKGROUND</td>
                        </tr>

                        <!-- Row 1: Father -->
                        <tr>
                            <td colspan="3">Father:</td>
                            <td colspan="6"><input type="text" name="father_name" class="form-input" style="width:100%;"></td>

                            <td colspan="2">Occupation:</td>
                            <td colspan="5"><input type="text" name="father_occupation" class="form-input" style="width:100%;"></td>

                            <td colspan="5">Employer/Employer’s Address:</td>
                            <td colspan="3"><input type="text" name="father_employer" class="form-input" style="width:100%;"></td>
                        </tr>

                        <!-- Row 2: Mother -->
                        <tr>
                            <td colspan="3">Mother:</td>
                            <td colspan="6"><input type="text" name="mother_name" class="form-input" style="width:100%;"></td>

                            <td colspan="2">Occupation:</td>
                            <td colspan="5"><input type="text" name="mother_occupation" class="form-input" style="width:100%;"></td>

                            <td colspan="5">Employer/Employer’s Address:</td>
                            <td colspan="3"><input type="text" name="mother_employer" class="form-input" style="width:100%;"></td>
                        </tr>

                        <!-- Row 3: Guardian -->
                        <tr>
                            <td colspan="3">Guardian:</td>
                            <td colspan="6"><input type="text" name="guardian_name" class="form-input" style="width:100%;"></td>

                            <td colspan="2">Occupation:</td>
                            <td colspan="5"><input type="text" name="guardian_occupation" class="form-input" style="width:100%;"></td>

                            <td colspan="5">Employer/Employer’s Address:</td>
                            <td colspan="3"><input type="text" name="guardian_employer" class="form-input" style="width:100%;"></td>
                        </tr>

                        <!-- Row 4: Guardian Address & Contact -->
                        <tr>
                            <td colspan="4">Guardian’s Address:</td>
                            <td colspan="12"><input type="text" name="guardian_address" class="form-input" style="width:100%;"></td>

                            <td colspan="3">Contact Nos.:</td>
                            <td colspan="5"><input type="text" name="guardian_contact" class="form-input" style="width:100%;"></td>
                        </tr>

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

                        <!-- Section V: How did you know about City College? -->
                        <tr>
                            <td colspan="24" style="text-align:left; font-weight:bold;">V. How did you know about City College? (Shade the circle of your answer)</td>
                        </tr>

                        <tr>
                            <td colspan="3">
                                <label><input type="radio" name="how_heard" value="tarpaulin"> Tarpaulin</label>
                            </td>
                            <td colspan="3">
                                <label><input type="radio" name="how_heard" value="word_of_mouth"> Word-of-mouth</label>
                            </td>
                            <td colspan="4">
                                <label><input type="radio" name="how_heard" value="internet_social_media"> Internet/Social Media</label>
                            </td>
                            <td colspan="14">
                                <label><input type="radio" name="how_heard" value="other"> Other (Please Specify):
                                    <input type="text" name="how_heard_other" class="form-input" style="width:100%;">
                                </label>
                            </td>
                        </tr>

                        <!-- Section Title -->
                        <tr>
                            <td colspan="24" style="text-align:left; font-weight:bold;">SIBLINGS</td>
                        </tr>

                        <!-- Example sibling row -->
                        <tr class="sibling-row">
                            <td colspan="3">Name:</td>
                            <td colspan="11"><input type="text" name="sibling_name[]" class="form-input" style="width:100%;"></td>

                            <td colspan="5">Educational Attainment:</td>
                            <td colspan="5"><input type="text" name="sibling_education[]" class="form-input" style="width:100%;"></td>
                        </tr>

                        <tr class="sibling-row">
                            <td colspan="7">Occupation/Employer/School Attending:</td>
                            <td colspan="17"><input type="text" name="sibling_occupation[]" class="form-input" style="width:100%;"></td>
                        </tr>

                        <tr>
                            <td colspan="24" style="text-align:left;">
                                <button type="button" class="btn-add" onclick="addSibling()">Add Sibling</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <button type="submit" class="btn-submit">Submit Application</button>
                </div>
            </form>
        </div>
    </main>

    <script src="../public/js/user.index.js"></script>
    <?php includeAndCache('../includes/footer.php'); ?>