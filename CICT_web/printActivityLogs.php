<?php
session_start();
include("library/tcpdf.php");
$xml = simplexml_load_file("xml/activity_logs.xml");

if(isset($_SESSION['filter'])){
    $filter = $_SESSION['filter'];
    $start = $_SESSION['start_date'];
    $end = $_SESSION['end_date'];
}

// Filter the users based on the selected option or custom date range
$logs = array();
foreach ($xml->activityLog as $log) {
    $dateCreated = new DateTime($log->date);
    switch ($filter) {
        case "day":
            if ($dateCreated->format("Ymd") == (new DateTime())->format("Ymd")) {
                $logs[] = $log;
            }
            break;
        case "week":
            $startOfWeek = (new DateTime())->modify('this week')->format("Ymd");
            $endOfWeek = (new DateTime())->modify('this week +6 days')->format("Ymd");
            if ($dateCreated->format("Ymd") >= $startOfWeek && $dateCreated->format("Ymd") <= $endOfWeek) {
                $logs[] = $log;
            }
            break;
        case "month":
            if ($dateCreated->format("Ym") == (new DateTime())->format("Ym")) {
                $logs[] = $log;
            }
            break;
        case "year":
            if ($dateCreated->format("Y") == (new DateTime())->format("Y")) {
                $logs[] = $log;
            }
            break;
        case "custom":
            if ($dateCreated >= $start && $dateCreated <= $end) {
                $logs[] = $log;
            }
            break;
        default:
            $logs[] = $log;
            break;
    }
}

$pdf = new TCPDF('H', 'mm', 'A4', true, 'UTF-8', false);


//remove default header and footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

//add page
$pdf->AddPage();

//add content (student list)
//title
$pdf->SetFont('Helvetica','',14);
$pdf->Cell(190,10,"CICT Learning Portal",0,1,'C');

$pdf->SetFont('Helvetica','',8);
$pdf->Cell(190,5,"Activity Logs",0,1,'C');

$pdf->SetFont('Helvetica','',10);
$pdf->Cell(30,5,"",0);
$pdf->Cell(160,5,"",0);
$pdf->Ln();
$pdf->Cell(30,5,"",0);
$pdf->Cell(160,5,"",0);
$pdf->Ln();
$pdf->Ln(2);


// output the HTML content
$html = '<table style="border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="background-color: #888; color: #fff; font-weight: bold; border: 1px solid black;" scope="col">#</th>
                    <th style="background-color: #888; color: #fff; font-weight: bold; border: 1px solid black;" scope="col">User</th>
                    <th style="background-color: #888; color: #fff; font-weight: bold; border: 1px solid black;" scope="col">Action</th>
                    <th style="background-color: #888; color: #fff; font-weight: bold; border: 1px solid black;" scope="col">Date</th>
                    <th style="background-color: #888; color: #fff; font-weight: bold; border: 1px solid black;" scope="col">Time</th>
                </tr>
            </thead>';
foreach ($logs as $log) {
    $html .= '<tr>
                <td style="border: 1px solid black;">' . $log["id"] . '</td>
                <td style="border: 1px solid black;">' . $log->username . '</td>
                <td style="border: 1px solid black;">' . $log->action . '</td>
                <td style="border: 1px solid black;">' . $log->date . '</td>
                <td style="border: 1px solid black;">' . $log->time . '</td>
              </tr>';
}
$html .= '</table>';

$pdf->writeHTML($html, true, false, true, false, '');

// ---------------------------------------------------------

// close and output PDF document
$pdf->Output('user_list.pdf', 'I');
?>