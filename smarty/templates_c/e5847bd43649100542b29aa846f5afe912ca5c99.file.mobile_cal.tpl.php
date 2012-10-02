<?php /* Smarty version 3.0rc1, created on 2012-10-02 10:06:26
         compiled from "/vol0/www/html/forecast/ywang/hurricane2/smarty/templates/mobile_cal.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1847468581506b02f2d44ca2-60749582%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e5847bd43649100542b29aa846f5afe912ca5c99' => 
    array (
      0 => '/vol0/www/html/forecast/ywang/hurricane2/smarty/templates/mobile_cal.tpl',
      1 => 1347894413,
    ),
  ),
  'nocache_hash' => '1847468581506b02f2d44ca2-60749582',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_arr2str')) include '/vol0/www/html/forecast/ywang/Smarty/plugins/function.arr2str.php';
?><!DOCTYPE html>
<html>
  <head>

    <?php echo $_smarty_tpl->getVariable('common_header')->value;?>


    <script type="text/javascript">

      /* create an array of days which need to be disabled */
      var disabledDays = <?php echo smarty_function_arr2str(array('array'=>$_smarty_tpl->getVariable('enabledates')->value),$_smarty_tpl->smarty,$_smarty_tpl);?>


      /* utility functions */
      function enableDates(cdate) {
        //var m = date.getMonth(), d = date.getDate(), y = date.getFullYear();
        //console.log('Checking (raw): ' + m + '-' + d + '-' + y);
        var dstr = $.datepicker.formatDate('yy-mm-dd',cdate);
        if($.inArray(dstr,disabledDays) != -1 ) {
          return [true];
        }
        return [false];
      }

      /* a Date was picked */
      function showProducts(dateText,inst) {
        //alert(dateText+" is picked!");
        var dstr = dateText.replace(/-/g,'');
        window.location.href = "mobile.php/prod/"+dstr+"/";
      }

      /* DatePicker options setting here */
      $(function(){

        // Datepicker
        $('#datepicker').datepicker({
          inline: true,
          dateFormat: "<?php echo $_smarty_tpl->getVariable('datefmt')->value;?>
",
          minDate: new Date("<?php echo $_smarty_tpl->getVariable('mindate')->value;?>
"),
          maxDate: new Date("<?php echo $_smarty_tpl->getVariable('maxdate')->value;?>
"),
          selectOtherMonths: true,
          showOtherMonths : true,
          changeMonth : true,
          changeYear : true,
          defaultDate: "<?php echo $_smarty_tpl->getVariable('defdate')->value;?>
",
          constrainInput: true,
          beforeShowDay: enableDates,
          onSelect: showProducts
        });
      });

      function normalview() {
      	window.location.href = "index.php?view=normal";
      }

      function toggleDisplay(elid) {
        if ( $("#"+elid).css("display") == "none" ) {
        	$("#"+elid).css("display","block");
        	$("#calendar").css("display","none");
        	$("#footer").css("display","none");
        } else {
          $("#"+elid).css("display","none") ;
          $("#calendar").css("display","block") ;
          $("#footer").css("display","block") ;
        }
        return false;
      }

    </script>
  </head>

  <body>
     <div id="header">
      <h1>CAPS Hurricane Forecast</h1>
      <a class="aboutbutton" href="javascript:toggleDisplay('aboutcontent')">About</a>
      <div id="aboutcontent" onclick="toggleDisplay('aboutcontent')">
        <center><span style="font-size:large;font-weight: bold; text-align:center;">4-km Realtime Forecasts for the Atlantic Hurricane Season</span><br/>
        <span style="font-size:12px;font-weight: bold; text-align:center;">Center for Analysis and Prediction of Storms</span><br/>
        <span style="font-size:medium;font-weight: bold; text-align:center;">University of Oklahoma</span><br/></center>

        <p>CAPS is producing twice daily (00 and 12 UTC) 48-hour-long realtime 4-km grid-spacing WRF ARW forecasts
        for the 2012 hurricane season over much of the Atlantics, initialized from GFS analysis, and from global
        EnKF analysis produced by Jeff Whitaker of ESRL. The forecasts are posted at
        <a href="http://www.caps.ou.edu/wx/p/" style="color:#1030EE;">http://www.caps.ou.edu/wx/p/</a> and enter through the daily calendar.</p>

        <p>
        This web page contains select forecast highlights with animations of Composite Reflectivity, Surface Winds and Mean Sea Level Pressure etc.</p>
      </div>
     </div>

     <div id="calendar" align="center" style="font-size: 110%;">
       <div id="datepicker"></div>
     </div>

     <div id="footer">
       <ul class="rounded">
           <li>Mobile View <span class="toggle"><form>
           	 <input type="checkbox" name="mobileview" onchange="normalview()" checked /></form></span>
           	</li>
       </ul>
     </div>

  </body>
</html>