<?php
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  header('Content-type: application/json');
  require_once("./db.php");
  require_once("./globalMethods.php");
  ini_set('memory_limit', '-1');

  $response = array();

  /* START POST PROCESSING */
  $yAxes              = $_POST['yaxes'];
  $endDate            = time()*1000;
  $status             = false;
  $startDate          = 0;
  $event_index        = -1;
  $machineIds         = '';
  $header_IDs_string  = '';
  $header_IDs_array   = $_POST['headerIds'];

  if(isset($_POST['startDate']) && strlen($_POST['startDate']) > 1 && isset($_POST['endDate']) && strlen($_POST['endDate']) > 1) {
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
  }
  if(isset($_POST['machineIds'])) {
    $machineIds = join(",",$_POST['machineIds']);
  }
  if(isset($_POST['headerIds'])) {
    $header_IDs_string = join(",",$_POST['headerIds']);
  }

  // Get Column Index and Header_id for each y_axis
  $axisInfo = array();
  for ($i = 0; $i < count($yAxes); $i++) {
    $tmp    = array();
    $index  = $yAxes[$i];
    for ($j = 0; $j < count($header_IDs_array); $j++) {
      $result       = $dbo->query("SELECT header_line FROM file_header WHERE id='$header_IDs_array[$j]'");
      $header_line  = $result->fetch_assoc()['header_line'];
      $split_line   = explode(",", $header_line);
      $find         = array_search($yAxes[$i], $split_line);
      if ($find !== false) {
        $tmp['header_id'] = $header_IDs_array[$j];
        if (stripos($index, "event") !== FALSE) {
          $tmp['column']    = $find;
          $event_index      = $i;
        } else {
          $tmp['column']    = ($find-1); // date is removed from data line
        }
        break;
      }
    }
    $axisInfo[$index] = $tmp;
  }
  /* END POST PROCESSING */

  // Get Laidig Descriptions
  $axesDescriptions = array();
  for ($i = 0; $i < count($yAxes); $i++) {
    if ($i != $event_index) {
      $axesDescriptions[] = getParameterDescription($yAxes[$i]);
    } else {
      $axesDescriptions[] = "Event";
    }
  }

  /* BEGIN Data Select */
  $DATA = array();
  for ($i = 0; $i < count($yAxes); $i++) {
    $DATA[$yAxes[$i]] = array();
  }
  // Get Numeric Data
  $qry = $dbo->query("SELECT header, time_stamp, data FROM datasets WHERE machineId IN ('$machineIds') AND header IN ('$header_IDs_string') AND time_stamp >= $startDate AND time_stamp <= $endDate");
  $response['dbug'] = "SELECT header, time_stamp, data FROM datasets WHERE machineId IN ('$machineIds') AND header IN ('$header_IDs_string') AND time_stamp >= $startDate AND time_stamp <= $endDate";

  if ($qry->num_rows > 0) {
    $status = true;

    while ($row = $qry->fetch_assoc()){
      foreach ($yAxes as $axis) {
        if ($axisInfo[$axis]['header_id'] == $row['header']) {
          $split_line     = explode(",", $row['data']);
          $datum          = $split_line[$axisInfo[$axis]['column']];
          $tuple          = array((float)$row['time_stamp'], (float)$datum);
          $DATA[$axis][]  = $tuple;
        }
      }
    }
  }
  // Get Event Data
  if ($event_index != -1) {
    $event_name = $yAxes[$event_index];
    $query = $dbo->query("SELECT * FROM events_file WHERE machineId IN ('$machineIds') AND  header IN ('$header_IDs_string') AND time_stamp >= $startDate AND time_stamp <= $endDate");
    if ($query->num_rows > 0) {
      $status = true;
      while ($row = $query->fetch_assoc()){
        $tuple = array("x" => (integer)$row['time_stamp'], "y" => 0, "event" => $row['data']);
        $DATA[$event_name][] = $tuple;
      }
    }
  }

  /* END Data Select */
  $response['series']       = $DATA;
  $response['status']       = $status;
  $response['eventIndex']   = $event_index;
  $response['axisInfo']     = $axisInfo;
  $response['descriptions'] = $axesDescriptions;

  echo json_encode($response);

?>
