
<?php
include('../middleware/checkSession.php');
include_once('../middleware/cache.php');

require_once '../config/db.php';

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

$sibling_names = json_decode($applicant['sibling_names'], true) ?? [];
$sibling_educations = json_decode($applicant['sibling_educations'], true) ?? [];
$sibling_occupations = json_decode($applicant['sibling_occupations'], true) ?? [];

// ================================
// 5. Upload base URL for photos
// ================================
$uploadBaseUrl = '/ccsfp/public/images/uploads/';
?>

<!DOCTYPE html>
<html>
<?php includeAndCache('../includes/admin_head.php'); ?>
<link rel="stylesheet" href="../public/css/admin_view.css">

<body>
    <?php includeAndCache('../includes/admin_sidebar.php'); ?>
    <main>
        <div class="container">
    
</div>
    <table>
        <tbody>
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
                            <img src="<?= $uploadBaseUrl . basename($applicant['photo']) ?>" alt="Applicant Photo">
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
                <td colspan="15"><input type="text" class="readonly-input" value="<?= htmlspecialchars($applicant['course_first']) ?>" disabled></td>
            </tr>
            <tr>
                <td colspan="5">2nd Choice:</td>
                <td colspan="15"><input type="text" class="readonly-input" value="<?= htmlspecialchars($applicant['course_second']) ?>" disabled></td>
            </tr>

            <!-- I. PERSONAL INFO -->
            <tr>
                <td colspan="24" style="text-align:left; font-weight:bold;">I. PERSONAL INFORMATION</td>
            </tr>
            <tr>
                <td colspan="3">Last Name:</td>
                <td colspan="3"><input class="readonly-input" value="<?= htmlspecialchars($applicant['last_name']) ?>" disabled></td>
                <td colspan="3">First Name:</td>
                <td colspan="3"><input class="readonly-input" value="<?= htmlspecialchars($applicant['first_name']) ?>" disabled></td>
                <td colspan="3">Middle Name:</td>
                <td colspan="3"><input class="readonly-input" value="<?= htmlspecialchars($applicant['middle_name']) ?>" disabled></td>
            </tr>
            <tr>
                <td colspan="2">Age:</td>
                <td colspan="3"><input class="readonly-input" value="<?= htmlspecialchars($applicant['age']) ?>" disabled></td>
                <td colspan="3">Gender:</td>
                <td colspan="3"><input class="readonly-input" value="<?= htmlspecialchars($applicant['gender']) ?>" disabled></td>
                <td colspan="4">Date of Birth:</td>
                <td colspan="9"><input class="readonly-input" value="<?= htmlspecialchars($applicant['dob']) ?>" disabled></td>
            </tr>
            <tr>
                <td colspan="4">Place of Birth:</td>
                <td colspan="12"><input class="readonly-input" value="<?= htmlspecialchars($applicant['birth_place']) ?>" disabled></td>
                <td colspan="4">Marital Status:</td>
                <td colspan="4"><input class="readonly-input" value="<?= htmlspecialchars($applicant['marital_status']) ?>" disabled></td>
            </tr>
            <tr>
                <td colspan="4">Contact Number/s:</td>
                <td colspan="5"><input class="readonly-input" value="<?= htmlspecialchars($applicant['contact']) ?>" disabled></td>
                <td colspan="2">Religion:</td>
                <td colspan="3"><input class="readonly-input" value="<?= htmlspecialchars($applicant['religion']) ?>" disabled></td>
                <td colspan="3">Email Address:</td>
                <td colspan="7"><input class="readonly-input" value="<?= htmlspecialchars($applicant['email']) ?>" disabled></td>
            </tr>
            <tr>
                <td colspan="6">Complete Home Address:</td>
                <td colspan="18"><input class="readonly-input" value="<?= htmlspecialchars($applicant['home_address']) ?>" disabled></td>
            </tr>
            <tr>
                <td colspan="7">Applicant is living with Relative:</td>
                <td colspan="17"><input class="readonly-input" value="<?= htmlspecialchars($applicant['relative_name']) ?>" disabled></td>
            </tr>
            <tr>
                <td colspan="3">Address:</td>
                <td colspan="21"><input class="readonly-input" value="<?= htmlspecialchars($applicant['relative_address']) ?>" disabled></td>
            </tr>

            <!-- II. SCHOLASTIC BACKGROUND -->
            <tr>
                <td colspan="24" style="text-align:left; font-weight:bold;">II. SCHOLASTIC BACKGROUND</td>
            </tr>
            <tr>
                <td colspan="6">College (Undergraduate):</td>
                <td colspan="9"><input class="readonly-input" value="<?= htmlspecialchars($applicant['college']) ?>" disabled></td>
                <td colspan="2">Course:</td>
                <td colspan="7"><input class="readonly-input" value="<?= htmlspecialchars($applicant['college_course']) ?>" disabled></td>
            </tr>
            <tr>
                <td colspan="4">School Address:</td>
                <td colspan="10"><input class="readonly-input" value="<?= htmlspecialchars($applicant['college_address']) ?>" disabled></td>
                <td colspan="6">Last Sem/Year Attended:</td>
                <td colspan="4"><input class="readonly-input" value="<?= htmlspecialchars($applicant['college_year']) ?>" disabled></td>
            </tr>
            <tr>
                <td colspan="7">Senior High School (Grade 12):</td>
                <td colspan="9"><input class="readonly-input" value="<?= htmlspecialchars($applicant['shs']) ?>" disabled></td>
                <td colspan="4">Year Graduated:</td>
                <td colspan="4"><input class="readonly-input" value="<?= htmlspecialchars($applicant['shs_year']) ?>" disabled></td>
            </tr>
            <tr>
                <td colspan="4">School Address:</td>
                <td colspan="13"><input class="readonly-input" value="<?= htmlspecialchars($applicant['shs_address']) ?>" disabled></td>
                <td colspan="2">LRN #:</td>
                <td colspan="5"><input class="readonly-input" value="<?= htmlspecialchars($applicant['shs_lrn']) ?>" disabled></td>
            </tr>
            <tr>
                <td colspan="6">Awards/Honors Received:</td>
                <td colspan="18"><input class="readonly-input" value="<?= htmlspecialchars($applicant['shs_awards']) ?>" disabled></td>
            </tr>
            <tr>
                <td colspan="7">Junior High School (Grade 10):</td>
                <td colspan="9"><input class="readonly-input" value="<?= htmlspecialchars($applicant['jhs']) ?>" disabled></td>
                <td colspan="4">Year Graduated:</td>
                <td colspan="4"><input class="readonly-input" value="<?= htmlspecialchars($applicant['jhs_year']) ?>" disabled></td>
            </tr>
            <tr>
                <td colspan="4">School Address:</td>
                <td colspan="20"><input class="readonly-input" value="<?= htmlspecialchars($applicant['jhs_address']) ?>" disabled></td>
            </tr>
            <tr>
                <td colspan="6">Awards/Honors Received:</td>
                <td colspan="18"><input class="readonly-input" value="<?= htmlspecialchars($applicant['jhs_awards']) ?>" disabled></td>
            </tr>
            <tr>
                <td colspan="3">Primary:</td>
                <td colspan="13"><input class="readonly-input" value="<?= htmlspecialchars($applicant['primary_school']) ?>" disabled></td>
                <td colspan="4">Year Graduated:</td>
                <td colspan="4"><input class="readonly-input" value="<?= htmlspecialchars($applicant['primary_year']) ?>" disabled></td>
            </tr>
            <tr>
                <td colspan="5">Special Skills/Talents:</td>
                <td colspan="19"><input class="readonly-input" value="<?= htmlspecialchars($applicant['skills']) ?>" disabled></td>
            </tr>
            <tr>
                <td colspan="6">Interest Sports/Affiliations:</td>
                <td colspan="18"><input class="readonly-input" value="<?= htmlspecialchars($applicant['sports']) ?>" disabled></td>
            </tr>

            <!-- III. FAMILY BACKGROUND -->
            <tr>
                <td colspan="24" style="text-align:left; font-weight:bold;">III. FAMILY BACKGROUND</td>
            </tr>
            <tr>
                <td colspan="3">Father:</td>
                <td colspan="4"><input class="readonly-input" value="<?= htmlspecialchars($applicant['father_name']) ?>" disabled></td>
                <td colspan="3">Occupation:</td>
                <td colspan="3"><input class="readonly-input" value="<?= htmlspecialchars($applicant['father_occupation']) ?>" disabled></td>
                <td colspan="7">Employer/Employer’s Address:</td>
                <td colspan="4"><input class="readonly-input" value="<?= htmlspecialchars($applicant['father_employer']) ?>" disabled></td>
            </tr>
            <tr>
                <td colspan="3">Mother:</td>
                <td colspan="4"><input class="readonly-input" value="<?= htmlspecialchars($applicant['mother_name']) ?>" disabled></td>
                <td colspan="3">Occupation:</td>
                <td colspan="3"><input class="readonly-input" value="<?= htmlspecialchars($applicant['mother_occupation']) ?>" disabled></td>
                <td colspan="7">Employer/Employer’s Address:</td>
                <td colspan="4"><input class="readonly-input" value="<?= htmlspecialchars($applicant['mother_employer']) ?>" disabled></td>
            </tr>
            <tr>
                <td colspan="3">Guardian:</td>
                <td colspan="4"><input class="readonly-input" value="<?= htmlspecialchars($applicant['guardian_name']) ?>" disabled></td>
                <td colspan="3">Occupation:</td>
                <td colspan="3"><input class="readonly-input" value="<?= htmlspecialchars($applicant['guardian_occupation']) ?>" disabled></td>
                <td colspan="7">Employer/Employer’s Address:</td>
                <td colspan="4"><input class="readonly-input" value="<?= htmlspecialchars($applicant['guardian_employer']) ?>" disabled></td>
            </tr>
            <tr>
                <td colspan="5">Guardian’s Address:</td>
                <td colspan="10"><input class="readonly-input" value="<?= htmlspecialchars($applicant['guardian_address']) ?>" disabled></td>
                <td colspan="3">Contact Nos.:</td>
                <td colspan="6"><input class="readonly-input" value="<?= htmlspecialchars($applicant['guardian_contact']) ?>" disabled></td>
            </tr>

            <!-- IV. MONTHLY FAMILY INCOME -->
            <tr>
                <td colspan="24" style="text-align:left; font-weight:bold;">IV. MONTHLY FAMILY INCOME</td>
            </tr>
            <tr>
                <td colspan="24"><input class="readonly-input" value="<?= htmlspecialchars($applicant['family_income']) ?>" disabled></td>
            </tr>

            <!-- V. HOW DID YOU KNOW -->
            <tr>
                <td colspan="24" style="text-align:left; font-weight:bold;">V. How did you know about City College?</td>
            </tr>
            <tr>
                <td colspan="24"><input class="readonly-input" value="<?= htmlspecialchars($applicant['how_heard']) ?>" disabled></td>
            </tr>

            <!-- SIBLINGS -->
            <tr>
                <td colspan="24" style="text-align:left; font-weight:bold;">SIBLINGS</td>
            </tr>
            <?php for ($i = 0; $i < count($sibling_names); $i++): ?>
                <tr>
                    <td colspan="3">Name:</td>
                    <td colspan="9"><input class="readonly-input" value="<?= htmlspecialchars($sibling_names[$i]) ?>" disabled></td>
                    <td colspan="6">Educational Attainment:</td>
                    <td colspan="6"><input class="readonly-input" value="<?= htmlspecialchars($sibling_educations[$i]) ?>" disabled></td>
                </tr>
                <tr>
                    <td colspan="10">Occupation / Employer / School Attending:</td>
                    <td colspan="14"><input class="readonly-input" value="<?= htmlspecialchars($sibling_occupations[$i]) ?>" disabled></td>
                </tr>
                 <tr>
                        <td colspan="24" style="text-align:center;">
                            <a href="update.php?id=<?= $applicant['id'] ?>">update</a>
                            <a href="delete.php?id=<?= $applicant['id'] ?>" onclick="return confirm('Are you sure you want to delete this applicant?');">Delete</a>
                        </td>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>
        </div>
    </main>
    <?php includeAndCache('../includes/admin_footer.php'); ?>
</body>
</html>
