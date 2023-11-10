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

$pdf->SetFont('Helvetica','',8);
$pdf->Cell(190,5,"User List",0,1,'C');

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
                    <th style="background-color: #888; color: #fff; font-weight: bold; border: 1px solid black;" scope="col">First Name</th>
                    <th style="background-color: #888; color: #fff; font-weight: bold; border: 1px solid black;" scope="col">Last Name</th>
                    <th style="background-color: #888; color: #fff; font-weight: bold; border: 1px solid black;" scope="col">Username</th>
                    <th style="background-color: #888; color: #fff; font-weight: bold; border: 1px solid black;" scope="col">Email</th>
                    <th style="background-color: #888; color: #fff; font-weight: bold; border: 1px solid black;" scope="col">Role</th>
                    <th style="background-color: #888; color: #fff; font-weight: bold; border: 1px solid black;" scope="col">Date Created</th>
                </tr>
            </thead>';
foreach ($users as $user) {
    $html .= '<tr>
                <td style="border: 1px solid black;">' . $user["uid"] . '</td>
                <td style="border: 1px solid black;">' . $user->firstName . '</td>
                <td style="border: 1px solid black;">' . $user->lastName . '</td>
                <td style="border: 1px solid black;">' . $user->username . '</td>
                <td style="border: 1px solid black;">' . $user->email . '</td>
                <td style="border: 1px solid black;">' . $user->role . '</td>
                <td style="border: 1px solid black;">' . $user->dateCreated . '</td>
              </tr>';
}
$html .= '</table>';

$pdf->writeHTML($html, true, false, true, false, '');

// ---------------------------------------------------------

// close and output PDF document
$pdf->Output('user_list.pdf', 'I');
?>