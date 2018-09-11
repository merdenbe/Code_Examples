<?php
header('Content-type: application/json');
require_once("./db.php");
$response_array = array();
$response_array['status'] = 0;

  if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if(isset($_GET['id']) && $_GET['id'] > -1) {
      $id = $_GET['id'];
      $response_array['status'] = $dbo->query("DELETE FROM events_file WHERE id=$id");
      logEvent('Manually deleted from datasets table');
    }

  } else {
    $columns  = array('id', 'machineId', 'header', 'time_stamp', 'data');
    $data     = array();
    $query;
    $order  = $columns[$_GET['order'][0]['column']];
    $dir    = $_GET['order'][0]['dir'];
    $length = $_GET['length'];
    $start  = $_GET['start'];

    $query  = $dbo->query("SELECT events_file.id, events_file.machineId, events_file.header, events_file.time_stamp, events_file.data, file_header.header_line as header, machine_id.data as machineId FROM events_file LEFT JOIN machine_id ON events_file.machineId = machine_id.id LEFT JOIN file_header ON events_file.header=file_header.id ORDER BY $order $dir LIMIT $start, $length");

    if ($query) {
      $response_array['status'] = 1;
      while ($row = $query->fetch_assoc()){
        $converted_timestamp = (integer)$row['time_stamp']/1000;
        $row['time_stamp'] = trim(date('m/d/Y H:i:s', $converted_timestamp));
        $row['data'] = trim($row['data']);
        $data[] = $row;
      }
    }
    $result = $dbo->query("select count(1) FROM events_file");
    $total = $result->fetch_array()[0];
    $response_array['recordsTotal'] = $total;
    $response_array['recordsFiltered'] = $total;
    $response_array['data'] = $data;
  }

echo json_encode($response_array);
?>
