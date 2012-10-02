<?php
define("WORKDIR", dirname(__FILE__));
define("DATADIR", WORKDIR . "/data");

define("WEBBASEDIR", "/vol0/www/html/forecast");
define("MINFILESIZE", 1024);

// This is to generate date string from PHP array of date(s)
function arr2str($a) {
    $first = TRUE;
    $js = "[";
    foreach($a as $i) {
        if(!$first) {
            $js .= ",";
        }
        $js .= "'$i'";
        $first = FALSE;
    }
    $js .= "]";
    return $js;
}

// Convert PHP array to Javascript Array
function arr2js($a) {
    $first = TRUE;
    $js = "var filelist = new Array(";
    foreach($a as $i) {
        if(!$first) {
            $js .= ",";
        }
        $js .= "'$i'";
        $first = FALSE;
    }
    $js .= ");";
    return $js;
}

// Get all directory in an Array form
function latestdir($basedir) {
    $dirs=array();
    if(($fd = opendir($basedir))) {
        while(($fn = readdir($fd)) !== FALSE) {
            $absfn = $basedir . "/" . $fn;
            if(!is_dir($absfn)) continue;
            if(preg_match("/^([0-9]{4})([0-9]{2})([0-9]{2})$/", $fn, $m)) {
                $dirs[] = sprintf('%4d-%02d-%02d',$m[1], $m[2], $m[3]);
            }
        }
        closedir($fd);
    }
    sort($dirs);
    return $dirs;
}

// Get cases with each date
function checkcases($datestr,$cases) {
    $retarr = array();
    preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $datestr, $m);
    $basedir = sprintf('%s/%4d%02d%02d/',DATADIR,$m[1], $m[2], $m[3]);
    foreach ($cases as $case) {
        preg_match("/^(\w{3}\d{2}Z)$/",$case,$m);
        $dir = $basedir . $m[1];
        //print $dir;
        if (is_dir($dir)) {
            $retarr[] = $case;
        }
    }
    return $retarr;
}

// Get fields with respect to date & specific case
function checkfields($datestr,$case,$fields) {
    $retarr = array();
    preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $datestr, $m);
    $basedir = sprintf('%s/%4d%02d%02d/%s/',DATADIR,$m[1], $m[2], $m[3],$case);
    foreach ($fields as $field) {
        $file1 = $basedir . $field . '01.png';
        $file2 = $basedir . $field . '001.png';
        if (file_exists($file1) || file_exists($file2) ) {
            $retarr[] = $field;
        }
    }
    return $retarr;
}


function abs2rel($fn) {
    return str_replace(WEBBASEDIR, "", $fn);
}

function rel2abs($fn) {
    return WEBBASEDIR . "$fn";
}

function genfilelist($datadir, $regex) {
    $maxts = 0;
    $filelist = array();
    if(($fd = opendir($datadir))) {
        while(($fn = readdir($fd)) !== FALSE) {
            $absfn = $datadir . "/" . $fn;
            if(is_dir($absfn)) {
                continue;
            }
            if(preg_match($regex, $fn, $m) && filesize($absfn) > MINFILESIZE) {
                $ts = filemtime($absfn);
                if($ts && $ts > $maxts) {
                    $maxts = $ts;
                }
                $filelist[] = abs2rel($absfn);
            }
        }
        closedir($fd);
    }
    sort($filelist);
    return array($filelist, $maxts);
}

//Sanitize string
function sanitizeString($var) {
    $var = stripslashes($var);
    $var = htmlentities($var);
    $var = strip_tags($var);
    return $var;
}

function getNetFileLevels($filename, $level) {
    while ($level > 0) {
        preg_match("/^(.*data\/)(\d{8})\/(\w{3}\d{2}Z)\/(.*)$/",$filename,$m);
        //$newfile = str_replace('/data/',"/data$level/",$filename);
        $newfile = $m[1] . $m[2] . '/' . $m[3] . '_big' . '/' . $m[4];
        //print 'abc\n';
        //print "$filename<br>";
        //print "$newfile<br>";
        if (file_exists($newfile)) return array('same'=>false, 'name'=>$newfile);
        $level -= 1;
    }
    if ($level <= 0) return array('same'=>true, 'name'=>$filename);
}
?>
