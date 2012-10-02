<?php
    require_once "common.php";

    $dir = NULL;
    $type = "s4c0";
    if(array_key_exists("date", $_REQUEST)) {
        if(preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", trim($_REQUEST["date"]),$m)) {
            $dir = "$m[1]$m[2]$m[3]";
        }
    }
    if(array_key_exists("type", $_REQUEST)) {
        if(preg_match("/^[_a-zA-Z0-9]+$/", trim($_REQUEST["type"]))) {
            $type = trim($_REQUEST["type"]);
            preg_match("/^(\w{3}\d{2}Z)(.*)$/",$type,$m);
            $case = $m[1];
            $field = $m[2];
        }
    }
    if(!$dir) {
      $dirs = latestdir(DATADIR);
      if(preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", end($dirs),$m)) {
            $dir = "$m[1]$m[2]$m[3]";
      }
    }

    $datadir = DATADIR . "/". $dir . "/" . "$case" ;
    $regex = "/^" . $field . "[0-9]{2,3}\.png$/";

    // Read in filelist from existing file to save server execution
    // It will lost automatically updating feature as genfilelist has.
    /*
    $listfile = $datadir . "/" . $field . ".list";
    if (file_exists($listfile)) {
        $fh = fopen($listfile,'r'); flock($fh,LOCK_SH);
        $filelist = array();
        while ( ($buffer = fgets($fh)) !== false ) {
            $filelist[] = trim($buffer);
        }        
        flock($fh,LOCK_UN); fclose($fh);
        $ts = filemtime($listfile);
    } else {    
      list($filelist, $ts) = genfilelist($datadir, $regex);       
    }
    */
    list($filelist, $ts) = genfilelist($datadir, $regex);       
    
    $filelistdata = arr2js($filelist);
    $filelistlen = count($filelist);
    $filelisthash = md5($filelistdata);

    header("Content-Type: application/x-javascript");
    echo "var filelisthash = '$filelisthash';\n";
    echo "var filelistlen = $filelistlen;\n";
    echo "var filelistts = $ts;\n";
    echo "$filelistdata\n";
?>
