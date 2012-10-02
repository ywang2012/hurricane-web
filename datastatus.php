<?php

  $action = $_REQUEST['action'];

  $errorcode = "good";
  $errorinfo = "";

  $auth = false;

  if (in_array($action,array("add","update","delete")) ) {
    if ($_REQUEST['auth'] == "3") {
      $auth = true;
    } else {
      $errorcode = "bad";
      $errorinfo = "Not authorized for this operation!";
    }
  } else if ($action == "show") {
    $auth = true;
  } else {

    $errorcode = -1;
    $errorinfo = "Unknown action = $action.";

  }

  //if ( in_array($action,array("show","add","update","delete")) ) {
  if ($auth) {

    $hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
    $hostip   = $_SERVER['REMOTE_ADDR'];
    $datetime = date("Y-m-d G:i:s T");

    try {
      $db = new PDO("sqlite:database/datastatus.db"); //,'','', array(PDO::ATTR_PERSISTENT => true));
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $pe) {
      $errorcode = $pe->getCode();
      $errorinfo = $pe->getMessage();
    }

    header("content-type: application/x-javascript");

    $tableColumn = Array('GFS00Z','GFS12Z','EnKF00Z','EnKF12Z','NOTES');

    switch ($action) {

      case "show" :
        try {
          $query = $db->query("SELECT * FROM status ORDER BY date DESC");
          $query->setFetchMode(PDO::FETCH_ASSOC);

          $statusentries = array();

          while ( $row = $query->fetch() ) {
            $keydate = $row['Date'];
            $statusentries[$keydate] = array();
            foreach ( $row as $key => $value ) {
              if ($key != 'Date') {
                $statusentries[$keydate][$key] = $value;
                //printf("%-20s %s\n", $key, $value);
              }
            }
          }

          print_jsarray($statusentries);

          $query = $db->query("select datetime from updates order by id desc limit 1");
          while ( $row = $query->fetch() ) {
            $datetime = $row['datetime'];
          }


        } catch (PDOException $pe) {
          $errorcode = $pe->getCode();
          $errorinfo = $pe->getMessage();
        }
        break;

      case "add" :

        $insertcol = Array('Date');
        $valueset = Array( "'" . $_REQUEST['date'] . "'");

        foreach ($tableColumn as $col) {
          if ($_REQUEST[$col]) {
            $insertcol[] = $col;
            $valueset[] = "'" . $_REQUEST[$col] . "'";
          }
        }

        try {
          $sql = "INSERT INTO status (" . implode(",",$insertcol)
                 . ") VALUES (" . implode(",",$valueset) . ")";
          //$errorinfo = $sql;
          $query = $db->prepare($sql);
          $query->execute();

          // updated time table
          $sql = "INSERT INTO updates VALUES ( NULL, ?, ?, ?, ? )";
          $query = $db->prepare($sql);
          $query->execute(array($datetime,$hostname,$hostip, $action . " " . $_REQUEST['date']));

        } catch (PDOException $pe) {
          $errorcode = $pe->getCode();
          $errorinfo = $pe->getMessage();
        }
        break;

      case "update" :

        $updateset = array();
        $valueset  = array();

        foreach ($tableColumn as $col) {
          $updateset[] = "$col=?";
          if ($_REQUEST[$col]) {
            $valueset[] = $_REQUEST[$col] ;
          } else {
            $valueset[] = ' ';
          }
        }
        $valueset[] = $_REQUEST['date'];

        try {
          $sql = "UPDATE status SET " . implode(",",$updateset) . " WHERE DATE=?";
          $query = $db->prepare($sql);
          $query->execute($valueset);

          // updated time table
          $sql = "INSERT INTO updates VALUES ( NULL, ?, ?, ?, ? )";
          $query = $db->prepare($sql);
          $query->execute(array($datetime,$hostname,$hostip, $action . " " . $_REQUEST['date']));

        } catch (PDOException $pe) {
          $errorcode = $pe->getCode();
          $errorinfo = $pe->getMessage();
          //erro handling
        }

        break;

      case "delete" :

        $dates = explode(',',$_REQUEST['date']);

        foreach ($dates as $date) {
          try {
            $sql = "DELETE FROM status WHERE DATE=?";
            $query = $db->prepare($sql);
            $query->execute(array($date));

          } catch (PDOException $pe) {
            $errorcode = $pe->getCode();
            $errorinfo = $pe->getMessage();
            //erro handling
          }
        }

        // updated time table
        $sql = "INSERT INTO updates VALUES ( NULL, ?, ?, ?, ? )";
        $query = $db->prepare($sql);
        $query->execute(array($datetime,$hostname,$hostip, $action . " " . $_REQUEST['date'] ));

        break;

    }

    /*
     * do not forget to release all handles !
     */

    $db = null;

  }

  print "var errorcode = \"$errorcode\";\n" .
        "var errormessage = \"$errorinfo\";\n" .
        "var lastupdate = \"$datetime\";\n ";

  function print_jsarray($entries) {

    $datekeys = arr2js(array_keys($entries));
    print "var dateitems = $datekeys;\n";

    //$statusentries = ass2js($entries);
    print "var statusentries = {\n";

    $first = TRUE;
    foreach ($entries as $datekey => $entry) {
      if ($first) {
        print "      \"$datekey\" : ";
        $first = FALSE;
      } else {
        print "     ,\"$datekey\" : ";
      }
      print ass2js($entry) . "\n";
    }
    print "      };\n";

  }

  // Convert PHP array to Javascript Array
  function arr2js($a) {
      $first = TRUE;
      $js = "new Array(";
      foreach($a as $i) {
          if(!$first) {
              $js .= ",";
          }
          $js .= "\"$i\"";
          $first = FALSE;
      }
      $js .= ")";
      return $js;
  }

  // Convert PHP associated array to Javascript associated Array
  function ass2js($a) {

    $js = "{";
    $comma = False;
    foreach ($a as $key => $value) {

        if (! $value) $value = ' ';

        if ($comma) {
          $js .= ", ";
        } else {
          $comma = TRUE;
        }
        $js .= "\"$key\" : \"$value\"";
    }
    $js .= "}";

    return preg_replace('/\r?\n?/', '', nl2br($js));
  }

?>
