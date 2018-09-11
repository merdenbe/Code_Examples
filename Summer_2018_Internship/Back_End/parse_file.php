<?php

  function addToBackupFilesTable($filename) {
    global $dbo;
    $escapedFileName  = $dbo->real_escape_string($filename);
    $dbo->query("INSERT INTO backup_files (filename, backup_path, header_id, machine_id, job_number, status, date_range) VALUES ('$escapedFileName', 'N/A', '-1', '-1', '0', '25', 'N/A')");
    return $dbo->insert_id;
  }

  function backupFile($filePath) {
    $ds           = DIRECTORY_SEPARATOR;
    $backupLoc    = getSettingByName('Backup Location');
    $backupFilename = array_values(array_slice(explode($ds, $filePath), -1))[0];
    $targetPath   = $backupLoc.$ds.$backupFilename;
    $zip_file = preg_replace("/.CSV/", ".zip", $targetPath);
    $zip = new ZipArchive;
    $filename = array_values(array_slice(explode(DIRECTORY_SEPARATOR, $filePath), -1))[0];
    if ($zip->open($zip_file, ZipArchive::CREATE) === TRUE) {
      $zip->addFile($filePath, $filename);
      $zip->close();
      return $zip_file;
    } else {
      return false;
    }
  }

  function stripDateFromMachineIdAndFormatJob($machineIdString, &$fileType, $newJobNumber) {
    $splitMachineId = explode(',', $machineIdString);
    $fileType = trim($splitMachineId[0]);
    $splitMachineId[4] = sprintf('%03d', $splitMachineId[4]); //add leading 0s to serial numbers
    $splitMachineId[3] = $newJobNumber; //add J to start of Job #
    unset($splitMachineId[1]); // date column
    unset($splitMachineId[2]); // time column

    return implode(',', $splitMachineId);
  }

  function parseDataFile($relativeFilePath, $backupFileId, $filename) {
    global $dbo;
    global $dbc;
    $current_time   = time();
    $response       = array();
    $scaling        = array();
    $lineNum        = 3;
    $date_col       = -1;
    $time_col       = -1;
    $fileType       = '';
    $startDate      = '';
    $endDate        = '';

    // Open File and Get Total Number of Lines
    $progressPercentage = 25;
    $linecount          = 0;
    $handle             = fopen($relativeFilePath, "r");
    if (!$handle) { //file not found.  happens when canceled manually
      $response['message'] = "Error reading the file... Try again!";
      $status = $response['message'];
      $dbc->query("UPDATE backup_files SET status='$status' WHERE id=$backupFileId");
      $response['status'] = false;
      unlink($relativeFilePath);
      return $response;
    }
    while(!feof($handle)){
      $line = fgets($handle);
      $linecount++;
    }
    fclose($handle);
    $lineInterval = ceil($linecount/10);

    // Open File and Read Line by Line
    $fp = fopen($relativeFilePath, "r");

    if ($fp) {

      // Process MachineId and Header Lines
      $machineIdString  = trim(fgets($fp));
      $job_number       = explode(",", $machineIdString)[3];
      $header           = trim(fgets($fp));
      $column_names     = explode(',', $header);

      // Prepend J to job_number if not there
      if ($job_number[0] != 'J') {
        $job_number = "J".$job_number;
      }

      $machineId = 0;
      $machineIdString = stripDateFromMachineIdAndFormatJob($machineIdString, $fileType, $job_number);
      if ($fileType != "TD" && $fileType != "PS" && $fileType != "EF") {
        $response['message'] = "Error: Missing a valid file type in machine ID line";
        $response['status'] = false;
        fclose($fp);
        $backupPath = backupFile($relativeFilePath);
        $escapedBackupPath  = $dbo->real_escape_string($backupPath);
        unlink($relativeFilePath);
        $status = $response['message'];
        $dbc->query("UPDATE backup_files SET backup_path='$escapedBackupPath', status='$status' WHERE id=$backupFileId");
        return $response;
      }

      // Find Date Column
      for ($i = 0; $i < count($column_names); $i++) {
        if (stripos($column_names[$i], "date") !== false) {
          $date_col = $i;
        } else if (stripos($column_names[$i], "time") !== false) {
          $time_col = $i;
        }
        if (strlen($column_names[$i]) == 7 && $i != $date_col && $i != $time_col) {
          $scaling[] = pow(10, (int)$column_names[$i][6]);
        } else {
          $scaling[] = 1;
        }
      }
      if ($date_col == -1) {
        $response['message'] = "Error: Incorrect or no Header line";
        $response['status'] = false;
        $status = $response['message'];
        fclose($fp);
        $backupPath = backupFile($relativeFilePath);
        $escapedBackupPath  = $dbo->real_escape_string($backupPath);
        unlink($relativeFilePath);
        $dbc->query("UPDATE backup_files SET backup_path='$escapedBackupPath', status='$status', job_number='$job_number' WHERE id=$backupFileId");
        return $response;
      }
      $num_col = count($column_names)-1;
      unset($scaling[$date_col]);
      if ($time_col != -1) {
        unset($scaling[$time_col]);
      }
      $scaling = array_values($scaling);

      // Begin Transaction
      $dbo->begin_transaction();

      // Check For New Parameters
      $header_line = explode(",", $header);
      unset($header_line[$date_col]);
      if ($time_col != -1) {
        unset($header_line[$time_col]);
      }
      $header_line = array_values($header_line);
      foreach ($header_line as $parameter) {
        // Check Full Paramter
        if (!preg_match("/[A-Za-z]{1}[0-9]{5,6}/", $parameter) && stripos($parameter, 'event') === false) {
          $response['message'] = "Error in " . $filename . ": Parameter " . $parameter . " has the wrong form";
          $response['status'] = false;
          $dbo->rollback();
          $status = $response['message'];
          logEvent($status);
          $dbc->query("UPDATE backup_files SET status='$status' WHERE id=$backupFileId");
          return $response;
        };
        $param = substr($parameter, 0, 4);
        $result = $dbo->query("SELECT * FROM parameters WHERE laidigId='$param'");
        if ($result->num_rows == 0) {
          $escapedParam  = $dbo->real_escape_string($param);
          $dbo->query("INSERT INTO parameters (laidigId, description) VALUES ('$escapedParam', '')");
        }
      }

      // Insert into file_headers table
      $headerId = 0;
      if ($time_col != -1) {
        $column_names[$date_col] = "Date";
        unset($column_names[$time_col]);
        $column_names = array_values($column_names);
        $header = implode(",", $column_names);
      }
      $result = $dbo->query("SELECT id FROM file_header WHERE header_line='$header'");
      if ($result->num_rows == 0) {
        $result = $dbo->query("INSERT INTO file_header (header_line) VALUES ('$header')");
        $headerId = $dbo->insert_id;
      } else {
        $headerId = $result->fetch_row()[0];
      }
      // Insert into machine_id table
      $result = $dbo->query("SELECT * FROM machine_id WHERE data='$machineIdString'");
      if ($result->num_rows == 0) {
        $result = $dbo->query("INSERT INTO machine_id (data, file_headers) VALUES ('$machineIdString', '$headerId')");
        $machineId = $dbo->insert_id;
      } else {
        $row = $result->fetch_assoc();
        $machineId = $row['id'];
        $current_headers = $row['file_headers'];
        $split_headers = explode(",", $current_headers);
        $split_headers[] = $headerId;
        $split_headers = array_unique($split_headers);
        $updated_headers = implode(",", $split_headers);
        $dbo->query("UPDATE `machine_id` SET `file_headers`='$updated_headers' WHERE `machine_id`.`id`='$machineId'");
      }

      while (($line = fgets($fp)) !== false) {
        // Store Corrected Date
        $split_line = explode(',', $line);
        // Error Checking
        if (count($split_line)-1 != $num_col) {
          $response['message'] = "Error: Missing data on line " . $lineNum;
          $response['status'] = false;
          $dbo->rollback();
          fclose($fp);
          $backupPath = backupFile($relativeFilePath);
          $escapedBackupPath  = $dbo->real_escape_string($backupPath);
          unlink($relativeFilePath);
          $status = $response['message'];
          logEvent($status);
          $dbc->query("UPDATE backup_files SET backup_path='$escapedBackupPath', status='$status', job_number='$job_number' WHERE id=$backupFileId");
          return $response;
        }
        if ($time_col != -1) {
          $split_line[$date_col] = $split_line[$date_col]." ".$split_line[$time_col];
          unset($split_line[$time_col]);
          $split_line = array_values($split_line);
        }
        $fixed_date = $split_line[$date_col];
        unset($split_line[$date_col]);
        $split_line = array_values($split_line);
        if ($fileType !== 'EF') {
          for ($j = 0; $j < count($split_line); $j++) {
            $split_line[$j] = (float)$split_line[$j]/$scaling[$j];
          }
        }
        $reformedData = trim(implode(",", $split_line));
        $timestamp  = strtotime($fixed_date); // no reason to do preg_replace, which is expensive, unless strtotime fails
        if($timestamp === false || $timestamp < 100 || $timestamp > $current_time) {
          $fixed_date = preg_replace("/\/\s/", "/", $fixed_date);
          $fixed_date = preg_replace("/-/", "/", $fixed_date);
          $timestamp  = strtotime($fixed_date);
        }

        if ($timestamp === false || $timestamp < 100) {
          $response['message'] = "Error: Incorrect date syntax on line  " . $lineNum;
          $response['status'] = false;
          $dbo->rollback();
          fclose($fp);
          $backupPath = backupFile($relativeFilePath);
          $escapedBackupPath  = $dbo->real_escape_string($backupPath);
          unlink($relativeFilePath);
          $status = $response['message'];
          logEvent($status);
          $dbc->query("UPDATE backup_files SET backup_path='$escapedBackupPath', status='$status', job_number='$job_number' WHERE id=$backupFileId");
          return $response;
        } elseif($timestamp > $current_time) {
          $response['message'] = "Error: Data is from a future date on line " . $lineNum;
          $response['status'] = false;
          $dbo->rollback();
          fclose($fp);
          $backupPath = backupFile($relativeFilePath);
          $escapedBackupPath  = $dbo->real_escape_string($backupPath);
          unlink($relativeFilePath);
          $status = $response['message'];
          logEvent($status);
          $dbc->query("UPDATE backup_files SET backup_path='$escapedBackupPath', status='$status', job_number='$job_number' WHERE id=$backupFileId");
          return $response;
        }

        // For Backup File table
        if ($lineNum == 3) {
          $startDate = $fixed_date;
        } elseif ($lineNum >= $linecount-1) {
          $endDate = $fixed_date;
        }
        $timestamp *= 1000;
        // Insert Data
        if ($fileType === 'EF') {
          $dbo->query("INSERT IGNORE INTO events_file (machineId, header, time_stamp, data) VALUES ('$machineId', '$headerId', '$timestamp','$reformedData') ON DUPLICATE KEY UPDATE data='$reformedData'");
        } else {
          $dbo->query("INSERT INTO datasets (machineId, header, time_stamp, data) VALUES ('$machineId', '$headerId', '$timestamp','$reformedData') ON DUPLICATE KEY UPDATE data='$reformedData'");
        }

        // Output Current Progress
        if ($lineNum % $lineInterval == 0) {
          $progressPercentage = ceil((($lineNum/$linecount)*75)+25);
          $dbc->query("UPDATE backup_files SET status='$progressPercentage' WHERE id=$backupFileId");
        }
        $lineNum++;
      }
      // Commit Changes to DB
      $dbo->commit();
      fclose($fp);
    }
    else {
      $response['message'] = "Error reading the file and we really aren't sure why... Try again!";
      $status = $response['message'];
      $dbc->query("UPDATE backup_files SET status='$status' WHERE id=$backupFileId");
      $response['status'] = false;
      unlink($relativeFilePath);
      return $response;
    }

    $date_range   = trim($startDate) . " - " . trim($endDate);
    $backupPath = backupFile($relativeFilePath);
    $escapedBackupPath  = $dbo->real_escape_string($backupPath);
    if ($backupPath) {
      $status = "Successfully Processed";
      $response['message'] = "Uploaded successfully! File backed up to " . $backupPath;
      logEvent("Uploaded " . $backupPath . " Machine ID: " . $machineId . " Header ID: " . $headerId);
      $dbc->query("UPDATE backup_files SET backup_path='$escapedBackupPath', header_id='$headerId', machine_id='$machineId', job_number='$job_number', status='$status', date_range='$date_range' WHERE id=$backupFileId");
    } else {
      $response['message'] = "ERROR: file was processed but failed to backup file";
      $status = $response['message'];
      $dbc->query("UPDATE backup_files SET backup_path='$escapedBackupPath', header_id='$headerId', machine_id='$machineId', job_number='$job_number', status='$status', date_range='$date_range' WHERE id=$backupFileId");
    }
    unlink($relativeFilePath);
    $response['status'] = true;
    return $response;
  }

  //FOR TESTING
  //var_dump(parseDataFile('../uploads/EF_example.CSV'));
?>
