<?php

namespace App\Libraries;

use TCPDF;

class Pdf extends TCPDF {

    public $viewName = '';

    /**
     * Declared explicitly: PHP 8.2 deprecates creating properties dynamically,
     * and Footer() assigns to this.
     *
     * @var \App\Controllers\BaseController|null
     */
    public $CI = NULL;

    /**
     * Forward the page setup through to TCPDF.
     *
     * The previous version accepted no arguments, so the format/orientation
     * passed by callers was silently dropped and TCPDF's defaults were used.
     * The defaults happened to match what callers passed, but the arguments
     * are honoured now so they behave as written.
     */
    public function __construct(
        $orientation = 'P',
        $unit = 'mm',
        $format = 'A4',
        $unicode = TRUE,
        $encoding = 'UTF-8',
        $diskcache = FALSE,
        $pdfa = FALSE
    ) {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
    }

    public function Header() {
        // if ($this->tocpage) or
        if ($this->page == 1) {

            $this->SetHeaderMargin(20);
//            $this->Cell(0, 15, 'First page header text', 0, false, 'C', 0, '', 0, false, 'M', 'M');
//            $this->Image(base_url('public/images/header_pdf.jpg'), 30, 5, 150, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
            $this->SetFont('helvetica', 'B', 14);
//            $this->SetTopMargin(20);
            // Set font
            $this->Cell(0, 1, 'KERALA PRADESH SCHOOL TEACHERS’ ASSOCIATION', 0, false, 'C', 0, '', 0, false, 'M', 'M');
            $this->Ln(6);
            $this->SetFont('helvetica', 'B', 11);
            $this->Cell(0, 30, 'Affiliated to AIPTF, AIFTO & Education International', 0, false, 'C', 0, '', 0, false, 'M', 'M');
            $this->SetFont('helvetica', 'B', 20);
            $this->Ln(6);


//            $this->setFooterMargin(20);
            // Title
        } else {
            $this->SetHeaderMargin(20);
            // *** replace the following parent::Header() with your code for other pages
            //parent::Header();
            // following will add your own logo ant text to other pages
//            $this->Image('http://localhost/other_pages_logo.png', 10, 10, 15, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
            $this->SetFont('helvetica', 'B', 14);
//            $this->Cell(0, 15, 'Other pages header text', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        }
        $this->SetFont('helvetica', ' ', 8);
        $this->Cell(160, 0, 'Date:', 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $this->Cell(172, 0, date('d-m-Y h:i A', time()), 0, false, 'L', 0, '', 0, false, 'M', 'M');
        $this->Ln(2);
        $this->writeHTML("<hr>", true, false, false, false, '');
        $this->SetFont('helvetica', '', 13);

        $bMargin = $this->getBreakMargin();
        $auto_page_break = $this->AutoPageBreak;
//        $this->Image(base_url('public/images/pdf_watermarker.png'), 30, 100, 100, 0, '', '', '', false, 300, '', false, false, 0);
        //Put the watermark
        $this->SetFont('helvetica', 'B', 75);
        $this->SetTextColor(224, 235, 255);
        $this->RotatedText(40, 140, 'K P S T A', 45);

        if ($this->viewName == "Approved") {
            $this->SetFont('helvetica', 'B', 18);
            $style5 = array('width' => 0.25, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(224, 235, 255));
            $this->Line(82, 188, 120, 188, $style5);
            $this->RotatedText(83, 190, 'APPROVED', 0);
            $this->Line(82, 200, 120, 200, $style5);
            $this->SetLineStyle($style5);
            $this->Circle(101, 195, 20);
        }

        // restore auto-page-break status
        $this->SetAutoPageBreak($auto_page_break, $bMargin);
        // set the starting point for the page content
        $this->setPageMark();
    }

    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        if ($this->viewName) {
            $this->Cell(0, 0, 'www.kpsta.in / kpsta.in@gmail.com [' . $this->viewName . ']', 0, false, 'C', 0, '', 0, false, 'T', 'M');
        } else {
            $this->Cell(0, 0, 'www.kpsta.in / kpsta.in@gmail.com', 0, false, 'C', 0, '', 0, false, 'T', 'M');
        }
        $this->Ln();
        $this->CI = & get_instance();
        $this->Cell(0, 0, $this->CI->session->userdata("groupName") . ' [' . $this->CI->session->userdata("officeName") . ']', 0, false, 'C', 0, '', 0, false, 'T', 'M');
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

    function RotatedText($x, $y, $txt, $angle) {
        //Text rotated around its origin
        $this->Rotate($angle, $x, $y);
        $this->Text($x, $y, $txt);
        $this->Rotate(0);
    }

}
