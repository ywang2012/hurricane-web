<?php
    require_once 'common.php';
    require dirname(WORKDIR) . "/Smarty/Smarty.class.php";

    $view = $_GET['view'];
    if ( $view != "normal" ) {
      // Detect Agent
      $iphone  = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
      $android = strpos($_SERVER['HTTP_USER_AGENT'],"Android");
      $palmpre = strpos($_SERVER['HTTP_USER_AGENT'],"webOS");
      $berry   = strpos($_SERVER['HTTP_USER_AGENT'],"BlackBerry");
      $ipod    = strpos($_SERVER['HTTP_USER_AGENT'],"iPod");

      $querystring = "";
      // order is important
      if (array_key_exists('date',  $_GET)) $querystring .= "/${_GET['date']}"; 
      if (array_key_exists('case',  $_GET)) $querystring .= "/${_GET['case']}";
      if (array_key_exists('field', $_GET)) $querystring .= "/${_GET['field']}";
      
      if (($iphone || $android || $palmpre || $ipod || $berry == true ) || view == "mobile")  {
        header("Location: http://forecast.ou.edu/ywang/hurricane2/mobile.php$querystring");
        exit;
      }
    }

    /*
    $iphone = false;
    if ($_GET['agent'] == 'iphone' || $argv[1] == 'iphone') {
        $iphone = true;
    } else {
        if (preg_match("/iPhone/",$_SERVER["HTTP_USER_AGENT"]))
            $iphone = true;
    }
    */

    // Create Smarty
    $smarty = new Smarty();
    $smarty->template_dir = WORKDIR . "/smarty/templates";
    $smarty->compile_dir = WORKDIR . "/smarty/templates_c";
    $smarty->cache_dir = WORKDIR . "/smarty/cache";
    $smarty->config_dir = WORKDIR . "/smarty/configs";

    // Available cases by dates and set the least one as default
    //$disdates = latestdir(DATADIR);

    /*
    // Define cases and fields
    casenames = array('gfs00Z','gfs12Z','ekf00Z','ekf12Z');

    fieldnames = array('slpcrf','wspd','streamline','500_rh','comref','maxspd','maxsfc');
    fielddes = array();
    fielddes['500_rh']     = array('title' => '500 hpa RH and Temperature',           'note' =>'');
    fielddes['wspd']       = array('title' => 'Sfc Wind Speed and Vectors',           'note' =>'');
    fielddes['slpcrf']     = array('title' => 'MSLP, Sfc Wind & Composite Z',         'note' =>'');
    fielddes['streamline'] = array('title' => 'Sfc Reflectivity (Z) and Streamlines', 'note' =>'');
    fielddes['comref']     = array('title' => 'High Frequnency Composite Z',          'note' =>'Overlayed with MSLP & sfc wind vector');
    fielddes['maxspd']     = array('title' => 'Max Wind Speed below 2 km',            'note' =>'5-min animation with sfc wind vector');
    fielddes['maxsfc']     = array('title' => 'Max Surface Wind Speed',               'note' =>'5-min animation with sfc wind vector');

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
    require_once 'case_types.php';
    flock($fh, LOCK_UN); fclose($fh);

    // Define cases and set default cases
    $caselayout = array();
    $caselayout['gfs'] = array('00Z', '12Z' );
    $caselayout['ekf'] = array('00Z', '12Z' );
    $caseOutNames = array('gfs' => 'GFS', 'ekf' => 'EnKF');

    // DATE is passed in from URL
    if (array_key_exists('date',$_GET)) {
      $defdate = $_GET['date'];
      if (preg_match("/^([0-9]{4})([0-9]{2})([0-9]{2})$/", $defdate, $m)) {
        $defdate = sprintf('%4d-%02d-%02d',$m[1], $m[2], $m[3]);
      }
    } else {
      $defdate = 'none';
    }

    // CASE is passed in from URL
    if (array_key_exists('case',$_GET)) {
      $defcase = $_GET['case'];
    } else {
      $defcase = 'none';
    }

    // Field is passed in from URL
    $deffield = 'none';
    if (array_key_exists('field',$_GET)) {
      $deffield = $_GET['field'];
    } else {
      $deffield = 'none';
    }

    // Check for default date
    if ( ! in_array($defdate,$disdates) ) {
      $defdate = end($disdates);
    }

    // Check for default case
    if ( array_key_exists($defcase,$fields[$defdate]) && $fields[$defdate][$defcase] !== "[]" ) {

      $myfields = explode(",",$fields[$defdate][$defcase]);
      foreach ($myfields as &$field) {
        $field = ereg_replace("[^A-Za-z0-9_]","",$field);
      }

      // check for default field
      if ( ! in_array($deffield,$myfields ) ){
        $deffield = $myfields[0];
      }

    } else {  // Find default case and default field automatically

      foreach ($fields[$defdate] as $key => $value) {
        if ($value !== "[]") {
          $defcase = $key;
          $myfields = explode(",",$value,2);
          $deffield = ereg_replace("[^A-Za-z0-9_]","",$myfields[0]);
          break;
        }
      }
    }
    //$defcase = $casenames[0];
    //$deffield = $fieldnames[0];

/*
    // Refesh the page
    $currhour = date('G');
    $currday  = date('j');
    $currmon  = date('n');
    $curryear = date('Y');

    // automatic update on 12am, 6pm, 2pm, 12pm, 10am, 8am, 6am
    $updatehours = array(24,18,14,12,10,8,6,5,4,0);
    for($i=1;$i<count($updatehours);$i++) {
      if ($currhour >= $updatehours[$i]) {
          $refhour = $updatehours[$i-1];
          break;
      }
    }

    $refresh = mktime($refhour,00,00,$currmon,$currday,$curryear) - time();
    $refresh = ($refresh <= 0)?120:$refresh;

*/

    // Define the dataPicker variables
    $currdate = date('Y-m-d');
    $datefmt = 'yyyy-MM-dd';
    $mindate = '2010-04-23';
    $maxdate = $currdate;

    // Web template last modification time
    $webtem = 'smarty/templates/web.tpl';
    if (file_exists($webtem)) {
        $ftime = date ("F d Y H:i:s.", filemtime($webtem));
    } else {
        $ftime = "September 11 2010 08:49:23";
    }

    /* Assign Smarty variables to be passed */
    $smarty->assign('listurl','updateanim.php');
    $smarty->assign('enabledates',$disdates);
    $smarty->assign('casenames',$casenames);
    $smarty->assign('caselayout',$caselayout);
    $smarty->assign('caseOutNames',$caseOutNames);
    $smarty->assign('fieldnames',$fieldnames);
    $smarty->assign('fielddes',$fielddes);
    $smarty->assign('types',$types);
    $smarty->assign('fields',$fields);
    $smarty->assign('defcase',$defcase);
    $smarty->assign('deffield',$deffield);
    $smarty->assign('defdate',$defdate);
    //$smarty->assign('refresh',$refresh);
    $smarty->assign('datefmt',$datefmt);
    $smarty->assign('webtime',$ftime);

    //$smarty->clear_all_cache();
    $smarty->caching = false;
    //$smarty->compile_check = true;
    //$smarty->debugging = true;
    //$smarty->force_compile = true;

    $smarty->assign('orgwidth',900);
    $smarty->assign('orgheight',700);
    $smarty->assign('imgmargintop',-220);
    $smarty->assign('imgmarginleft',-80);
    $smarty->display(WORKDIR . "/smarty/templates/web.tpl");
?>
