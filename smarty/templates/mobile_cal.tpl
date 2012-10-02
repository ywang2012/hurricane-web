<!DOCTYPE html>
<html>
  <head>

    {$common_header}

    <script type="text/javascript">

      /* create an array of days which need to be disabled */
      var disabledDays = {arr2str array=$enabledates}

      /* utility functions */
      function enableDates(cdate) {ldelim}
        //var m = date.getMonth(), d = date.getDate(), y = date.getFullYear();
        //console.log('Checking (raw): ' + m + '-' + d + '-' + y);
        var dstr = $.datepicker.formatDate('yy-mm-dd',cdate);
        if($.inArray(dstr,disabledDays) != -1 ) {ldelim}
          return [true];
        {rdelim}
        return [false];
      {rdelim}

      /* a Date was picked */
      function showProducts(dateText,inst) {ldelim}
        //alert(dateText+" is picked!");
        var dstr = dateText.replace(/-/g,'');
        window.location.href = "mobile.php/prod/"+dstr+"/";
      {rdelim}

      /* DatePicker options setting here */
      $(function(){ldelim}

        // Datepicker
        $('#datepicker').datepicker({ldelim}
          inline: true,
          dateFormat: "{$datefmt}",
          minDate: new Date("{$mindate}"),
          maxDate: new Date("{$maxdate}"),
          selectOtherMonths: true,
          showOtherMonths : true,
          changeMonth : true,
          changeYear : true,
          defaultDate: "{$defdate}",
          constrainInput: true,
          beforeShowDay: enableDates,
          onSelect: showProducts
        {rdelim});
      {rdelim});

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