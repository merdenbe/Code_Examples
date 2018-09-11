<?php
header('Content-type: application/json');
require_once("./db.php");
$response_array = array();
$response_array['status'] = 0;
# table is passed as a GET variable so we know which table to work with
if(isset($_GET['tbl']) && ($_GET['tbl'] === 'events' || $_GET['tbl'] === 'materials' || $_GET['tbl'] === 'parameters' || $_GET['tbl'] === 'jobs')) {
  $table = $_GET['tbl'];

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['laidigId']) && isset($_POST['description'])) {
      $lid = mysqli_real_escape_string($dbo, trim($_POST['laidigId']));
      $desc = mysqli_real_escape_string($dbo, $_POST['description']);
      if(isset($_POST['id']) && $_POST['id'] > -1) {
        $id = $_POST['id'];
        $query = $dbo->query("SELECT id FROM $table WHERE laidigId='$lid' AND id!=$id");
        if($query->num_rows > 0) {
          $response_array['status'] = 2;
        } else {
          $query = $dbo->query("SELECT * FROM $table WHERE id=$id");
          $row = $query->fetch_assoc();
          $response_array['status'] = $dbo->query("UPDATE $table SET laidigId='$lid', description='$desc' WHERE id=$id");
          if(strcmp($row['laidigId'], $lid) !== 0) {
            logEvent('Updated '.$table.' table. LaidigId from '.$row['laidigId'].' to '.$lid);
          }
          if(strcmp($row['description'], $desc) !== 0) {
            logEvent('Updated '.$table.' table. Description from '.$row['description'].' to '.$desc);
          }
        }  
      } else {
        $query = $dbo->query("SELECT id FROM $table WHERE laidigId='$lid'");
        if($query->num_rows > 0) {
          $response_array['status'] = 2;
        } else {
          $response_array['status'] = $dbo->query("INSERT INTO $table (laidigId, description) VALUES ('$lid', '$desc')");
          logEvent('Inserted '.$lid.' into '.$table.' table');
        }
      }
    }

  } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if(isset($_GET['id']) && $_GET['id'] > -1) {
      $id = $_GET['id'];
      $query = $dbo->query("SELECT laidigId FROM $table WHERE id=$id");
      $oldName = $query->fetch_assoc()['laidigId'];
      $response_array['status'] = $dbo->query("DELETE FROM $table WHERE id=$id");
      logEvent('Deleted '.$oldName.' from '.$table.' table');
    }

  } else {
    $data = array();
    $query;
    if(isset($_GET['id']) && $_GET['id'] > -1) {
      $id = $_GET['id'];
      $query = $dbo->query("SELECT * FROM $table WHERE id=$id");
    } else {
      $query = $dbo->query("SELECT * FROM $table");
    }
    if ($query) {
      $response_array['status'] = 1;
      while ($row = $query->fetch_assoc()){
        $data[] = $row;
      }
    }
    $response_array['data'] = $data;
  }
}
echo json_encode($response_array);
?>
