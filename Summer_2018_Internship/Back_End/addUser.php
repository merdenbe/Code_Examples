<?php
require_once("./db.php");

  if(isset($_POST['username']) && isset($_POST['role']) && isset($_POST['password'])) {
    $name     = mysqli_real_escape_string($dbo, trim($_POST['username']));
    $password = mysqli_real_escape_string($dbo, trim($_POST['password']));
    $role     = $_POST['role'];

    if(isset($_POST['userId']) && $_POST['userId'] > -1) {
      $uid      = $_POST['userId'];
      $query    = $dbo->query("SELECT * FROM users WHERE id=$uid");
      $row      = $query->fetch_assoc();
      $oldName  = $row['name'];
      $oldRole  = $row['role'];
      $oldPassword = $row['password'];
      if ($name != $oldName) {
        $query = $dbo->query("SELECT id FROM users WHERE name='$name' AND id!=$uid");
        if($query->num_rows > 0) {
          $rslt = 2;
        } else {
          $rslt = $dbo->query("UPDATE users SET name='$name' WHERE id=$uid");
          logEvent('Updated name of user from '.$oldName.' to '.$name);
        }
      }
      if ($role != $oldRole) {
        $rslt = $dbo->query("UPDATE users SET role='$role' WHERE id=$uid");
        logEvent('Updated role of '. $name . ' from '.$oldRole.' to '.$role);
      }
      if ($password != $oldPassword) {
        $rslt = $dbo->query("UPDATE users SET password='$password' WHERE id=$uid");
      }
    } else {
      $query = $dbo->query("SELECT id FROM users WHERE name='$name'");
      if($query->num_rows > 0) {
        $rslt = 2;
      } else {
        $rslt = $dbo->query("INSERT INTO users (name, role, password) VALUES ('$name', '$role', '$password')");
        logEvent('Added user '.$name." as role: ".$role);
      }
    }
  } else {
    $rslt = 0;
}

echo $rslt;
?>
