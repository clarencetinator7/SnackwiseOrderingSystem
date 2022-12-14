<?php
require_once dirname(__FILE__) . '/vendor/autoload.php';
require_once dirname(__FILE__) . '/php/classes/DbConnection.php';
require_once dirname(__FILE__) . '/php/classes/Validate.php';
require_once dirname(__FILE__) . '/php/classes/Closed_Date.php';

$validate = new Validate();
$closed_date = new Closed_Date();

$closed_date->delete_past_closed_date();
$db = new DbConnection();
$conn = $db->connect();

$filter = $_SESSION['report_filter'];
$currentDate = date("Y-m-d");

if($filter == "alltime") {
	$query = 'SELECT transaction_id, CONCAT(user.firstname, " ", user.lastname) AS fullname, order_id, date, total_price 
	FROM transaction 
	INNER JOIN user ON user.user_id = transaction.user_id
	ORDER BY transaction_id DESC';
	$date_span = "";
}
if($filter == "thisweek") {
	$query = 'SELECT transaction_id, CONCAT(user.firstname, " ", user.lastname) AS fullname, order_id, date, total_price 
	FROM transaction 
	INNER JOIN user ON user.user_id = transaction.user_id 
	WHERE WEEK(date) = WEEK("'.$currentDate.'") AND YEAR(date) = YEAR("'.$currentDate.'")
	ORDER BY transaction_id DESC';
	$date_span = "Weekly";
}
if($filter == "thismonth") {
	$query = 'SELECT transaction_id, CONCAT(user.firstname, " ", user.lastname) AS fullname, order_id, date, total_price 
	FROM transaction 
	INNER JOIN user ON user.user_id = transaction.user_id 
	WHERE MONTH(date) = MONTH("'.$currentDate.'") AND YEAR(date) = YEAR("'.$currentDate.'")
	ORDER BY transaction_id DESC';
	$date_span = "Monthly";
}
if($filter == "thisyear") {
	$query = 'SELECT transaction_id, CONCAT(user.firstname, " ", user.lastname) AS fullname, order_id, date, total_price 
	FROM transaction 
	INNER JOIN user ON user.user_id = transaction.user_id 
	WHERE YEAR(date) = YEAR("'.$currentDate.'")
	ORDER BY transaction_id DESC';
	$date_span = "Annual";
}



$counter = 0;
$sales = 0;
$html='';
$stmt  = $conn->prepare($query);
$result  =  $stmt->execute();

if ($stmt->rowCount() > 0) {
	$delay = 100;

	$html.='<body style="width:100%;">';
	$html.='<div style="text-align:center;">  <h1><span style="color: #DD1C1A;">SNACK</span><span
	style="color: #F0C808; text-align: center;">WISE</span></h1></div>';
	$html.='<div style="text-align:center;">'.$date_span.' Sales Report</div>';
	$html.='<div style="text-align:center;">Zone 3, Stall 1 Sto. Rosario Hagonoy Bulacan</div>';
	$html.='<br><br>';
	$html.='<div style="text-align:left;">Date Generated: '. date('F d, Y', strtotime($currentDate)).'</div>';
	$html.='<br>';

	$html.='<table class="table" style="width:100%;">';
	$html.='<tr><td>#</td> <td>Transaction ID</td> <td>Customer Name</td> <td>Order ID</td> <td>Date of Order</td> <td>Total Paid</td> </tr>';
	while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
		$counter++;
		$html.='<tr><td>'.$counter.'</td><td>'.$row['transaction_id'].'</td><td>'.$row['fullname'].'</td><td>'.$row['order_id'].'</td><td>'.$row['date'].'</td><td>'.$row['total_price'].'</td></tr>';
		$sales = $sales + floatval($row['total_price']); 
	}

	$html.='</table>';
	$html.='<br><br>';
	$html.='<div style="text-align:right;">Total Sales: PHP '. number_format($sales, 2, '.', ',').'</div>';
	$html.='<div style="text-align:right;">Total Transaction: '. $counter.'</div>';
	$html.='</body>';
}else{
	$html="No records found";
}


$mpdf=new \Mpdf\Mpdf();
$mpdf->WriteHTML($html);
$file='media/'.time().'.pdf';
$mpdf->output($file,'I');

?>