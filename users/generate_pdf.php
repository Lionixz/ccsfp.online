<?php
// C:\xampp\htdocs\ccsfp\admin\generate_pdf.php

// Start output buffering to avoid any accidental output
ob_start();

require_once '../config/db.php';
require_once '../vendor/autoload.php'; // Dompdf autoload

use Dompdf\Dompdf;
use Dompdf\Options;

// Get applicant ID from URL
$applicant_id = $_GET['id'] ?? null;
if (!$applicant_id) {
    die("No applicant ID provided.");
}

// Fetch applicant data
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

// Base URL for images (adjust if your site uses a different domain/path)
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$uploadBaseUrl = $baseUrl . '/ccsfp/public/images/uploads/';

// Build the HTML table exactly as in the view, but with minimal markup
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Applicant #' . htmlspecialchars($applicant['id']) . ' Details</title>
    <style>
        /* Print styles from openPrintWindow */
        @page {
            size: A4 portrait;
            margin: 10mm;
        }
        html, body {
            width: 210mm;
            min-height: 297mm;
            margin: 0;
            padding: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: #fff;
            color: #000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 10.5px;
            vertical-align: middle;
            word-wrap: break-word;
        }
        tr {
            page-break-inside: avoid;
        }
        img {
            max-width: 100%;
            height: auto;
        }
        input {
            border: none;
            width: 100%;
            font-size: 10.5px;
            color: #000;
            background: transparent;
        }
        .photo-wrapper {
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .photo-wrapper img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}
        .readonly-input {
            border: none;
            background: transparent;
            width: 100%;
        }
    </style>
</head>
<body>
    <table id="applicant-table">
        <tbody>
            <tr>
                <td colspan="5" rowspan="4">
                    <img src="' . $baseUrl . '/ccsfp/public/images/system/logo.png" alt="CCSFP Logo" width="120">
                </td>
                <td colspan="15">City College of San Fernando Pampanga</td>
                <td colspan="4">CCSFP Admission Form 001</td>
            </tr>
            <tr>
                <td colspan="15">City of San Fernando, Pampanga</td>
                <td colspan="4" rowspan="2">Application No: <br>' . htmlspecialchars($applicant['id']) . '</td>
            </tr>
            <tr>
                <td colspan="15">Email: citycollegesfp@gmail.com</td>
            </tr>
            <tr>
                <td colspan="15">APPLICATION FORM FOR FIRST YEAR</td>                
                <td colspan="4" rowspan="4" class="photo-td">
                    <div class="photo-wrapper">';

if (!empty($applicant['photo'])) {
    $photoUrl = $uploadBaseUrl . basename($applicant['photo']);
    $html .= '<img src="' . htmlspecialchars($photoUrl) . '" alt="Applicant Photo">';
} else {
    $html .= '<span>No Photo</span>';
}

$html .= '
                    </div>
                </td>
            </tr>

            <!-- Courses -->
            <tr>
                <td colspan="20">COURSE APPLIED FOR:</td>
            </tr>
            <tr>
                <td colspan="5">1st Choice:</td>
                <td colspan="15"><input class="readonly-input" value="' . htmlspecialchars($applicant['course_first']) . '" disabled></td>
            </tr>
            <tr>
                <td colspan="5">2nd Choice:</td>
                <td colspan="15"><input class="readonly-input" value="' . htmlspecialchars($applicant['course_second']) . '" disabled></td>
            </tr>

            <!-- I. PERSONAL INFO -->
            <tr>
                <td colspan="24">I. PERSONAL INFORMATION</td>
            </tr>
            <tr>
                <td colspan="3">Last Name:</td>
                <td colspan="5"><input class="readonly-input" value="' . htmlspecialchars($applicant['last_name']) . '" disabled></td>
                <td colspan="3">First Name:</td>
                <td colspan="5"><input class="readonly-input" value="' . htmlspecialchars($applicant['first_name']) . '" disabled></td>
                <td colspan="3">Middle Name:</td>
                <td colspan="5"><input class="readonly-input" value="' . htmlspecialchars($applicant['middle_name']) . '" disabled></td>
            </tr>
            <tr>
                <td colspan="3">Age:</td>
                <td colspan="5"><input class="readonly-input" value="' . htmlspecialchars($applicant['age']) . '" disabled></td>
                <td colspan="3">Gender:</td>
                <td colspan="5"><input class="readonly-input" value="' . htmlspecialchars($applicant['gender']) . '" disabled></td>
                <td colspan="3">Date of Birth:</td>
                <td colspan="5"><input class="readonly-input" value="' . htmlspecialchars($applicant['dob']) . '" disabled></td>
            </tr>
            <tr>
                <td colspan="4">Place of Birth:</td>
                <td colspan="12"><input class="readonly-input" value="' . htmlspecialchars($applicant['birth_place']) . '" disabled></td>
                <td colspan="4">Marital Status:</td>
                <td colspan="4"><input class="readonly-input" value="' . htmlspecialchars($applicant['marital_status']) . '" disabled></td>
            </tr>
            <tr>
                <td colspan="4">Contact Number/s:</td>
                <td colspan="5"><input class="readonly-input" value="' . htmlspecialchars($applicant['contact']) . '" disabled></td>
                <td colspan="2">Religion:</td>
                <td colspan="3"><input class="readonly-input" value="' . htmlspecialchars($applicant['religion']) . '" disabled></td>
                <td colspan="3">Email Address:</td>
                <td colspan="7"><input class="readonly-input" value="' . htmlspecialchars($applicant['email']) . '" disabled></td>
            </tr>
            <tr>
                <td colspan="6">Complete Home Address:</td>
                <td colspan="18"><input class="readonly-input" value="' . htmlspecialchars($applicant['home_address']) . '" disabled></td>
            </tr>
            <tr>
                <td colspan="6">Applicant is living with Relative:</td>
                <td colspan="18"><input class="readonly-input" value="' . htmlspecialchars($applicant['relative_name']) . '" disabled></td>
            </tr>
            <tr>
                <td colspan="6">Address:</td>
                <td colspan="18"><input class="readonly-input" value="' . htmlspecialchars($applicant['relative_address']) . '" disabled></td>
            </tr>

            <!-- II. SCHOLASTIC BACKGROUND -->
            <tr>
                <td colspan="24">II. SCHOLASTIC BACKGROUND</td>
            </tr>
            <tr>
                <td colspan="5">College (Undergraduate):</td>
                <td colspan="9"><input class="readonly-input" value="' . htmlspecialchars($applicant['college']) . '" disabled></td>
                <td colspan="2">Course:</td>
                <td colspan="8"><input class="readonly-input" value="' . htmlspecialchars($applicant['college_course']) . '" disabled></td>
            </tr>
            <tr>
                <td colspan="5">School Address:</td>
                <td colspan="9"><input class="readonly-input" value="' . htmlspecialchars($applicant['college_address']) . '" disabled></td>
                <td colspan="6">Last Sem/Year Attended:</td>
                <td colspan="4"><input class="readonly-input" value="' . htmlspecialchars($applicant['college_year']) . '" disabled></td>
            </tr>
            <tr>
                <td colspan="6">Senior High School (Grade 12):</td>
                <td colspan="9"><input class="readonly-input" value="' . htmlspecialchars($applicant['shs']) . '" disabled></td>
                <td colspan="4">Year Graduated:</td>
                <td colspan="5"><input class="readonly-input" value="' . htmlspecialchars($applicant['shs_year']) . '" disabled></td>
            </tr>
            <tr>
                <td colspan="4">School Address:</td>
                <td colspan="13"><input class="readonly-input" value="' . htmlspecialchars($applicant['shs_address']) . '" disabled></td>
                <td colspan="2">LRN #:</td>
                <td colspan="5"><input class="readonly-input" value="' . htmlspecialchars($applicant['shs_lrn']) . '" disabled></td>
            </tr>
            <tr>
                <td colspan="6">Awards/Honors Received:</td>
                <td colspan="18"><input class="readonly-input" value="' . htmlspecialchars($applicant['shs_awards']) . '" disabled></td>
            </tr>
            <tr>
                <td colspan="7">Junior High School (Grade 10):</td>
                <td colspan="9"><input class="readonly-input" value="' . htmlspecialchars($applicant['jhs']) . '" disabled></td>
                <td colspan="4">Year Graduated:</td>
                <td colspan="4"><input class="readonly-input" value="' . htmlspecialchars($applicant['jhs_year']) . '" disabled></td>
            </tr>
            <tr>
                <td colspan="4">School Address:</td>
                <td colspan="20"><input class="readonly-input" value="' . htmlspecialchars($applicant['jhs_address']) . '" disabled></td>
            </tr>
            <tr>
                <td colspan="6">Awards/Honors Received:</td>
                <td colspan="18"><input class="readonly-input" value="' . htmlspecialchars($applicant['jhs_awards']) . '" disabled></td>
            </tr>
            <tr>
                <td colspan="4">Primary:</td>
                <td colspan="12"><input class="readonly-input" value="' . htmlspecialchars($applicant['primary_school']) . '" disabled></td>
                <td colspan="4">Year Graduated:</td>
                <td colspan="4"><input class="readonly-input" value="' . htmlspecialchars($applicant['primary_year']) . '" disabled></td>
            </tr>
            <tr>
                <td colspan="5">Special Skills/Talents:</td>
                <td colspan="19"><input class="readonly-input" value="' . htmlspecialchars($applicant['skills']) . '" disabled></td>
            </tr>
            <tr>
                <td colspan="6">Interest Sports/Affiliations:</td>
                <td colspan="18"><input class="readonly-input" value="' . htmlspecialchars($applicant['sports']) . '" disabled></td>
            </tr>

            <!-- III. FAMILY BACKGROUND -->
            <tr>
                <td colspan="24">III. FAMILY BACKGROUND</td>
            </tr>
            <tr>
                <td colspan="3">Father:</td>
                <td colspan="4"><input class="readonly-input" value="' . htmlspecialchars($applicant['father_name']) . '" disabled></td>
                <td colspan="3">Occupation:</td>
                <td colspan="4"><input class="readonly-input" value="' . htmlspecialchars($applicant['father_occupation']) . '" disabled></td>
                <td colspan="6">Employer/Employer’s Address:</td>
                <td colspan="4"><input class="readonly-input" value="' . htmlspecialchars($applicant['father_employer']) . '" disabled></td>
            </tr>
            <tr>
                <td colspan="3">Mother:</td>
                <td colspan="4"><input class="readonly-input" value="' . htmlspecialchars($applicant['mother_name']) . '" disabled></td>
                <td colspan="3">Occupation:</td>
                <td colspan="4"><input class="readonly-input" value="' . htmlspecialchars($applicant['mother_occupation']) . '" disabled></td>
                <td colspan="6">Employer/Employer’s Address:</td>
                <td colspan="4"><input class="readonly-input" value="' . htmlspecialchars($applicant['mother_employer']) . '" disabled></td>
            </tr>
            <tr>
                <td colspan="3">Guardian:</td>
                <td colspan="4"><input class="readonly-input" value="' . htmlspecialchars($applicant['guardian_name']) . '" disabled></td>
                <td colspan="3">Occupation:</td>
                <td colspan="4"><input class="readonly-input" value="' . htmlspecialchars($applicant['guardian_occupation']) . '" disabled></td>
                <td colspan="6">Employer/Employer’s Address:</td>
                <td colspan="4"><input class="readonly-input" value="' . htmlspecialchars($applicant['guardian_employer']) . '" disabled></td>
            </tr>
            <tr>
                <td colspan="5">Guardian’s Address:</td>
                <td colspan="10"><input class="readonly-input" value="' . htmlspecialchars($applicant['guardian_address']) . '" disabled></td>
                <td colspan="3">Contact Nos.:</td>
                <td colspan="6"><input class="readonly-input" value="' . htmlspecialchars($applicant['guardian_contact']) . '" disabled></td>
            </tr>

            <!-- IV. MONTHLY FAMILY INCOME -->
            <tr>
                <td colspan="24">IV. MONTHLY FAMILY INCOME</td>
            </tr>
            <tr>
                <td colspan="24"><input class="readonly-input" value="' . htmlspecialchars($applicant['family_income']) . '" disabled></td>
            </tr>

            <!-- V. HOW DID YOU KNOW -->
            <tr>
                <td colspan="24">V. How did you know about City College?</td>
            </tr>
            <tr>
                <td colspan="24"><input class="readonly-input" value="' . htmlspecialchars($applicant['how_heard']) . '" disabled></td>
            </tr>

            <!-- SIBLINGS -->
            <tr>
                <td colspan="24">SIBLINGS</td>
            </tr>';

if (!empty($sibling_names)) {
    for ($i = 0; $i < count($sibling_names); $i++) {
        $html .= '
            <tr>
                <td colspan="3">Name:</td>
                <td colspan="9"><input class="readonly-input" value="' . htmlspecialchars($sibling_names[$i] ?? '') . '" disabled></td>
                <td colspan="6">Educational Attainment:</td>
                <td colspan="6"><input class="readonly-input" value="' . htmlspecialchars($sibling_educations[$i] ?? '') . '" disabled></td>
            </tr>
            <tr>
                <td colspan="10">Occupation / Employer / School Attending:</td>
                <td colspan="14"><input class="readonly-input" value="' . htmlspecialchars($sibling_occupations[$i] ?? '') . '" disabled></td>
            </tr>';
    }
} else {
    $html .= '
            <tr>
                <td colspan="24">No siblings information available</td>
            </tr>';
}

$html .= '
        </tbody>
    </table>
</body>
</html>';

// Configure Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true); // Allow loading images from URL

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Clear output buffer and send PDF
ob_end_clean();
$dompdf->stream("applicant_{$applicant['id']}.pdf", array("Attachment" => true));
exit;