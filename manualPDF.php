<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('../vendor/autoload.php'); //path to the autoload.php file 

if(isset($_POST['pdf_content'])){
    $htmlContent = $_POST['pdf_content'];
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator("HTML to PDF");
    $pdf->SetAuthor("PDF editor");
    $pdf->SetTitle("Manual PDF");

    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    $pdf->AddPage();
    $pdf->SetFont('dejavusans', '', 12); // Or another Unicode-compatible font
    $pdf->writeHTML($htmlContent, true, false, true, false, '');

    header("Content-Type: application/pdf");
    header("Content-Disposition: attachment; filename=\"manual.pdf\"");
    echo $pdf->Output('manual.pdf', 'S'); // Output the PDF as a string
    exit();
} else {
    http_response_code(400);
    echo json_encode(["error" => "No content provided"]);
    exit();
}
