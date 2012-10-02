// Class to save configurations
function Configueration() {
  this.image_frame = null;
  this.slider = null;
  this.currcase = null;
  this.currfield = null;
  this.currdate = null;
  this.iphone = false;
  this.mozilla=false;
  this.chrome = false;
}

var config = new Configueration();

function initMain(div_image,div_frame,sscript,sdate,scase,sfield,iphone)  {
  if (/(Firefox|Chrome|Safari)/.test(navigator.userAgent)){
    config.mozilla = true;
  }
  if (/Chrome/.test(navigator.userAgent)){
    config.chrome = true;
  }
  config.boxwidth  = 400;
  config.boxheight = 300;
  config.boxlevel  = 1.5;
  config.boxlocked = false;

  config.currcase = scase;
  config.currfield = sfield;
  config.currdate = sdate;
  config.image_frame = div_image;
  config.iphone = iphone;

  var block1 = document.getElementById('body_container')
  var cheight = parent.document.documentElement.clientHeight
  block1.style.height=(cheight-10) + "px";

  config.updateDelay = 30*60*1000;

  config.slider = new Slider(document.getElementById("div_slider"),
                           document.getElementById("input_slider"));
  var stype = scase+sfield;
  initAnimation(div_image,div_frame,sscript,sdate,stype,config.slider);
  setDate(sdate);
  setCase(scase);
  setField(sfield);
  setPlayButton();
  setTimeout("autoUpdates()",config.updateDelay);
}

function dateChanged(dp) {
  setDate(dp.cal.getNewDateStr());
  updateAnimation(config.currdate,config.currcase+config.currfield);
  setPlayButton();
}

function caseChanged(scase) {
  setCase(scase);
  var stype = config.currcase + config.currfield;
  updateAnimation(config.currdate,stype);
  setPlayButton();
}

function fieldChanged(sfield) {
  setField(sfield);
  var stype = config.currcase + config.currfield;
  updateAnimation(config.currdate,stype);
  setPlayButton();
}

/************** Setting Document elements ****************************/

function setPlayButton() {
  if (playing) {
    document.getElementById("playButton").className="buttonoff";
    document.getElementById("pauseButton").className="buttonon";
    document.getElementById("static_frame").style.visibility = 'hidden'
  } else {
    document.getElementById("playButton").className="buttonon";
    document.getElementById("pauseButton").className="buttonoff";
    showController();
  }
  setSlider();
  setFPS();
}

function setSlider() {
  config.slider.setMaximum(I.length);
  config.slider.setMinimum(1);
  if (playing) {
    config.slider.onchange=updateBoxImage;
  } else {
    config.slider.onchange=function() {
      I.setImg(config.slider.getValue()-1);
      showFrame();
      updateBoxImage();
    };
    document.getElementById("div_slider").focus();
  }
}

function setCase(scase){
  config.currcase = scase;
  var stype = scase + config.currfield;
  try{
    if (config.currtype!=null) {
      document.getElementById("td_" +config.currcase).className="tdNormal";
    }
  }catch(e){

  }
  //document.getElementById("td_" +stype).className="tdSelected";
  setTDtype();
  document.getElementById("status_type").innerHTML=stype;
  fhour = parseInt(stype.substring(3,5));
  fhourstr = fhour > 9 ? fhour+"Z" : "0"+fhour+"Z"
  document.getElementById("status_date").innerHTML=config.currdate+" "+fhourstr;
}

function setField(sfield){
  config.currfield = sfield;
  var stype = config.currcase+sfield;
  try{
    if (config.currtype!=null) {
      document.getElementById("td_" +config.currfield).className="tdNormal";
      document.getElementById("img_"+config.currfield).src="images/left_pro_arrow.gif";
    }
  }catch(e){

  }
  //document.getElementById("td_" +stype).className="tdSelected";
  setTDtype();
  document.getElementById("status_type").innerHTML=stype;
  fhour = parseInt(stype.substring(3,5));
  fhourstr = fhour > 9 ? fhour+"Z" : "0"+fhour+"Z"
  document.getElementById("status_date").innerHTML=config.currdate+" "+fhourstr;
}

function setDate(sdate) {
  config.currdate = sdate;
  fhour = parseInt((config.currcase + config.currfield).substring(3,5));
  fhourstr = fhour > 9 ? fhour+"Z" : "0"+fhour+"Z"
  document.getElementById("status_date").innerHTML=config.currdate+" "+fhourstr;
  setTDtype();
}

function setFPS() {
  if (playing) {
    document.getElementById("status_rate").innerHTML=fps;
  } else {
    document.getElementById("status_rate").innerHTML="stopped";
  }
}

/*********************** Animation Button controller *****************/
function playPic() {
  if(config.boxshown && !config.boxlocked) hideBox();
  togglePlaying(true);
  setPlayButton();
}

function upSpeed() {
  if (playing) incRate();
  setFPS();
}

function downSpeed() {
  if (playing) decRate();
  setFPS();
}

/*************** Extra document dynamical stuffs **********************/
function showController() {
  try {
    var divitem = document.getElementById("static_frame");
    //alert(block1.offsetLeft)
  } catch (e) {
    alert('does not support getElementById')
  }

  initStatic();

  divitem.style.visibility = 'visible';
  divitem.style.left=block1.offsetLeft+'px'
  divitem.style.top=block1.offsetTop+20+'px'

  return;
}


function caseChooser() {
  document.getElementById("top_container").style.visibility="visible";
}

function closeChooser() {
  document.getElementById("top_container").style.visibility="hidden";
}

// For PC, key control
function image_keycheck(e) {
  var KeyID = (window.event) ? window.event.keyCode : e.keyCode;
  //alert(KeyID)
  switch(KeyID) {
    case 27:   // escape
      realsize();
      break;
    case 33:   // up
      bigit();
      if (e && e.stopPropagation) //if stopPropagation method supported
        e.stopPropagation()
      else
        event.cancelBubble=true;
      break;
    case 34:   // down
      smallit();
      if (e && e.stopPropagation) //if stopPropagation method supported
        e.stopPropagation()
      else
        event.cancelBubble=true;
      break;
  }
}

function setTDtype() {
  //change case class
  for ( var i=0, len=casenames.length;i<len;++i) {
    var stype = casenames[i];
    document.getElementById("td_"+stype).className="tdFadeOut";
  }
  for ( var i=0, len=datetypes[config.currdate].length; i<len; ++i ){
    var stype = datetypes[config.currdate][i];
    document.getElementById("td_"+stype).className="tdNormal";
  }
  if (config.currcase && contains(datetypes[config.currdate],config.currcase) ) {
    document.getElementById("td_" +config.currcase).className="tdSelected";
  }

        // Change field class
  for (var i = 0, len=fieldnames.length;i<len;++i) {
    document.getElementById("td_"+fieldnames[i]).className="tdFadeOut";
    document.getElementById("img_"+fieldnames[i]).src="images/left_pro_arrow.gif";
  }
  for (var i = 0, len=datecasefields[config.currdate][config.currcase].length;i<len;++i) {
    document.getElementById("td_"+datecasefields[config.currdate][config.currcase][i]).className="tdNormal";
  }
  if (config.currfield && contains(datecasefields[config.currdate][config.currcase],config.currfield)) {
    document.getElementById("td_"+config.currfield).className="tdSelected";
    document.getElementById("img_"+config.currfield).src="images/left_pro_arrow_c.gif";
  }
}

function contains(a, obj) {
  var i = a.length;
  while (i--) {
    if (a[i] === obj) {
      return true;
    }
  }
  return false;
}

function toggleProduct(id) {
  var box = document.getElementById(id);
  if (box.style.visibility == "visible") {
    box.style.visibility = "hidden";
    box.style.display="none";
    document.getElementById("img_"+id).src="images/left_arrow.gif";
  } else {
    box.style.visibility = "visible";
    box.style.display="block";
    document.getElementById("img_"+id).src="images/down_arrow.gif";
  }
}

function goURL(dest) {
  var gdatey = config.currdate;
  var gcaset = config.currcase;
  var gfield = config.currfield;

  if (dest == 0) {
    var gcase = gcaset.substring(0,3);
    var gtime = gcaset.substring(3,5);
    var gday  = gdatey.substring(8,10);

    var cdarr  = gdatey.split("-");
    var fdstr  = cdarr.join("");

    /*
    var cdate = new Date(cdarr[0],cdarr[1],cdarr[2],0,0,0,0);  // forecast starting date
    var pdate = new Date(cdate.getTime()+48*60*60*1000);       // 48h forecast valid date
    var fdyr = pdate.getFullYear();
    var fdmn = "00"+pdate.getMonth();
    var ldmn = fdmn.length-2;
    var fddy = "00"+pdate.getDate();
    var lddy = fddy.length-2;
    var fdstr = pdate.getFullYear()+fdmn.substring(ldmn)+fddy.substring(lddy);
    */

    //var casestr = {'gfs': 'hurr1', 'ekf': 'hurr2'};
    var casestr = {'gfs': 'GSIA', 'ekf': 'EnKFA'};
    var urlstr = "http://www.caps.ou.edu/wx/p/"+fdstr+"/r/"+casestr[gcase]
               + "/wrf_"+gday+"."+gtime+"00Z/"+gtime+"00Z/";
    if (confirm("Go to \""+urlstr+"\"?")) window.location = urlstr;

  } else if (dest < 0) {

    var urlstr = "/ywang/hurricane1/index.php?date="+gdatey+"&case="+gcaset+"&field="+gfield;
    window.location = urlstr;

  } else {

    var urlstr = "/ywang/hurricane2/index.php?date="+gdatey+"&case="+gcaset+"&field="+gfield;
    window.location = urlstr;

  }
}
