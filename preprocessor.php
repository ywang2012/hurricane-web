<?php
  require_once 'common.php';

  // Define cases and fields
  $casenames = array('gfs00Z','gfs12Z','ekf00Z','ekf12Z');

  $fieldnames = array('slpcrf','wspd','streamline','500_rh','comref','maxspd','maxsfc');
  $fielddes = array();
  $fielddes['500_rh']     = array('title' => '500 hpa RH and Temperature',           'note' =>'');
  $fielddes['wspd']       = array('title' => 'Sfc Wind Speed and Vectors',           'note' =>'');
  $fielddes['slpcrf']     = array('title' => 'MSLP, Sfc Wind & Composite Z',         'note' =>'');
  $fielddes['streamline'] = array('title' => 'Sfc Reflectivity (Z) and Streamlines', 'note' =>'');
  $fielddes['comref']     = array('title' => 'High Frequnency Composite Z',          'note' =>'Overlayed with MSLP & sfc wind vector');
  $fielddes['maxspd']     = array('title' => 'Max Wind Speed below 2 km',            'note' =>'5-min animation with sfc wind vector');
  $fielddes['maxsfc']     = array('title' => 'Max Surface Wind Speed',               'note' =>'5-min animation with sfc wind vector');
 
  // Available cases by dates and set the least one as default
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

  $fh = fopen('case_types.php','w+');
  flock($fh, LOCK_EX);
  
  fwrite($fh, "<?php\n");
  
  fwrite($fh, "\$casenames  = ". arr2str_php($casenames)  .";\n");
  fwrite($fh, "\$fieldnames = ". arr2str_php($fieldnames) .";\n");
  foreach ($fieldnames as $field) {
    fwrite($fh, "\$fielddes['$field'] = " . hash2str_php($fielddes[$field]) . ";\n" );    
  }
  
  fwrite($fh, "\$disdates = ". arr2str_php($disdates) .";\n");
  fwrite($fh, "\$types    = ". hash2str_php($types)   .";\n");
  foreach ($disdates as $datestr) {
    fwrite($fh, "\$fields['$datestr'] = " . hash2str_php($fields[$datestr]) . ";\n" );
  }
  
  fwrite($fh, "?>\n");
  
  flock($fh, LOCK_UN);
  fclose($fh);
  
  // This is to generate string of PHP array from PHP array data
  function arr2str_php($a) {
      $first = TRUE;
      $arrstr = "array(";
      foreach($a as $i) {
          if(!$first) {
              $arrstr .= ",";
          }
          $arrstr .= "'$i'";
          $first = FALSE;
      }
      $arrstr .= ")";
      return $arrstr;
  }
  
  // This is for definition string from PHP hash
  function hash2str_php($a) {
    $first = TRUE;
    $arrstr = "array(";
    foreach ($a as $key => $vals) {
      if (!$first) {
        $arrstr .= ",";
      }
      $arrstr .= "'$key' => \"$vals\"";
      $first = FALSE;
    }
    $arrstr .= ")";
    return $arrstr;
  }

?>