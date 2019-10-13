<?php 
require_once(__DIR__ .'/../includes/autoload.php');

  $results = $more_btn = $count = '';
 

  $data = array("result" => $results, "more" => $more_btn, "left" => $count);
  echo json_encode($data, JSON_UNESCAPED_SLASHES);

?>
