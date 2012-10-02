<?php
    require_once 'common.php';
    require dirname(WORKDIR) . "/Smarty/Smarty.class.php";

    // Create Smarty
    $smarty = new Smarty();
    $smarty->template_dir = WORKDIR . "/smarty/templates";
    $smarty->compile_dir  = WORKDIR . "/smarty/templates_c";
    $smarty->cache_dir    = WORKDIR . "/smarty/cache";
    $smarty->config_dir   = WORKDIR . "/smarty/configs";

    if (array_key_exists('action',$_GET)) {
    	$action = $_GET["action"];
    } else  {
    	$action = 'cal';
    }

    if (array_key_exists('date',$_GET)) {
      $indate = $_GET["date"];
      if ( preg_match("/^([0-9]{4})([0-9]{2})([0-9]{2})$/", $indate, $m) ) {
        $indate = sprintf('%4d-%02d-%02d',$m[1], $m[2], $m[3]);
      }
    } else {
      $indate = "";
    }

   /********************************************************************
    **
    ** Smarty specific variables for each web page
    **
    *******************************************************************/

    if ( $action == 'cal') {        /** datepicker page specific smarty variables **/

      // Available dates of forecast and set the least one as default
      $disdates = latestdir(DATADIR);
      if ($indate) {
      	$defdate = $indate;
      } else {
      	$defdate  = end($disdates);
      }

      // Define the dataPicker variables
      $datefmt = 'yy-mm-dd';
      $mindate = 'January 1, 2011';
      $maxdate = date("F j, Y, g:i a");

      /* Assign Smarty variables to be passed */
      $smarty->assign('enabledates',$disdates);
      $smarty->assign('defdate',$defdate);
      $smarty->assign('datefmt',$datefmt);
      $smarty->assign('mindate',$mindate);
      $smarty->assign('maxdate',$maxdate);

    } elseif ($action == 'prod') {  /** product page specific smarty variables **/

      // Define cases and fields
      $casenames = array('gfs00Z','gfs12Z','ekf00Z','ekf12Z');

      $fieldnames = array('slpcrf','wspd','streamline','500_rh','comref','maxspd','maxsfc');
      $fielddes = array();
      $fielddes['slpcrf']  = array('title' => 'MSLP, Sfc Wind & Composite Z',         'note' =>'');
      $fielddes['500_rh']  = array('title' => '500 hpa RH and Temperature',           'note' =>'');
      $fielddes['wspd']    = array('title' => 'Sfc Wind Speed and Vectors',           'note' =>'');
      $fielddes['streamline']  = array('title' => 'Sfc Reflectivity (Z) and Streamlines', 'note' =>'');
      $fielddes['comref']  = array('title' => 'High Frequnency Composite Z',          'note' =>'Overlayed with MSLP & sfc wind vector');
      $fielddes['maxspd']  = array('title' => 'Max Wind Speed below 2 km',            'note' =>'5-min animation with sfc wind vector');
      $fielddes['maxsfc']  = array('title' => 'Max Surface Wind Speed',               'note' =>'5-min animation with sfc wind vector');

      $types   = arr2str(checkcases($indate,$casenames));    // available cases
      $fields  = array();
      foreach ($casenames as $case) {
        $fields[$case] = arr2str(checkfields($indate,$case,$fieldnames));  // available fields for each case
      }

      // find default case and default field
      foreach ($fields as $key => $value) {
        if ($value !== "[]") {
          $defcase = $key;
          //$myfields = explode(",",$value,2);
          //$deffield = ereg_replace("[^A-Za-z0-9_]","",$myfields[0]);
          break;
        }
      }
      if (array_key_exists('case',$_GET)) {
      	$defcase = $_GET["case"];
      }

      $deffield='none';
      if (array_key_exists('field',$_GET)) {
      	$deffield = $_GET["field"];
      }

      /* Assign Smarty variables to be passed */
      $smarty->assign('casenames',$casenames);
      $smarty->assign('fieldnames',$fieldnames);
      $smarty->assign('fielddes',$fielddes);
      $smarty->assign('types', $types);
      $smarty->assign('fields',$fields);

      $smarty->assign('defdate',$indate);
      $smarty->assign('defcase',$defcase);
      $smarty->assign('deffield',$deffield);

    } elseif ($action == 'anim') {  /** animation page specific smarty variables **/

      $incase = $_GET["case"];
      //$incase = "gfs00Z";

      $infield = $_GET["field"];
      //$infield = "wspd";

      // Web template last modification time
      $webtem = 'smarty/templates/mobile_anim.tpl';
      if (file_exists($webtem)) {
          $ftime = date ("F d Y H:i:s.", filemtime($webtem));
      } else {
          $ftime = "September 11 2010 08:49:23";
      }

      /* Assign Smarty variables to be passed */
      //$smarty->assign('listurl','updateanim.php');
      $smarty->assign('defdate',$indate);
      $smarty->assign('defcase',$incase);
      $smarty->assign('deffield',$infield);
      $smarty->assign('webtime',$ftime);
    } else {
      echo "Wrong action!!!!!!";
      exit;
    }

   /********************************************************************
    **
    ** Now direct smarty to display the web page
    **
    *******************************************************************/

    $common_header = <<< EOH

    <meta name="viewport" content="user-scalable=yes, width=device-width" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />

    <title>CAPS Hurricane Forecast</title>

    <base href="http://forecast.ou.edu/ywang/hurricane2/" />

    <!-- jQuery UI datepicker, button, play panel etc. -->
    <link rel="stylesheet" type="text/css" href="scripts/jquery-ui-1.8.23/css/redmond/jquery-ui-1.8.23.custom.css" />

    <script type="text/javascript" src="scripts/jQuery.js"></script>
    <script type="text/javascript" src="scripts/jquery-ui-1.8.23/js/jquery-ui-1.8.23.custom.min.js"></script>

    <!-- iPhone stylesheet -->
    <link rel="stylesheet" type="text/css"  href="css/iphone.css" />
    <link rel="apple-touch-icon" sizes="114x114" href="images/CAPSHurricane.png">

EOH;

    $smarty->assign('common_header',$common_header);

    //$smarty->clear_all_cache();
    $smarty->caching = false;
    //$smarty->compile_check = true;
    //$smarty->debugging = true;
    //$smarty->force_compile = true;

    $smarty->display(WORKDIR . '/smarty/templates/mobile_' . $action . '.tpl');
?>
