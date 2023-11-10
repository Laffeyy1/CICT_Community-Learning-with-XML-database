<?php
session_start();
include("library/tcpdf.php");
$xml = simplexml_load_file("xml/users.xml");

if(isset($_SESSION['filter'])){
    $filter = $_SESSION['filter'];
    $start = $_SESSION['start_date'];
    $end = $_SESSION['end_date'];
}

// Filter the users based on the selected option or custom date range
$users = array();
foreach ($xml->user as $user) {
    $dateCreated = new DateTime($user->dateCreated);
    switch ($filter) {
        case "day":
            if ($dateCreated->format("Ymd") == (new DateTime())->format("Ymd")) {
                $users[] = $user;
            }
            break;
        case "week":
            $startOfWeek = (new DateTime())->modify('this week')->format("Ymd");
            $endOfWeek = (new DateTime())->modify('this week +6 days')->format("Ymd");
            if ($dateCreated->format("Ymd") >= $startOfWeek && $dateCreated->format("Ymd") <= $endOfWeek) {
                $users[] = $user;
            }
            break;
        case "month":
            if ($dateCreated->format("Ym") == (new DateTime())->format("Ym")) {
                $users[] = $user;
            }
            break;
        case "year":
            if ($dateCreated->format("Y") == (new DateTime())->format("Y")) {
                $users[] = $user;
            }
            break;
        case "custom":
            if ($dateCreated >= $start && $dateCreated <= $end) {
                $users[] = $user;
            }
            break;
        default:
            $users[] = $user;
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

$pdf->SetFont('Helvetica','',10);
$pdf->Cell(190,5,"Users Achievments List",0,1,'C');

$pdf->SetFont('Helvetica','',10);
$pdf->Cell(30,5,"",0);
$pdf->Cell(160,5,"",0);
$pdf->Ln();
$pdf->Cell(30,5,"",0);
$pdf->Cell(160,5,"",0);
$pdf->Ln();
$pdf->Ln(2);


// output the HTML content
$html = '<table border="1">
            <tr>
                <th style="border: 1px solid black;">User ID</th>
                <th style="border: 1px solid black;">Total Points</th>';

// Get the number of quizzes and assessments
$quiz_count = 0;
$assess_count = 0;
foreach ($xml->user as $user) {
    if ($user->role == "student") {
        foreach ($user->quiz as $quiz) {
            if ($quiz != "") {
                $quiz_count++;
            }
        }
        foreach ($user->assess as $assess) {
            if ($assess != "") {
                $assess_count++;
            }
        }
    }
}

// Add quiz columns to header row
for ($i = 1; $i <= $quiz_count; $i++) {
    $html .= '<th style="border: 1px solid black;">Quiz ' . $i . '</th>';
}

// Add assess column to header row
$html .= '<th style="border: 1px solid black;">Total Assess</th></tr>';

foreach ($xml->user as $user) {
    if ($user->role == "student") {
        $total_points = 0;
        $total_assess = 0;
        $quiz_scores = "";
        foreach ($user->quiz as $quiz) {
            $quiz_score = ($quiz != "") ? intval($quiz) : 0;
            $total_points += $quiz_score;
            $quiz_scores .= '<td style="border: 1px solid black; text-align: center; vertical-align: middle;">' . $quiz_score . '</td>';
        }
        
        foreach ($user->assess as $assess) {
            $assess_score = ($assess != "") ? intval($assess) : 0;
            $total_assess += $assess_score;
        }
        
        $html .= '<tr>
                    <td style="border: 1px solid black; text-align: center; vertical-align: middle;">' . $user["uid"] . '</td>
                    <td style="border: 1px solid black; text-align: center; vertical-align: middle;">' . $total_points . '</td>
                    ' . $quiz_scores . '
                    <td style="border: 1px solid black; text-align: center; vertical-align: middle;">' . $total_assess . '</td>
                  </tr>';
    }
}

$html .= '</table>';


$pdf->writeHTML($html, true, false, true, false, '');

// ---------------------------------------------------------

// close and output PDF document
$pdf->Output('user_list.pdf', 'I');
?>