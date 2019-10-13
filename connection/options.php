<?php 
require_once(__DIR__ .'/../includes/autoload.php');
$status = $msg = $option = $response = '';
$type = $_POST['type'];
$data = $_POST['data'];
if (is_array($data)) {
	$data = $data;
} else {
	$data = json_decode($data);
}

$data = array('status' => $status, 'msg' => $msg, 'option' => $option, 'resp' => $response);
echo json_encode($data, JSON_UNESCAPED_SLASHES); 
