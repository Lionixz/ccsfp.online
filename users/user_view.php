<?php
include('../middleware/checkSession.php');
include('../middleware/cache.php');
require_once '../config/db.php';

// 1. Get logged-in user ID
$users_id = $_SESSION['user_id'] ?? null;
if (!$users_id) {
    die("User not logged in.");
}

// 2. Fetch applicant data
$stmt = $conn->prepare("SELECT * FROM applicants WHERE users_id = ?");
$stmt->bind_param("s", $users_id);
$stmt->execute();
$result = $stmt->get_result();
$applicant = $result->fetch_assoc();

if (!$applicant) {
    die("No application found.");
}

// Decode siblings JSON
$sibling_names = json_decode($applicant['sibling_names'], true) ?? [];
$sibling_educations = json_decode($applicant['sibling_educations'], true) ?? [];
$sibling_occupations = json_decode($applicant['sibling_occupations'], true) ?? [];
?>

<!DOCTYPE html>
<html>
<?php includeAndCache('../includes/head.php'); ?>
<link rel="stylesheet" href="../public/css/user.view.css">

<style>

</style>

<body>
    <?php includeAndCache('../includes/sidebar.php'); ?>
    <main>
        <div class="container">
          
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
                <td colspan="4" rowspan="2">Application No: <?= $applicant['id'] ?></td>
            </tr>
            <tr>
                <td colspan="18">Email: citycollegesfp@gmail.com</td>
            </tr>
            <tr>
                <td colspan="16">APPLICATION FORM FOR FIRST YEAR</td>
                <td colspan="5" rowspan="7" class="photo-td">
                    <div class="photo-wrapper">
                        <?php if ($applicant['photo']): ?>
                            <img src="../public/images/uploads/<?= $applicant['photo'] ?>" alt="Applicant Photo">
                        <?php else: ?>
                            <span>No Photo</span>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>

            <!-- Courses -->
            <tr>
                <td colspan="8">COURSE APPLIED FOR:</td>
                <td colspan="11"></td>
            </tr>
            <tr>
                <td colspan="5">1st Choice:</td>
                <td colspan="15"><input type="text" class="readonly-input" value="<?= htmlspecialchars($applicant['course_first']) ?>"></td>
            </tr>
            <tr>
                <td colspan="5">2nd Choice:</td>
                <td colspan="15"><input type="text" class="readonly-input" value="<?= htmlspecialchars($applicant['course_second']) ?>"></td>
            </tr>

            <!-- I. PERSONAL INFO -->
            <tr>
                <td colspan="24" style="text-align:left; font-weight:bold;">I. PERSONAL INFORMATION</td>
            </tr>
            <tr>
                <td colspan="3">Last Name:</td>
                <td colspan="3"><input class="readonly-input" value="<?= htmlspecialchars($applicant['last_name']) ?>"></td>
                <td colspan="3">First Name:</td>
                <td colspan="3"><input class="readonly-input" value="<?= htmlspecialchars($applicant['first_name']) ?>"></td>
                <td colspan="3">Middle Name:</td>
                <td colspan="3"><input class="readonly-input" value="<?= htmlspecialchars($applicant['middle_name']) ?>"></td>
            </tr>
            <tr>
                <td colspan="2">Age:</td>
                <td colspan="3"><input class="readonly-input" value="<?= htmlspecialchars($applicant['age']) ?>"></td>
                <td colspan="3">Gender:</td>
                <td colspan="3"><input class="readonly-input" value="<?= htmlspecialchars($applicant['gender']) ?>"></td>
                <td colspan="4">Date of Birth:</td>
                <td colspan="9"><input class="readonly-input" value="<?= htmlspecialchars($applicant['dob']) ?>"></td>
            </tr>
            <tr>
                <td colspan="4">Place of Birth:</td>
                <td colspan="12"><input class="readonly-input" value="<?= htmlspecialchars($applicant['birth_place']) ?>"></td>
                <td colspan="4">Marital Status:</td>
                <td colspan="4"><input class="readonly-input" value="<?= htmlspecialchars($applicant['marital_status']) ?>"></td>
            </tr>
            <tr>
                <td colspan="4">Contact Number/s:</td>
                <td colspan="5"><input class="readonly-input" value="<?= htmlspecialchars($applicant['contact']) ?>"></td>
                <td colspan="2">Religion:</td>
                <td colspan="3"><input class="readonly-input" value="<?= htmlspecialchars($applicant['religion']) ?>"></td>
                <td colspan="3">Email Address:</td>
                <td colspan="7"><input class="readonly-input" value="<?= htmlspecialchars($applicant['email']) ?>"></td>
            </tr>
            <tr>
                <td colspan="6">Complete Home Address:</td>
                <td colspan="18"><input class="readonly-input" value="<?= htmlspecialchars($applicant['home_address']) ?>"></td>
            </tr>
            <tr>
                <td colspan="7">Applicant is living with Relative:</td>
                <td colspan="17"><input class="readonly-input" value="<?= htmlspecialchars($applicant['relative_name']) ?>"></td>
            </tr>
            <tr>
                <td colspan="3">Address:</td>
                <td colspan="21"><input class="readonly-input" value="<?= htmlspecialchars($applicant['relative_address']) ?>"></td>
            </tr>

            <!-- II. SCHOLASTIC BACKGROUND -->
            <tr>
                <td colspan="24" style="text-align:left; font-weight:bold;">II. SCHOLASTIC BACKGROUND</td>
            </tr>
            <tr>
                <td colspan="6">College (Undergraduate):</td>
                <td colspan="9"><input class="readonly-input" value="<?= htmlspecialchars($applicant['college']) ?>"></td>
                <td colspan="2">Course:</td>
                <td colspan="7"><input class="readonly-input" value="<?= htmlspecialchars($applicant['college_course']) ?>"></td>
            </tr>
            <tr>
                <td colspan="4">School Address:</td>
                <td colspan="10"><input class="readonly-input" value="<?= htmlspecialchars($applicant['college_address']) ?>"></td>
                <td colspan="6">Last Sem/Year Attended:</td>
                <td colspan="4"><input class="readonly-input" value="<?= htmlspecialchars($applicant['college_year']) ?>"></td>
            </tr>
            <tr>
                <td colspan="7">Senior High School (Grade 12):</td>
                <td colspan="9"><input class="readonly-input" value="<?= htmlspecialchars($applicant['shs']) ?>"></td>
                <td colspan="4">Year Graduated:</td>
                <td colspan="4"><input class="readonly-input" value="<?= htmlspecialchars($applicant['shs_year']) ?>"></td>
            </tr>
            <tr>
                <td colspan="4">School Address:</td>
                <td colspan="13"><input class="readonly-input" value="<?= htmlspecialchars($applicant['shs_address']) ?>"></td>
                <td colspan="2">LRN #:</td>
                <td colspan="5"><input class="readonly-input" value="<?= htmlspecialchars($applicant['shs_lrn']) ?>"></td>
            </tr>
            <tr>
                <td colspan="6">Awards/Honors Received:</td>
                <td colspan="18"><input class="readonly-input" value="<?= htmlspecialchars($applicant['shs_awards']) ?>"></td>
            </tr>
            <tr>
                <td colspan="7">Junior High School (Grade 10):</td>
                <td colspan="9"><input class="readonly-input" value="<?= htmlspecialchars($applicant['jhs']) ?>"></td>
                <td colspan="4">Year Graduated:</td>
                <td colspan="4"><input class="readonly-input" value="<?= htmlspecialchars($applicant['jhs_year']) ?>"></td>
            </tr>
            <tr>
                <td colspan="4">School Address:</td>
                <td colspan="20"><input class="readonly-input" value="<?= htmlspecialchars($applicant['jhs_address']) ?>"></td>
            </tr>
            <tr>
                <td colspan="6">Awards/Honors Received:</td>
                <td colspan="18"><input class="readonly-input" value="<?= htmlspecialchars($applicant['jhs_awards']) ?>"></td>
            </tr>
            <tr>
                <td colspan="3">Primary:</td>
                <td colspan="13"><input class="readonly-input" value="<?= htmlspecialchars($applicant['primary_school']) ?>"></td>
                <td colspan="4">Year Graduated:</td>
                <td colspan="4"><input class="readonly-input" value="<?= htmlspecialchars($applicant['primary_year']) ?>"></td>
            </tr>
            <tr>
                <td colspan="5">Special Skills/Talents:</td>
                <td colspan="19"><input class="readonly-input" value="<?= htmlspecialchars($applicant['skills']) ?>"></td>
            </tr>
            <tr>
                <td colspan="6">Interest Sports/Affiliations:</td>
                <td colspan="18"><input class="readonly-input" value="<?= htmlspecialchars($applicant['sports']) ?>"></td>
            </tr>

            <!-- III. FAMILY BACKGROUND -->
            <tr>
                <td colspan="24" style="text-align:left; font-weight:bold;">III. FAMILY BACKGROUND</td>
            </tr>
            <tr>
                <td colspan="3">Father:</td>
                <td colspan="4"><input class="readonly-input" value="<?= htmlspecialchars($applicant['father_name']) ?>"></td>
                <td colspan="3">Occupation:</td>
                <td colspan="3"><input class="readonly-input" value="<?= htmlspecialchars($applicant['father_occupation']) ?>"></td>
                <td colspan="7">Employer/Employer’s Address:</td>
                <td colspan="4"><input class="readonly-input" value="<?= htmlspecialchars($applicant['father_employer']) ?>"></td>
            </tr>
            <tr>
                <td colspan="3">Mother:</td>
                <td colspan="4"><input class="readonly-input" value="<?= htmlspecialchars($applicant['mother_name']) ?>"></td>
                <td colspan="3">Occupation:</td>
                <td colspan="3"><input class="readonly-input" value="<?= htmlspecialchars($applicant['mother_occupation']) ?>"></td>
                <td colspan="7">Employer/Employer’s Address:</td>
                <td colspan="4"><input class="readonly-input" value="<?= htmlspecialchars($applicant['mother_employer']) ?>"></td>
            </tr>
            <tr>
                <td colspan="3">Guardian:</td>
                <td colspan="4"><input class="readonly-input" value="<?= htmlspecialchars($applicant['guardian_name']) ?>"></td>
                <td colspan="3">Occupation:</td>
                <td colspan="3"><input class="readonly-input" value="<?= htmlspecialchars($applicant['guardian_occupation']) ?>"></td>
                <td colspan="7">Employer/Employer’s Address:</td>
                <td colspan="4"><input class="readonly-input" value="<?= htmlspecialchars($applicant['guardian_employer']) ?>"></td>
            </tr>
            <tr>
                <td colspan="5">Guardian’s Address:</td>
                <td colspan="10"><input class="readonly-input" value="<?= htmlspecialchars($applicant['guardian_address']) ?>"></td>
                <td colspan="3">Contact Nos.:</td>
                <td colspan="6"><input class="readonly-input" value="<?= htmlspecialchars($applicant['guardian_contact']) ?>"></td>
            </tr>

            <!-- IV. MONTHLY FAMILY INCOME -->
            <tr>
                <td colspan="24" style="text-align:left; font-weight:bold;">IV. MONTHLY FAMILY INCOME</td>
            </tr>
            <tr>
                <td colspan="24"><input class="readonly-input" value="<?= htmlspecialchars($applicant['family_income']) ?>"></td>
            </tr>

            <!-- V. HOW DID YOU KNOW -->
            <tr>
                <td colspan="24" style="text-align:left; font-weight:bold;">V. How did you know about City College?</td>
            </tr>
            <tr>
                <td colspan="24"><input class="readonly-input" value="<?= htmlspecialchars($applicant['how_heard']) ?>"></td>
            </tr>

            <!-- SIBLINGS -->
            <tr>
                <td colspan="24" style="text-align:left; font-weight:bold;">SIBLINGS</td>
            </tr>
            <?php for ($i = 0; $i < count($sibling_names); $i++): ?>
                <tr>
                    <td colspan="3">Name:</td>
                    <td colspan="9"><input class="readonly-input" value="<?= htmlspecialchars($sibling_names[$i]) ?>"></td>
                    <td colspan="6">Educational Attainment:</td>
                    <td colspan="6"><input class="readonly-input" value="<?= htmlspecialchars($sibling_educations[$i]) ?>"></td>
                </tr>
                <tr>
                    <td colspan="10">Occupation / Employer / School Attending:</td>
                    <td colspan="14"><input class="readonly-input" value="<?= htmlspecialchars($sibling_occupations[$i]) ?>"></td>
                </tr>
                 <tr>
    <td colspan="24" style="text-align:center;">
                                If you need to update the file, please ask for assistance.
                            </td>
                </tr>
                 
            <?php endfor; ?>
        </tbody>
    </table>

        
        </div>
    </main>
    <?php includeAndCache('../includes/footer.php'); ?>
</body>
</html>
