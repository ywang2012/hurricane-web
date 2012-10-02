
eventpos = new Array();

function showBox(e,reshowing){
	if (playing || zoomlevel > 1.0 || zoomlevel < 1.0) return;
	// do nothing while playing

	var box = document.getElementById("box");
	if (!reshowing) {
		if (config.mozilla){
			var image = e.target; //document.images[config.image_frame];
			var clickX = e.layerX;
			var clickY = e.layerY;
			var absleft = e.clientX-config.boxwidth/2;
			var abstop = e.clientY-config.boxheight/2;
		} else {
			var image = window.event.srcElement; //document.images[config.image_frame];
			var clickX = window.event.x;
			var clickY = window.event.y;
			//var contner = image.parentNode;
			var absleft = window.event.clientX-config.boxwidth/2; //clickX+contner.offsetLeft-config.boxwidth/2;
			var abstop =  window.event.clientY-config.boxheight/2; //clickY+contner.offsetTop-config.boxheight/2;
		}
		eventpos['x'] = clickX
		eventpos['y'] = clickY
	} else {
		var image = document.images[config.image_frame]
		var absleft = parseInt(box.style.left)
		var abstop  = parseInt(box.style.top)
		var clickX  = eventpos['x']
		var clickY  = eventpos['y']
	}
	if (!config.boxlocked || reshowing) {
		//alert(absleft)
		box.style.left=(absleft-1)+'px';
		box.style.top=(abstop-25)+'px';
		box.style.width=config.boxwidth+'px';
		box.style.height=(config.boxheight+24)+'px';
		var nail = document.getElementById("boxnail");
		nail.style.top='1px';
		nail.style.left=(config.boxwidth-34) + 'px';
		var close = document.getElementById("boxclose");
		close.style.top='1px';
		close.style.left=(config.boxwidth-16) + 'px';
	}

        var reg1 = /(http:\/)?\/?([^:\/\s]+)((\/\w+)*\/)([\w\-\.]+\.[^#?\s]+)(\?file=([^&]+))/;
	var reg2 = /(http:\/)?\/?([^:\/\s]+)((\/\w+)*\/)([\w\-\.]+\.[^#?\s]+)/;
	var file = '';
	if (image.src.match(reg1)) {
		file = RegExp.$7;
	} else if (image.src.match(reg2)) {
		file = RegExp.$3+RegExp.$5;
	} else {
		alert("Unknown image src = "+image.src);
	}
	/*
	box.innerHTML='<img id="boximg" src="zoomimg.php?file='+file+
			        '&top='+clickY+'&left='+clickX+
			        '&width='+(config.boxwidth)+'&height='+(config.boxheight)+
				'&orgwidth='+image.width+'&orgheight='+image.height+'" />';
	*/
	document.images['boximg'].src='zoomimg.php?file='+file+'&level='+config.boxlevel+
			'&top='+clickY+'&left='+clickX+
			'&width='+(config.boxwidth)+'&height='+(config.boxheight)+
			'&orgwidth='+image.width+'&orgheight='+image.height;
/*
 alert('zoomimg.php?file='+file+'&level='+config.boxlevel+
			'&top='+clickY+'&left='+clickX+
			'&width='+(config.boxwidth)+'&height='+(config.boxheight)+
			'&orgwidth='+image.width+'&orgheight='+image.height)			
*/
	box.style.visibility="visible";
	config.boxshown = true;
};

function updateBoxImage() {
	if (! config.boxshown) return;
	var boximg = document.getElementById("boximg");
	if (!boximg) return;

	var image = document.images[config.image_frame];

	var reg1 = /(http:\/)?\/?([^:\/\s]+)((\/\w+)*\/)([\w\-\.]+\.[^#?\s]+)(\?file=([^&]+))/;
	var reg2 = /(http:\/)?\/?([^:\/\s]+)((\/\w+)*\/)([\w\-\.]+\.[^#?\s]+)/;

	var file = '';
	if (image.src.match(reg1)) {
		file = RegExp.$7;
	} else if (image.src.match(reg2)) {
		file = RegExp.$3+RegExp.$5;
	} else {
		alert("Unknown image src = "+image.src);
	}

	var reg3 = /([^?]+)\?file=([^&]+)(.*)/;
	if (boximg.src.match(reg3)) {
		//alert(RegExp.$1+'?file='+file+RegExp.$3);
		boximg.src = RegExp.$1+'?file='+file+RegExp.$3;
	}
}

function hideBox() {
	document.images['boximg'].src=''
	config.boxlocked = false;
	document.getElementById('boxnail').src="images/unlock.png";
	var box = document.getElementById("box");
	if (box) {
		box.style.visibility="hidden";
	}
	config.boxshown = false;
}

function lockZoomBox(toLock) {
	//document.getElementById("box").onmouseout=function() {};
	if (toLock) {
			document.getElementById("boxnail").src="images/lock.png";
			config.boxlocked = true;
	} else {
		if (config.boxlocked) {
			document.getElementById("boxnail").src="images/unlock-over.png";
			config.boxlocked = false;
		} else {
			document.getElementById("boxnail").src="images/lock-over.png";
			config.boxlocked = true;
		}
	}
}

function checkLockIn(lockicon) {
        if (config.boxlocked) lockicon.src="images/lock-over.png";
        else lockicon.src="images/unlock-over.png";
}

function checkLockOut(lockicon) {
        if (config.boxlocked) lockicon.src="images/lock.png";
        else lockicon.src="images/unlock.png";
}

function setBoxSize(form) {
	if (playing || zoomlevel > 1.0 || zoomlevel < 1.0) return;
	// do nothing while playing
	var index = form.selectedIndex
	var sizestr = form.options[index].value.split('x');
	config.boxwidth = parseInt(sizestr[0]);
	config.boxheight = parseInt(sizestr[1]);
	//alert(config.boxwidth+''+config.boxheight)
	showBox(null,true)
}

function setBoxLevel(form) {
	if (playing || zoomlevel > 1.0 || zoomlevel < 1.0) return;  // do nothing while playing
	config.boxlevel = parseFloat(form.value);
	//alert(config.boxlevel)
	showBox(null,true)
}

//*****************************************************************************
// Do not remove this notice.
//
// Copyright 2001 by Mike Hall.
// See http://www.brainjar.com for terms of use.
//*****************************************************************************

// Global object to hold drag information.

var dragObj = new Object();

function dragStart(event, id) {

  var el;
  var x, y;

  // If an element id was given, find it. Otherwise use the element being
  // clicked on.

  if (id)
    dragObj.elNode = document.getElementById(id);
  else {
    if (config.mozilla) dragObj.elNode = event.target;
    else                dragObj.elNode = window.event.srcElement;
  }

  // Get cursor position with respect to the page.
  if (config.mozilla) {
    x = event.clientX + window.scrollX;
    y = event.clientY + window.scrollY;
  } else {
    x = window.event.clientX + document.documentElement.scrollLeft
      + document.body.scrollLeft;
    y = window.event.clientY + document.documentElement.scrollTop
      + document.body.scrollTop;
  }

  // Save starting positions of cursor and element.

  dragObj.cursorStartX = x;
  dragObj.cursorStartY = y;
  dragObj.elStartLeft  = parseInt(dragObj.elNode.style.left, 10);
  dragObj.elStartTop   = parseInt(dragObj.elNode.style.top,  10);

  if (isNaN(dragObj.elStartLeft)) dragObj.elStartLeft = 0;
  if (isNaN(dragObj.elStartTop))  dragObj.elStartTop  = 0;

  // Capture mousemove and mouseup events on the page.
  if (config.mozilla) {
    document.addEventListener("mousemove", dragGo,   true);
    document.addEventListener("mouseup",   dragStop, true);
    event.preventDefault();
  } else {
    document.attachEvent("onmousemove", dragGo);
    document.attachEvent("onmouseup",   dragStop);
    window.event.cancelBubble = true;
    window.event.returnValue = false;
  }
}

function dragGo(event) {

  var x, y;

  // Get cursor position with respect to the page.
  if (config.mozilla) {
    x = event.clientX + window.scrollX;
    y = event.clientY + window.scrollY;
  } else {
    x = window.event.clientX + document.documentElement.scrollLeft
      + document.body.scrollLeft;
    y = window.event.clientY + document.documentElement.scrollTop
      + document.body.scrollTop;
  }

  // Move drag element by the same amount the cursor has moved.

  dragObj.elNode.style.left = (dragObj.elStartLeft + x - dragObj.cursorStartX) + "px";
  dragObj.elNode.style.top  = (dragObj.elStartTop  + y - dragObj.cursorStartY) + "px";

  if (config.mozilla)
    event.preventDefault();
  else {
    window.event.cancelBubble = true;
    window.event.returnValue = false;
  }

  if ((x-dragObj.cursorStartX) > 0 || (y-dragObj.cursorStartY) >0 )
      lockZoomBox(true);
}

function dragStop(event) {

  // Stop capturing mousemove and mouseup events.
  if (config.mozilla) {
    document.removeEventListener("mousemove", dragGo,   true);
    document.removeEventListener("mouseup",   dragStop, true);
  } else {
    document.detachEvent("onmousemove", dragGo);
    document.detachEvent("onmouseup",   dragStop);
  }
}

function iphoneZoom() {
	if (config.mozilla){
		var image = e.target; //document.images[config.image_frame];
		var clickX = e.layerX;
		var clickY = e.layerY;
	} else {
		var image = window.event.srcElement; //document.images[config.image_frame];
		var clickX = window.event.x;
		var clickY = window.event.y;
	}
	var imgwidth  = image.width;
	var imgheight = image.height;
	I.reqlevel = 1;
	I.reqwratio = clickX/imgwidth;
	I.reqhratio = clickY/imgheight;
	updateAnimation(config.currdate,config.currtype);
}
