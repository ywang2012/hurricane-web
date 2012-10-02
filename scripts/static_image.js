var imgwidth =  null;
var imgheight = null;
var imgmgtop = null;
var imgmglft = null;
var zoomlevel = 0;

var image1 = new Image();
var block1 = null;
var imgsrc = null;
var imgbig = null;
var sizetop = 200;
var sizelft = 320;

function initStatic() {
    var bigRe = new RegExp("((gfs|ekf)(00|12)Z)")
    image1 = document.images[S.image_frame];
    imgsrc = image1.src
    imgbig = imgsrc.replace(bigRe,"$1_big")
    //alert(imgsrc+"\n"+imgbig)

    block1 = document.getElementById('div_frames')
    //alert(zoomlevel);
    if (!zoomlevel) {
        imgwidth =  image1.width;
        imgheight = image1.height;
        imgmgtop  = imgtopmg;
        imgmglft  = imglftmg;
        zoomlevel = 1.0;
    }

    //orgmargin = image1.style.margin;
    //alert(orgwidth);
}

function realsize()
{
    image1.src = imgsrc;
    image1.style.zoom = "100%";
    image1.height=imgheight;
    image1.width=imgwidth;
    block1.style.position="relative";
    block1.style.left = 0;
    block1.style.top = 0;
    block1.style.width=orgwidth+"px";
    block1.style.height=orgheight+"px";
    block1.style.overflow="hidden";
    block1.style.border="0px solid black";
    image1.style.marginTop  = imgtopmg+"px";
    image1.style.marginLeft = imglftmg+"px";
    image1.style.border="0px";
    block1.scrollTop  = 0;
    block1.scrollLeft = 0;
    imgmgtop = imgtopmg;
    imgmglft = imglftmg;
    sizetop = 200;
    sizelft = 320;
    zoomlevel = 1;

    //left:306px; top: 180px
    var divitem = document.getElementById("static_frame");
    divitem.style.top  = block1.offsetTop+20+'px';
    divitem.style.left = block1.offsetLeft+'px';

}

function bigit(){
    zoomlevel *= 1.5;

    var height1 = Math.ceil(image1.height*zoomlevel);
    var width1  = Math.ceil(image1.width*zoomlevel);
    imgmgtop = Math.ceil(imgmgtop*zoomlevel);
    imgmglft = Math.ceil(imgmglft*zoomlevel);

    image1.src  = imgbig;
    image1.height = height1;
    image1.width  = width1;
    image1.style.marginTop  = imgmgtop+"px";
    image1.style.marginLeft = imgmglft+"px";
    var cwidth = window.innerWidth || document.body.clientWidth;
    var cheight = window.innerHeight || document.body.clientHeight;
    cwidth -= sizelft;
    cheight -= sizetop;
    block1.style.width=cwidth+"px";
    block1.style.height=cheight+"px";
    //alert(block1.style.height+","+sizetop)
    if (height1 > orgheight || width1 > orgwidth) {
        block1.style.overflow='auto';
    }
}

function smallit(){
    zoomlevel *= 0.9;
    var height1= Math.ceil(image1.height*zoomlevel);
    var width1 = Math.ceil(image1.width*zoomlevel);
    imgmgtop = Math.ceil(imgmgtop*zoomlevel);
    imgmglft = Math.ceil(imgmglft*zoomlevel);

    //image1.src  = imgsrc;
    image1.height=height1;
    image1.width=width1;
    image1.style.marginTop  = imgmgtop+"px";
    image1.style.marginLeft = imgmglft+"px";
    if (height1 <= orgheight || width1 <= orgwidth)
        block1.style.overflow="hidden";
}

function setScreenOk(){
    var w = parent.document.documentElement.clientWidth-10;
    var h = parent.document.documentElement.clientHeight-10;
		var divitem = document.getElementById("static_frame");

    /*
    //alert(w+", "+h);
    var r1 = w/orgwidth;
    var r2 = h/orgheight;
    var r = (r1>r2)?r2:r1;
    var w1 = r*orgwidth;
    var h1 = r*orgheight;
    image1.src = imgbig;
    image1.width = w1 ;
    image1.height = h1 ;
    block1.style.width=(w1+2) + 'px';
    block1.style.height=(h1+2) + 'px';
    block1.style.position="absolute";
    var w2 = (w-w1)/2 ;
    var h2 = (h-orgheight)/4 - 40;
    block1.style.top = 5+'px';
    block1.style.left = ((w2>0)?w2:10 ) + 'px';
    //alert(w2+", "+h2);
    image1.style.margin="0 0 0 0";
    image1.style.border="1px solid black";

		divitem.style.top = "40px";
		divitem.style.left = ( ( (w2>0)?w2:10 )+30 )+"px";

    zoomlevel = r;
    */

    image1.src = imgbig;
    image1.width = 1500 ;
    image1.height =1500 ;

    var w1 = w;
    var h1 = h;
    var t1 = 2;
    var l1 = 2;
    var st = 320;
    var sl = 140;
    sizetop = 0;   sizelft = 0;

    if (w > 1518) {
      w1 = 1517;
      l1 = Math.floor((w-w1)/2.);
      sl = 0;
      sizelft = (w-w1);
    }

    if (h > 1518) {
      h1 = 1517;
      t1 = Math.floor((h-h1)/2.0);
      st = 0;
      sizetop = h-h1;
    }

    block1.style.width= w1 + 'px';
    block1.style.height=h1 + 'px';
    block1.style.position="absolute";
    block1.style.top =  t1+'px';
    block1.style.left = l1+'px';
    image1.style.margin="0 0 0 0";
    block1.style.border="1px solid black";
    block1.style.overflow="auto";
    //alert(st+','+block1.scrollTop);

    divitem.style.top = (t1+20)+"px";
    divitem.style.left = (l1+20)+"px";

    zoomlevel = 1;
    imgmgtop  = 0; imgmglft  = 0;
    image1.focus();

    block1.scrollLeft=sl;
    block1.scrollTop=st;

    return;
}
