<?php /* Smarty version 3.0rc1, created on 2012-10-02 10:06:27
         compiled from "/vol0/www/html/forecast/ywang/hurricane2/smarty/templates/mobile_anim.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1122656032506b02f349b358-69478996%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c3a5151801e624807bd8986df64b6b63282272ee' => 
    array (
      0 => '/vol0/www/html/forecast/ywang/hurricane2/smarty/templates/mobile_anim.tpl',
      1 => 1347896413,
    ),
  ),
  'nocache_hash' => '1122656032506b02f349b358-69478996',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<!DOCTYPE html>
<html>
  <head>

    <?php echo $_smarty_tpl->getVariable('common_header')->value;?>


    <script language="javascript" src="scripts/animation.js"></script>
    <script language="javascript" src="scripts/iphone_touch.js"></script>

		<script type="text/javascript">

      defdate = "<?php echo $_smarty_tpl->getVariable('defdate')->value;?>
";
      defcase = "<?php echo $_smarty_tpl->getVariable('defcase')->value;?>
";
      deffield = "<?php echo $_smarty_tpl->getVariable('deffield')->value;?>
";

      listurl = "updateanim.php";

      function toggleMenu() {
        $('#header .leftButton').toggleClass('pressed');
      }

      function toggleDisplay(elid) {
        if ( $("#"+elid).css("display") == "none" ) {
        	$("#"+elid).css("display","block");
        } else {
        	 $("#"+elid).css("display","none") ;
        }
        return false;
      }

      function goBack() {
        //history.go(-1);
       var dstr = defdate.replace(/-/g,'');
       window.location.href = "mobile.php/prod/"+dstr+"/"+defcase+"/"+deffield+"/";
      }

      $(function() {
        initAnimation("imgFrame",["numFrame"],listurl,defdate,defcase+deffield,false);
        //stopPlaying(false);
      })

      // toolbar buttons
	    $(function() {
        if (/(iPhone|iPod|iPad)/.test(navigator.userAgent)) {
          $('#toolbar').html('<h1><a href="javascript:toggleDisplay(\'touchpanel\');">Touch Instructions</a></h1>' )
          .css('left','100px');
        } else {
          $('#toolbar').html(
       	'<button id="beginning">go to beginning</button>' +
       	'<button id="rewind">rewind</button>' +
       	'<button id="play">play / pause</button>' +
       	'<button id="forward">fast forward</button>' +
       	'<button id="end">go to end</button>' +
       	'<span id="repeat">' +
       	'	<button id="faster">faster</button>' +
       	'	<button id="slower">slower</button>' +
       	'</span>' );
       	  $('#toolbar').addClass('aligncenter');

	    	  $( "#beginning" ).button({
	    	  	text: false,
	    	  	icons: {
	    	  		primary: "ui-icon-seek-start"
	    	  	}
	    	  }).click(jumpBegin);

	    	  $( "#rewind" ).button({
	    	  	text: false,
	    	  	icons: {
	    	  		primary: "ui-icon-seek-prev"
	    	  	}
	    	  }).click(jumpBack);

	    	  $( "#play" ).button({
	    	  	text: false,
	    	  	icons: {
	    	  		primary: "ui-icon-pause"
	    	  	}
	    	  }).click(function() {
	    	  	var options;
	    	  	if ( $( this ).text() === "play" ) {
	    	  		options = {
	    	  			label: "pause",
	    	  			icons: {
	    	  				primary: "ui-icon-pause"
	    	  			}
	    	  		};
	    	  	} else {
	    	  		options = {
	    	  			label: "play",
	    	  			icons: {
	    	  				primary: "ui-icon-play"
	    	  			}
	    	  		};
	    	  	}
	    	  	$( this ).button( "option", options );
	    	  	togglePlaying(false);
	    	  });

	    	  $( "#forward" ).button({
	    	  	text: false,
	    	  	icons: {
	    	  		primary: "ui-icon-seek-next"
	    	  	}
	    	  }).click(jumpForward);

	    	  $( "#end" ).button({
	    	  	text: false,
	    	  	icons: {
	    	  		primary: "ui-icon-seek-end"
	    	  	}
	    	  }).click(jumpEnd);

	    	  $( "#faster" ).button({
	    	  	text:false,
	    	  	icons : {
	    	  		primary : "ui-icon-plus"
	    	  	}
	    	  }).click(incRate);
	    	  $( "#slower" ).button({
	    	  	text:false,
	    	  	icons : {
	    	  		primary : "ui-icon-minus"
	    	  	}
	    	  }).click(decRate);
	    	  $( "#repeat" ).buttonset();

	      }
	    });

      var iostouch = new ios_touch();

			function processingTouchRoutine() {
				var swipedElement = document.getElementById(iostouch.triggerElementID);
				if ( iostouch.swipeDirection == 'left' ) {
					//swipedElement.style.backgroundColor = 'green';
					jumpForward();
				} else if ( iostouch.swipeDirection == 'right' ) {
					//swipedElement.style.backgroundColor = 'green';
					jumpBack();
				} else if ( iostouch.swipeDirection == 'up' ) {
					//swipedElement.style.backgroundColor = 'maroon';
					jumpBegin();
				} else if ( iostouch.swipeDirection == 'down' ) {
					//swipedElement.style.backgroundColor = 'purple';
					jumpEnd();
				} else {
					togglePlaying(false);
				}
			}
		</script>
  </head>

  <body>
     <div id="header">
       <a class="back" href="javascript:goBack()">Back</a>
       <h3>"<?php echo $_smarty_tpl->getVariable('deffield')->value;?>
" for <?php echo $_smarty_tpl->getVariable('defcase')->value;?>
 on <?php echo $_smarty_tpl->getVariable('defdate')->value;?>
</h3>
       <div id="numFrame" class="rightFrm"></div>
     </div>

     <div id="animFrame">
       <div id="animImg" ontouchstart="iostouch.touchStart(event,'animImg')"
          ontouchend="iostouch.touchEnd(event)"
          ontouchmove="iostouch.touchMove(event)"
          ontouchcancel="iostouch.touchCancel(event)"
          >
        <img id="imgFrame" src="http://forecast.ou.edu/ywang/hurricane2/loading.gif">
       </div>
     </div>

     <div id="touchpanel" class="ui-widget-header ui-corner-all"
          style="display:none;">
       <ul>
       	  <li>toggle play/pause : <span class="swipenote">single touch</span> </li>
       	  <li>next frame : <span class="swipenote">swipe left </span></li>
       	 	<li>previous frame : <span class="swipenote">swipe right </span></li>
       	 	<li>the first frame : <span class="swipenote">swipe up </span></li>
       	 	<li>the last frame : <span class="swipenote">swipe down </span></li>
       	  <li>scroll or zoom : <span class="swipenote">two finger drag or pinch</span> </li>
        </ul>
     </div>

     <div id="toolbar" class="ui-widget-header ui-corner-all"
     	    style="width:450px;display:block;">
     </div>

  </body>
</html>