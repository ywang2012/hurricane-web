<?xml verison="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Cache-Control" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title>CAPS - Atlantic Hurricane</title>

    <base href="http://forecast.ou.edu/ywang/hurricane2/" />

    <link rel="stylesheet" type="text/css" media="all" href="css/main.css" />

    <link type="text/css" rel="StyleSheet" href="scripts/slider/css/luna/luna.css" />
    <script type="text/javascript" src="scripts/slider/js/range.js"></script>
    <script type="text/javascript" src="scripts/slider/js/timer.js"></script>
    <script type="text/javascript" src="scripts/slider/js/slider.js"></script>

    <script language="javascript" src="scripts/DatePicker/WdatePicker.js" ></script>
    <script language="javascript" src="scripts/controller.js"></script>
    <script language="javascript" src="scripts/zoombox.js"></script>
    <script language="javascript" src="scripts/updates.js"></script>
    <script language="javascript" src="scripts/animation.js"></script>
    <script language="javascript" src="scripts/static_image.js"></script>

    <style type="text/css">
        div.frames {
            height : {$orgheight}px;
            width  : {$orgwidth}px;
            overflow: hidden;
        }
        div.frames img {
             margin-top:{$imgmargintop}px;
             margin-left:{$imgmarginleft}px;
        }
    </style>
    <script language="javascript">
        var orgwidth = {$orgwidth};
        var orgheight = {$orgheight};
        var imgtopmg = {$imgmargintop};
        var imglftmg = {$imgmarginleft};

        var datetypes = new Array();
      {foreach from=$enabledates item=datestr}
        datetypes['{$datestr}'] = {$types[$datestr]};
      {/foreach}

        var datecasefields = new Array();
      {foreach from=$enabledates item=datestr}
        datecasefields['{$datestr}'] = {ldelim}
        {foreach from=$casenames item=mycase}
          {$mycase}:{$fields[$datestr][$mycase]},
        {/foreach}
        {rdelim};
      {/foreach}
        var casenames = {arr2str array=$casenames};
        var fieldnames = {arr2str array=$fieldnames};

        var fielddesc = new Array();
      {foreach from=$fieldnames item=myfield}
        fielddesc['{$myfield}'] = {ldelim}title:'{$fielddes[$myfield].title}',note:'{$fielddes[$myfield].note}'{rdelim};
      {/foreach}

       config.orgmargintop = "{$imgmargintop}px";
       config.orgmarginleft = "{$imgmarginleft}px";

       function normalview() {
       	 var dstr = config.currdate.replace(/-/g,'');
       	 window.location.href = "mobile.php/"+dstr+"/";
       }

    </script>
</head>
<body onLoad="initMain('img_frames',['status_frame','status_forecast'],'{$listurl}','{$defdate}','{$defcase}','{$deffield}',false)">
<div id="body_container" style="overflow:auto;height:850px;">
<!-- Top table -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" background="images/index_01.gif">
  <tr>
    <td align="left" width="200">
        <img src="images/capslogo.png" border="0" style="padding-left: 120px;" />
    </td>
    <td align="Center">
        <span class="style1">CAPS Forecasts for the Atlantic 2012 Hurricane Season</span>
        <img src="images/about.png" style="position:relative;top:5px;"
             alt="Information" title="Information" onclick="toggleProduct('aboutWindow')" /><br />
        <span class="style2">Animation Movies of Selected Products</span>
    </td>
    <td align="right" width="60">
        <img src="images/help.png" border="0" style="padding-right: 20px;"
             alt="Help" title="Help" onclick="toggleProduct('helpWindow')" />
    </td>
  </tr>
</table>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
    <td width="300" valign="top">
        <!-- Left table -->
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="14"><img src="images/left_left_top.gif" /></td>
            <td background="images/left_mid_top.gif"></td>
            <td width="9"><img src="images/left_right_top.gif" /></td>
          </tr>
          <tr >
            <td background="images/left_left_mid.gif"></td>
            <td height="5"></td>
            <td background="images/left_right_mid.gif"></td>
          </tr>
          <!-- Date Picker -->
          <tr>
            <td background="images/left_left_mid.gif"></td>
            <td align="center">
                <div id="in_date"></div>
                <script>
                WdatePicker({ldelim}eCont:'in_date',onpicked:dateChanged,startDate:'{$defdate}',
                dateFmt:'{$datefmt}',opposite:true,disabledDates:{arr2str array=$enabledates}{rdelim} );
                </script>
                <!--
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td>
                        <table width="70" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="7">
                                <img src="images/time_input_left.gif" width="7" height="28"></td>
                            <td background="images/time_input_mid.gif">
                                <input type="text" name="date" size="8" value="{$defdate}" maxlength="10" id="in_date" class="NFText">
                            </td>
                            <td width="26">
                                <img src="images/time_input_right.gif" width="26" height="28"
                                onclick="WdatePicker({ldelim} el:'in_date',onpicked:dateChanged,
                                dateFmt:'$datefmt',minDate:'$mindate',maxDate:'$maxdate',
                                opposite:true,disabledDates:$enabledates{rdelim} )">
                            </td>
                        </tr>
                        </table>
                    </td>
                </tr>
                </table>
                -->
            </td>
            <td background="images/left_right_mid.gif"></td>
          </tr>
          <tr >
            <td background="images/left_left_mid.gif"></td>
            <td height="10"></td>
            <td background="images/left_right_mid.gif"></td>
          </tr>
        {foreach from=$caselayout key=OutCase item=timeCase}
          <tr>
            <td background="images/left_left_mid.gif"></td>
            <td class="tdSeparator1">
                <img id="img_{$OutCase}" src="images/down_arrow.gif" border="0" onclick="toggleProduct('{$OutCase}')" />
                Initialized with {$caseOutNames[$OutCase]} data
                <div id="{$OutCase}" style="visibility:visible;">
                <table width="100%" border="0" cellspacing="5" cellpadding="0" bgcolor="#ffffff">
                <tr>
          {foreach from=$timeCase item=TimeInit}
                    <td bgcolor="#e0e0e0" align="center" width="30%" id="td_{$OutCase}{$TimeInit}">
                    <a href="javascript:caseChanged('{$OutCase}{$TimeInit}');">{$TimeInit}</a></td>
          {/foreach}
                </tr>
                </table>
                </div>
            </td>
            <td background="images/left_right_mid.gif"></td>
          </tr>
        {/foreach}
          <tr >
            <td background="images/left_left_mid.gif"></td>
            <td height="10"></td>
            <td background="images/left_right_mid.gif"></td>
          </tr>
          <tr>
            <td background="images/left_left_mid.gif"></td>
            <td background="images/pro_mid.gif">
                <img src="images/pro_left.gif" width="118" height="25" />
            </td>
            <td background="images/left_right_mid.gif"></td>
          </tr>
          <tr >
            <td background="images/left_left_mid.gif"></td>
            <td height="5"></td>
            <td background="images/left_right_mid.gif"></td>
          </tr>
        {foreach from=$fieldnames item=field}
          <tr>
            <td background="images/left_left_mid.gif"></td>
            <td height="20" align="left" id="td_{$field}">
                <img id="img_{$field}" src="images/left_pro_arrow.gif" border="0" />
                <a href="javascript:fieldChanged('{$field}');">{$fielddes[$field].title}</a><br/>
                {if $fielddes[$field].note ne ""} <span class="comment">({$fielddes[$field].note})</span> {/if}
            </td>
            <td background="images/left_right_mid.gif"></td>
          </tr>
        {/foreach}
          <tr>
            <td background="images/left_left_mid.gif"></td>
            <td height="20" align="left">&nbsp;
            </td>
            <td background="images/left_right_mid.gif"></td>
          </tr>
          <tr>
            <td background="images/left_left_mid.gif"></td>
            <td height="20" align="left">
                <img src="images/more.png" border="0" align="absmiddle" />
                <a href="javascript:goURL(0);" style="color:#0c99cc;">More Forecast Fields</a>
            </td>
            <td background="images/left_right_mid.gif"></td>
          </tr>
          <tr>
            <td background="images/left_left_mid.gif"></td>
            <td height="20" align="left">
                <img src="images/go_back_16.png" border="0" align="absmiddle" />&nbsp;
                <a href="javascript:goURL(-1);" style="color:#0c99cc;">Forecast over 2011 domain</a>
            </td>
            <td background="images/left_right_mid.gif"></td>
          </tr>
          <tr>
            <td><img src="images/left_left_bottom.gif" width="14" height="12" /></td>
            <td background="images/left_mid_bottom.gif"></td>
            <td><img src="images/left_right_bottom.gif" width="9" height="12" /></td>
          </tr>
        </table>
        <div style="color:#aaaaaa; font-size: 10px; font-family: Verdana, Arial, Helvetica, sans-serif;
                    position:relative;top:10px;left:15px;">
        <img src="http://www.caps.ou.edu/ARPS/cgi-bin/PScounter.cgi?id=hurr&what=counter" border=0>
        Since Sept. 11, 2010
        </div>
     <div id="footer">
       <ul class="rounded">
           <li>Mobile View <span class="toggle"><form>
           	 <input type="checkbox" name="mobileview" onchange="normalview()" /></form></span>
           	</li>
       </ul>
     </div>

     </td>
    <td align="left" valign="top">
        <!-- main animation controller -->
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="11"><img src="images/main_timer_left.gif" width="11" height="76" /></td>
            <td background="images/main_timer_mid.gif" align="left" valign="middle">
            <table width="840px" border="0" cellspacing="0" cellpadding="0">
            <tr><td align="center">
            <!-- Play buttons -->
                <table width="200" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td>
                        <img src="images/first_25.gif"  alt="First Frame" title="First Frame" style="cursor:hand"  border="0"
                             onclick="jumpBegin();"
                             onMouseOver="this.src='images/first_25_over.gif';"
                             onMouseOut="this.src='images/first_25.gif';"
                             onMouseDown="this.src='images/first_25.gif';"
                             onMouseUp="this.src='images/first_25_over.gif';" />
                    </td>
                    <td>
                        <img src="images/pre_25.gif"  alt="Previous Frame" title="Previous Frame" style="cursor:hand"  border="0"
                             onclick="jumpBack();"
                             onMouseOver="this.src='images/pre_25_over.gif';"
                             onMouseOut="this.src='images/pre_25.gif';"
                             onMouseDown="this.src='images/pre_25.gif';"
                             onMouseUp="this.src='images/pre_25_over.gif';" />
                    </td>
                    <td valign="middle">
                    <span id="playButton" class="buttonoff"><img src="images/play_25.gif" alt="Play" title="Play" border="0" style="cursor:hand"
                         onclick="playPic();"
                         onMouseOver="this.src='images/play_25_over.gif';"
                         onMouseOut="this.src='images/play_25.gif';"
                         onMouseDown="this.src='images/play_25.gif';"
                         onMouseUp="this.src='images/play_25_over.gif';"/></span>
                    <span id="pauseButton" class="buttonon"><img src="images/pause1_25.gif" alt="Pause"  title="Pause" border="0" style="cursor:hand"
                         onclick="playPic();"
                         onMouseOver="this.src='images/pause1_25_over.gif';"
                         onMouseOut="this.src='images/pause1_25.gif';"
                         onMouseDown="this.src='images/pause1_25.gif';"
                         onMouseUp="this.src='images/pause1_25_over.gif';"/></span>
                    </td>
                    <td>
                        <img src="images/next_25.gif" alt="Next Frame" title="Next Frame" style="cursor:hand" border="0"
                             onclick="jumpForward();"
                             onMouseOver="this.src='images/next_25_over.gif';"
                             onMouseOut="this.src='images/next_25.gif';"
                             onMouseDown="this.src='images/next_25.gif';"
                             onMouseUp="this.src='images/next_25_over.gif';"/>
                    </td>
                    <td>
                        <img src="images/last_25.gif" alt="Last Frame" title="Last Frame" style="cursor:hand" border="0"
                             onclick="jumpEnd();"
                             onMouseOver="this.src='images/last_25_over.gif';"
                             onMouseOut="this.src='images/last_25.gif';"
                             onMouseDown="this.src='images/last_25.gif';"
                             onMouseUp="this.src='images/last_25_over.gif';"/>
                    </td>
                    <td align="right" width="40">
                        <table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td><img src="images/speed_up.gif"  alt="Faster" title="Faster"
                                     onclick="upSpeed();"   style="cursor:hand"  border="0"
                                     onMouseOver="this.src='images/speed_up_over.gif';"
                                     onMouseOut="this.src='images/speed_up.gif';"
                                     onMouseDown="this.src='images/speed_up.gif';"
                                     onMouseUp="this.src='images/speed_up_over.gif';"/>
                            </td>
                            <td><img src="images/speed_down.gif"  alt="Slower" title="Slower"
                                     onclick="downSpeed();"   style="cursor:hand"  border="0"
                                     onMouseOver="this.src='images/speed_down_over.gif';"
                                     onMouseOut="this.src='images/speed_down.gif';"
                                     onMouseDown="this.src='images/speed_down.gif';"
                                     onMouseUp="this.src='images/speed_down_over.gif';"/>
                            </td>
                        </tr>
                        </table>
                    </td>
                </tr>
                </table>
            </td>
            </tr>
            <tr>
            <td align="center" valign="bottom">
                <!-- Slider -->
                <div class="slider" id="div_slider" tabIndex="1" style="position:relative;top:10px;">
                    <input type="hidden" class="slider-input" id="input_slider" name="slider-input-1"/>
                </div>
            </td>
            </tr>
            </table>
            <!-- top background table element -->
            </td>
            <td width="11" ><img src="images/main_timer_right.gif" width="11" height="76" /></td>
          </tr>
        </table>

        <!-- main animation frame -->
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td background="images/main_title_left.gif"></td>
            <td background="images/main_title_mid.gif" height="25">
        	  <div style="padding-top:5px;font-size: 10pt; color:#FFFFFF; ">
        	  	<!--
                        <img src="images/title_arrow.gif" border="0" />
                        <b>Composite Reflectivity</b>&nbsp;&nbsp;
                        -->
                        <img src="images/title_arrow.gif" border="0" />
                        <span style="font-size:9pt;">CASE：</span>
                        <b><span style="font-size:11pt;" id="status_type">arwcn</span></b>&nbsp;&nbsp;

                        <img src="images/title_arrow.gif" border="0" />
                        <span style="font-size:9pt;">START TIME：</span>
                        <b><span style="font-size:11pt;" id="status_date">2010-05-11</span></b>&nbsp;&nbsp;

                        <img src="images/title_arrow.gif" border="0" />
                        <span style="font-size:9pt;">FORECAST：</span>
                        <b><span style="font-size:11pt;" id="status_forecast"></span></b>&nbsp;&nbsp;

                        <img src="images/title_arrow.gif" border="0" />
                        <span style="font-size:9pt;">FPS：</span>
                        <b><span id="status_rate"></span></b>&nbsp;&nbsp;

                        <img src="images/title_arrow.gif" border="0" />
                        <span style="font-size:9pt;">FRAME：</span><b><span id="status_frame"></span></b>
        	  </div>
            </td>
            <td background="images/main_title_right.gif"></td>
          </tr>
          <tr>
            <td background="images/main_left_mid.gif"></td>
            <td>
                <div id="div_frames" class="frames" style="position:relative;">
                    <img src="loading.gif" name="img_frames" id="img_frames" tabIndex="2"
                            onkeydown="image_keycheck(event);" onclick="showBox(event);" />
                </div>

                <div id="static_frame" class="static_frame" style="position:absolute;left:306px; top: 180px;z-index:1;">
  <table border="0" cellspacing="2" cellpadding="0">
    <tr>
      <td>&nbsp;</td>
      <td><img src="images/map/nozoom.gif" width="20" height="20" style="cursor:hand" onClick="setScreenOk();" title="Full Screen"></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><img src="images/map/zoom.gif" width="20" height="20" style="cursor:hand" onClick="realsize();" title="Original Size"></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><img src="images/map/zoom_in.gif" width="20" height="20" style="cursor:hand" onClick="bigit();" title="Zoom In"></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><img src="images/map/zoom_out.gif" width="20" height="20" style="cursor:hand" onClick="smallit();" title="Zoom Out"></td>
      <td>&nbsp;</td>
    </tr>
  </table>
                </div>
            </td>
            <td background="images/main_right_mid.gif"></td>
          </tr>
          <tr>
            <td width="11"><img src="images/main_left_bottom.gif" width="11" height="12" /></td>
            <td background="images/main_mid_bottom.gif"></td>
            <td width="11"><img src="images/main_right_bottom.gif" width="11" height="12" /></td>
          </tr>
          <tr>
            <td colspan="3">
       <font style="font: 10px Verdana, Arial, Helvetica, Sans-serif;color:#666666;">
       Last Updated on: {$webtime}.
       Recommended browser size: 1280x800 or larger.
       </font>
       <font class="tdNote" style="font: 10px Verdana, Arial, Helvetica, Sans-serif;color:#666666;float:right;">
       <a href="datastatus.html" target="_blank">
       Image Processing Status.</a>
       </font>
            </td>
          </tr>
       </table>

    </td>
</tr>
</table>
</div>

<div id="box" style="position:absolute;z-index:5; border: solid red 1px; visibility:hidden;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr><td bgcolor="#ececec" style="font-size:9pt;font-weight:bold;font-family:Arial, Helvetica, sans-serif;" width="300">
            <form name="form1">
                &nbsp;&nbsp;Box Size：
                <select name="boxsize" onchange="setBoxSize(this)">
                <option value="320x240">320x240</option>
                <option value="400x300" selected="selected">400x300</option>
                <option value="640x480">640x480</option>
                <option value="800x600">800x600</option>
                </select>
                &nbsp;&nbsp;Zoom Level：
                <select name="boxlevel" onchange="setBoxLevel(this)">
                <option value="1.5" selected="selected">1.5</option>
                </select>
            </form>
            <img id="boxnail" name="nail" src="images/unlock.png"
                 style="cursor:default;opacity:0.6;position:absolute;"
                 onmouseover="checkLockIn(this)"
                 onmouseout="checkLockOut(this)"
                 onclick="lockZoomBox()" />
            <img id="boxclose" name="close" src="images/Close.png"
                 style="cursor:default;opacity:0.6;position:absolute;"
                 onmouseover="this.src='images/Close-over.png'"
                 onmouseout="this.src='images/Close.png'"
                 onclick="hideBox();" />
        </td>
        <td bgcolor="#ececec" style="cursor: move;" onmousedown="dragStart(event,'box')">&nbsp;</td>
        <td bgcolor="#ececec" width="10">&nbsp;</td>
        </tr>
        <tr><td colspan="3"><img id="boximg"  name="boximg" src="" style="cursor: move;" onmousedown="dragStart(event,'box')" />
        </td>
        </tr>
    </table>
</div>

<div id="aboutWindow" style="position:absolute;top:40px;left:400px;display:none;visibility:hidden;">
    <TABLE WIDTH="600" BORDER=0 CELLPADDING="5" CELLSPACING=0 BGCOLOR="#000066" style="opacity:0.9; border:0.05em solid #FEFFAA;-moz-border-radius:.5em;">
        <TR>
            <td onclick="toggleProduct('aboutWindow')">
<center><span style="font-size:large;font-weight: bold; text-align:center;color:#ffffff;">4-km Resolution Realtime Forecasts for the Atlantic Hurricane Season</span><br/>
<span style="font-size:medium;font-weight: bold; text-align:center;color:#ffffff;">Presented by the Center for Analysis and Prediction of Storms</span><br/>
<span style="font-size:medium;font-weight: bold; text-align:center;color:#ffffff;">University of Oklahoma</span><br/></center>

<p style="color:#ffffff;">CAPS is producing twice daily (00 and 12 UTC) 48-hour-long realtime 4-km grid-spacing WRF ARW forecasts
for the 2010 hurricane season over much of the Atlantics, initialized from GFS analysis, and from global
EnKF analysis produced by Jeff Whitaker of ESRL. The forecasts are posted at
<a href="http://www.caps.ou.edu/wx/p/" style="color:#EEEE00;">http://www.caps.ou.edu/wx/p/</a> and enter through the daily calendar.</p>

<p style="color:#ffffff;">
This page contains select forecast highlights with animations of Composite Reflectivity, Surface Winds and Mean Sea Level Pressure etc.</p>
           </TD>
        </TR>
    </TABLE>';
</div>

<div id="helpWindow" style="position:absolute;top:40px;left:600px;display:none;visibility:hidden;">
    <TABLE WIDTH="550" BORDER=0 CELLPADDING="5" CELLSPACING=0 BGCOLOR="#000066" style="opacity:0.9; border:0.05em solid #FEFFAA;-moz-border-radius:.5em;">
        <TR>
            <td>
            <img id="helpclose" name="helpclose" src="images/Close.png"
                 style="cursor:default;opacity:0.6;"
                 onmouseover="this.src='images/Close-over.png'"
                 onmouseout="this.src='images/Close.png'"
                 onclick="toggleProduct('helpWindow');" align="right" />
<span style="font-size:large;font-weight: bold; text-align:center;color:#ffffff;"><a href="javascript:toggleProduct('animControl');" style="color:#EEEE00;">To Control Animation</a>:</span>
<ul id="animControl" style="display:none;">
<p style="color:#ffffff;">This web page contains two panels. Left panel is for calendar, case chooser and product area. Right panel contains animation control panel and animation frame. The web page will resynchronize with the server every 30 minutes automatically for product availability.</p>
<li style="color:#ffffff;">User can choose a forecast date by clicking the calendar directly. The forecast available days are higlighted with colors. All dates in gray indicate forecast is not available on that day.</li>
<li style="color:#ffffff;">A forecast case identified with initialization time can be chosen from two sets just below the calendar. One set is initialized from NCEP GFS analyses and the other one is from global EnKF analyses produced by Dr. Jeff Whitaker of ESRL.</li>
<li style="color:#ffffff;">The forecast products for the chosen case on the highlighted calendar day can be selected in the production area. Text in gray color indicates unavailable product on that day for that specific case.</li>
<li style="color:#ffffff;">The status bar just above the animation frame should change accordingly once a selection is made.</li>
<li style="color:#ffffff;">The link "More Forecast Fields" will redirect to <a href="http://www.caps.ou.edu/wx/p/" style="color:#EEEE00;">http://www.caps.ou.edu/wx/p/</a> for additional forecast products with the selected case on the selected day. The back button of the web browser should bring you back to this page again.</li>
<li style="color:#ffffff;">Buttons <img src="images/speed_up.gif" />, <img src="images/speed_down.gif" /> are used for increasing / reducing animation speed (FPS).</li>
</ul>
            </td>
        </tr>
        <tr><td>
<span style="font-size:large;font-weight: bold; text-align:center;color:#ffffff;"><a href="javascript:toggleProduct('toZoom');" style="color:#EEEE00;">To Zoom Image</a>:</span>
<ul id="toZoom" style="display:none;">
<p style="color:#ffffff;">The server provides all images with two resolutions. The lower resolution (1000x1000) is for animation purpose and the higher resolution is 1500x1500.</p>
<li style="color:#ffffff;">First, stop the animation using the control buttons above the frame.</li>
<li style="color:#ffffff;">Click anywhere in the image, a pop-up window will be opened with higher resolution image centered at where user clicked.</li>
<li style="color:#ffffff;">User can adjust the zoom box size with "Box Size" drop-down list.</li>
<li style="color:#ffffff;">The zoom box can also be moved around with mouse by dragging the gray area just left the lock button <img src="images/unlock.png">.</li>
<li style="color:#ffffff;">User can lock the zoom box with button <img src="images/unlock.png"> so that the zoom window can animate synchronously with the main loop once user starts the animation again. Note that lower animation speed is recommended, especially with large zoom box size. The zoom box will also be locked automatically once it is dragged around.</li>
<li style="color:#ffffff;">Locked zoom box can only be dismissed with the close button <img src="images/Close.png">.</li>
</ul>
            </td>
        </tr>
        <tr><td>
<span style="font-size:large;font-weight: bold; text-align:center;color:#ffffff;"><a href="javascript:toggleProduct('imgControl');" style="color:#EEEE00;">To Manipulate Image</a>:</span>
<ul id="imgControl" style="display:none;">
<p style="color:#ffffff;">Once the animation is stopped, a set of controllers will appear on the left-top corner of the image.</p>
<li style="color:#ffffff;">Click <img src="images/map/nozoom.gif" /> for full screen (actually, full size of the browser client area).</li>
<li style="color:#ffffff;">Click <img src="images/map/zoom_in.gif" />, <img src="images/map/zoom_out.gif" /> for zooming in and zooming out.</li>
<li style="color:#ffffff;">Button <img src="images/map/zoom.gif" /> for restoring to original size and location.</li>
<li style="color:#ffffff;">Users can also control the slide show with keyboard as long as the slider handler is green and it will turn to green when the animation is stopped or users click the slider handler explicitly. The acceptable key strokes are arrow keys, Page Up, Page Down, Home and End etc.</li>
</ul>
           </TD>
        </TR>
    </TABLE>
</div>

</body>
</html>
