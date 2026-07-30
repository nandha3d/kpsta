<?php

$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetTitle('Members List');
$pdf->SetHeaderMargin(10);
$pdf->SetTopMargin(25);
$pdf->setFooterMargin(15);
$pdf->SetAutoPageBreak(true);
$pdf->SetAuthor('Author');
$pdf->SetDisplayMode('real', 'default');

$pdf->AddPage();


$pdf->SetFont('helvetica', '', 12);

$pdf->Ln();
$pdf->Cell(180, 5, 'Member\'s List', '', 1, 'C');
$pdf->Ln();

// column titles
$header = array('Name', 'Design.', 'Phone', 'School', 'Branch', 'A.S Subscriber');

// Colors, line width and bold font
$pdf->SetFillColor(249, 249, 249);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(0.1);
$pdf->SetFont('', ' ', '10');
// Header
$w = array(30, 30, 40, 32, 27, 31);
$num_headers = count($header);
for ($i = 0; $i < $num_headers; ++$i) {
    $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
}
$pdf->Ln();


// Color and font restoration
$pdf->SetFillColor(224, 235, 255);
$pdf->SetTextColor(0);
$pdf->SetFont('', '', '10');
// Data
$fill = 0;
foreach ($data as $key => $row) {
    $pdf->Cell($w[0], 6, $row['name'], 'LR', 0, 'L', $fill);
    $pdf->Cell($w[1], 6, $row['designation'], 'LR', 0, 'L', $fill);
    $pdf->Cell($w[2], 6, $row['mobile'], 'LR', 0, 'L', $fill);
    $pdf->Cell($w[3], 6, $row['school'], 'LR', 0, 'L', $fill);
    $pdf->Cell($w[4], 6, $row['branch'], 'LR', 0, 'L', $fill);
    if ($row['adhyapaka_sabdham_subscriber'] == 1) {
        $value = "Yes";
    } else {
        $value = "No";
    }
    $pdf->Cell($w[5], 6, $value, 'LR', 0, 'C', $fill);
    $pdf->Ln();
//    $fill = !$fill;
}
$pdf->Cell(array_sum($w), 0, '', 'T');


ob_end_flush();
$pdf->Output(PDF_PATH_TEMP . $fileName, 'F');  //save pdf
$pdf->Output($fileName, 'D');
