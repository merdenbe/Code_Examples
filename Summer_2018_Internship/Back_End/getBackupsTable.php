
<?php

  header('Content-type: application/json');
  require_once('./db.php');

  $query;
  $data = array();
  $response_array = array();
  $response_array['status'] = 0;
  $columns = array('filename', 'status', 'date_uploaded', 'job_number');
  $order  = $columns[$_POST['order'][0]['column']];
  $dir    = $_POST['order'][0]['dir'];
  $length = $_POST['length'];
  $start  = $_POST['start'];
  $result = $dbo->query("select count(1) FROM backup_files");
  $total = $result->fetch_array()[0];
  $response_array['recordsTotal'] = $total;
  $response_array['recordsFiltered'] = $total;

  if($_POST['search']['value'] != '') {
    $needle = '%'.$_POST['search']['value'].'%';
    $query  = $dbo->query("SELECT backup_files.job_number, backup_files.date_range, backup_files.status, backup_files.filename, backup_files.backup_path, file_header.header_line as header, machine_id.data as machineId, backup_files.date_uploaded FROM backup_files LEFT JOIN machine_id ON backup_files.machine_id=machine_id.id LEFT JOIN file_header ON backup_files.header_id=file_header.id WHERE backup_files.job_number LIKE '$needle' OR backup_files.date_uploaded LIKE '$needle' OR backup_files.status LIKE '$needle' OR backup_files.filename LIKE '$needle' ORDER BY $order $dir");
    $response_array['recordsFiltered'] = $query->num_rows;
    $query  = $dbo->query("SELECT backup_files.job_number, backup_files.date_range, backup_files.status, backup_files.filename, backup_files.backup_path, file_header.header_line as header, machine_id.data as machineId, backup_files.date_uploaded FROM backup_files LEFT JOIN machine_id ON backup_files.machine_id=machine_id.id LEFT JOIN file_header ON backup_files.header_id=file_header.id WHERE backup_files.job_number LIKE '$needle' OR backup_files.date_uploaded LIKE '$needle' OR backup_files.status LIKE '$needle' OR backup_files.filename LIKE '$needle' ORDER BY $order $dir LIMIT $start, $length");
  } else {
    $query  = $dbo->query("SELECT backup_files.job_number, backup_files.date_range, backup_files.status, backup_files.filename, backup_files.backup_path, file_header.header_line as header, machine_id.data as machineId, backup_files.date_uploaded FROM backup_files LEFT JOIN machine_id ON backup_files.machine_id=machine_id.id LEFT JOIN file_header ON backup_files.header_id=file_header.id ORDER BY $order $dir LIMIT $start, $length");
  }

  if ($query) {
    $response_array['status'] = 1;
    while ($row = $query->fetch_assoc()){
      if(is_numeric($row['status'])) {
        $row['status'] = $row['status']."% done";
      }
      $data[] = $row;
    }
  }

  $response_array['data'] = $data;
  echo json_encode($response_array);

?>
