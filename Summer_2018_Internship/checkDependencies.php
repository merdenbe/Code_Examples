<?php

  // Generate Set of Dependent Files
  $path = "./dependencies.txt";
  $fp   = fopen($path, "r");
  // Declare Empty Array
  $dependencies = array();
  if ($fp) {
    while (($line = fgets($fp)) !== false) {
      // Check for relevant line
      $js_StartIndex  = stripos($line, "src");
      $css_StartIndex = stripos($line, "href");

      // Cut out each file path
      if ($js_StartIndex !== false) {
        $dependencies[] = explode("\"", $line)[1];
      } else if ($css_StartIndex !== false) {
        $dependencies[] = explode("\"", $line)[3];
      }
    }
    // Convert to Set
    $dependencies = array_unique($dependencies);
    foreach ($dependencies as $d) {
      echo $d."<br><br>";
    }
    fclose($fp);
  } else {
    echo "Error: Could not open file <br>";
  }

?>
