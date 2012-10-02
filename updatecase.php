<?php
    require_once "common.php";

    $datefmt = 'yyyy-MM-dd';
    
    /*
    // Define cases and set default cases    
    $casenames = array('gfs00Z','gfs12Z','ekf00Z','ekf12Z');
    $fieldnames = array('slpcrf','500_rh','wspd','streamline','comref','maxspd','maxsfc');

    $disdates = latestdir(DATADIR);   

    $types = array();
    $fields = array();
    foreach ($disdates as $datestr) {
      $types[$datestr] = arr2str(checkcases($datestr,$casenames));
      $fields[$datestr] = array();
      foreach ($casenames as $case) {
        $fields[$datestr][$case] = arr2str(checkfields($datestr,$case,$fieldnames));
      }
    }
    */

    if (!file_exists('case_types.php')) {
      $waitime = 4;   //how long in seconds to wait for case_types.php to be ready
      for ($i=0; $i<$waitime; $i++) {
        if (file_exists('case_types.php')) {
            break;
        }
        sleep(1);  // if not found wait one second before continue looping
      }
    }
    $fh = fopen('case_types.php','r'); flock($fh, LOCK_SH);
    require_once 'case_types.php';                   // Require pre-prepared file to save server execution
    flock($fh, LOCK_UN); fclose($fh);
    
    header("Content-Type: application/x-javascript");

    foreach($disdates as $datestr) {
        echo "datetypes['$datestr'] = $types[$datestr];\n";
    }

    foreach ($disdates as $datestr) {
        echo "datecasefields['$datestr'] = {";
        foreach ($casenames as $mycase) {
          echo "$mycase: {$fields[$datestr][$mycase]},";
        }
        echo "}\n";
    }
    
    echo "WdatePicker({eCont:'in_date',onpicked:dateChanged,startDate:config.currdate,
        dateFmt:'$datefmt',opposite:true,disabledDates:",arr2str($disdates),"});";
?>
