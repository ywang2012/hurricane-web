/*
 * anim.js
 *
 * vim:syntax=javascript
 *
 * $Id: anim.js 1422 2010-05-10 15:17:05Z sdhill $
 *
 */

// Settings class
function Settings() {
    this.deffps     = 4;
    this.fps_max    = 30;
    this.fps_min    = 1;
    this.fps_step   = 1;
    this.delay_max  = 1000;
    this.waitimg    = true;
    this.jump_inc   = 1;
//    this.defimgurl  = null;
    this.defimgurl  = "unavailable.gif";
    this.div_frame  = null;
    this.slider     = null;

    this.init = function(imgframe,divframe,basephp,currdate,currtype,aslider) {
        this.basephp = basephp;
        this.setURL(currdate,currtype);
        this.image_frame = imgframe;
        this.div_frame = divframe;
        this.slider = aslider;
    }

    this.setURL = function(currdate,currtype) {
        this.listurl = this.basephp+"?date="+currdate+"&type="+currtype;
    }
}

// ImageCache class
function ImageCache(loadcallback, defimgsrc, waitimg) {
    this.imglist    = new Array();
    this.length     = 0;
    this.index      = -1;
    this.callback   = (loadcallback) ? loadcallback : function() {alert("ImageCache requires a load callback function");};
    this.loaddelay  = 100;
    this.loadtimeout= 15000;
    this.reqwith = null;
    this.reqheight = null;
    this.requrl = null;
    this.defimg     = (defimgsrc) ? new Image() : null;
    if(defimgsrc) {
        this.defimg.src = defimgsrc;
    }
    this.waitimg = (waitimg) ? true : false;
    this.updatehash = null;
    this.updatets   = new Date();

    this.reset = function() {
        this.imglist = new Array();
        this.length  = 0;
        this.index   = -1;
        this.updatehash = null;
        this.updates = new Date();
    }

    this.getImg     = function() {
        if(this.index >= 0 && this.index < this.length) {
            if(this.imglist[this.index].complete) {
                return this.imglist[this.index].src;
            }
        }
        if (this.length > 0) {
            return "loading.gif";
        } else {
            return (this.defimg) ? this.defimg.src : null;
        }
    }

    this.setImg     = function(i) {
        var now;
        var me = this;
        i = (!i) ? 0 : i;
        if(this.length <= 0) {
            this.index = -1;
//            return this.getImg();
            return;
        }
        if(i < 0 || i >= this.length) {
            this.index = 0;
            i= 0;
            if(this.callback) {
                this.callback(this);
            }
        }
        now = new Date();
        if(!this._loadimg(me, i, now.getTime() + this.loadtimeout) && this.waitimg) {
            return; // wait for img load
        }
        this.index = i;
        if(this.index + 1 < this.length) { // preload index + 1
            now = new Date();
            this._loadimg(me, this.index + 1, now.getTime() + this.loadtimeout);
        }
    }

    this.nextImg    = function(step) {
        step = (step && step > 0) ? step : 1;
        this.setImg(this.index + step);
    }

    this.prevImg    = function(step) {
        step = (step && step > 0) ? step : 1;
        this.setImg(this.index - step);
    }

    this._loadimg   = function(me, i, timeo) {
        if(i >= 0 && i < this.length) {
            var now;
            if(typeof this.imglist[i] == 'string') {
                var img = new Image();
                //alert(this.requrl+this.reqwidth)
                if (this.requrl && (this.reqwidth || this.reqheight)) {
                    var reqstr = null;
                    if (this.reqwidth) {
                        reqstr = "&width="+this.reqwidth;
                    }
                    if (this.reqheight) {
                        reqstr += "&height="+this.reqheight;
                    }
                    if (this.reqlevel) {
                        reqstr += "&level="+this.reqlevel;
                    }
                    if (this.reqwratio) {
                        reqstr += "&wratio="+this.reqwratio;
                    }
                    if (this.reqhratio) {
                        reqstr += "&hratio="+this.reqhratio;
                        //alert(this.requrl+"?file="+this.imglist[i]+reqstr);
                    }
                    img.src = this.requrl+"?file="+this.imglist[i]+reqstr;
                    this.imglist[i] = img;
                } else {
                    img.src = this.imglist[i];
                    this.imglist[i] = img;
                }
                now = new Date();
                setTimeout(function() {me._loadimg(i, timeo);}, Math.min(this.loaddelay, timeo - now.getTime()));
            } else if(this.imglist[i].complete) {
                return true;
            } else {
                now = new Date();
                if(now.getTime() >= timeo) {
                    if(this.defimg) {
                        this.imglist[i].src = this.defimg.src;
                        return true;
                    }
                } else if(!this.imglist[i].complete) {
                    now = new Date();
                    setTimeout(function() {me._loadimg(i, timeo);}, Math.min(this.loaddelay, timeo - now.getTime()));
                }
            }
        }
        return false;
    }

    this.updateImgList  = function(imglist, hash, ts) {
        // FIXME - implement this
        //       - selectively update this.imglist from imglist
        // update - if list changes, reload the whole thing, not efficient!
        // cheap alternative - assume that list is only appended to, old/previous entries never change
        if(hash && hash == this.updatehash) { return;  }
        this.updatehash = (hash) ? hash : null;
        this.updatets = (ts) ? new Date(ts * 1000) : new Date();
        this.imglist = (imglist) ? imglist : new Array();
        this.length = this.imglist.length;
        this.setImg(0);
    }
}

// main
var S           = new Settings();
var playing     = true;
var frametimer  = null;
var fps         = S.deffps;
var delay       = 1000.0/fps;
var req         = null;
var I           = new ImageCache(updateCallback, S.defimgurl, S.waitimg);


function initAnimation(divimage,divframe,ascript,adate,atype,aslider) {
    S.init(divimage,divframe,ascript,adate,atype,aslider);
    updateStatus();
    //alert(S.image_frame);
    requestList(false);
    handleRequest();
    //updateChangeRateSetting(S.deffps);
    nextFrame();
 }

// Update animation if either type or date changed
function updateAnimation(adate,atype) {
    if (playing) stopPlaying();
    playing = true;
    S.setURL(adate,atype);
    //I = new ImageCache(updateCallback, S.defimgurl, S.waitimg);
    //I.index = -1;
    //I.length = 0;
    I.reset();
    updateStatus();
    requestList(false);
    handleRequest();
    nextFrame();
}

function nextFrame() {
    showFrame();
    if (S.slider) S.slider.setValue(I.index+1);
    I.nextImg();
    if(playing) {
        frametimer = setTimeout("nextFrame()", delay);
    }
}

function showFrame() {
    var src = I.getImg();
    var msg;
    if(src) {
        document.images[S.image_frame].src = src;
        updateStatus();
    }
}

function updateCallback(imgcache) {
    if(handleRequest()) {
        requestList(true);
    }
}

function updateStatus() {
    if (S.div_frame) {
        if (S.div_frame[0]) {   // First frame number
            var e = document.getElementById(S.div_frame[0]);
            var msg = (I.index + 1) + ' of ' + I.length;
            //msg = msg || 'Loading...';
            if(e) {
                e.innerHTML = msg;
            }
        }

        if (S.div_frame[1]) {      // Second forecast time
            e = document.getElementById(S.div_frame[1]);
            if(e) {               // It is special for Hurricane forecast
                var startHour = parseInt(document.getElementById("status_date").innerHTML.substring(11,13))
                if (config.currfield == "comref" ||
                    config.currfield == "maxspd" ||
                    config.currfield == "maxsfc" ) {
                  var startMin = startHour*60;
                  var fmin = startMin + I.index*5;
                  var fhour = ( Math.floor(fmin/60) +24-6) % 24;
                  fmin = fmin % 60;
                  var fhstr = (fhour < 10)?"0"+fhour:fhour;
                  var fmstr = (fmin < 10)?"0"+fmin:fmin;
                  e.innerHTML = fhstr+":"+fmstr+" (CST)"
                } else {
                  var fhour = startHour + I.index;
                  fhour = (fhour+24-6) % 24;
                  var fhstr = (fhour < 10)?"0"+fhour:fhour;
                  e.innerHTML = fhstr+":00 (CST)";
                }
            }
        }
    }
}

/*
function updateRate(msg) {
    var e = document.getElementById('frame_rate_info');
    if(!e) {
        return;
    }
    if(!msg) {
        msg = "" + delay + " delay";
//
//        msg = "" + fps + "";
//        if(fps >= S.fps_max) {
//            msg = msg + " (max)";
//        }
//        if(fps <= S.fps_min) {
//            msg = msg + " (min)";
//        }
//        msg = msg + " fps";
//
    }
    e.firstChild.data = msg;
}
*/

/*
function updatePlaying(paused) {
    var e = document.getElementById('play_button');
    if(e) {
        if(paused) {
            e.value = 'Play';
        } else {
            e.value = 'Pause';
        }
    }
}
*/

function togglePlaying(slide) {
    if(playing) {
        stopPlaying(slide);
    } else {
        startPlaying();
    }
}

function startPlaying() {
    //updatePlaying(false);
    if(!playing) {
        playing = true;
        nextFrame();
    }
}

function stopPlaying(slide) {
    //updatePlaying(true);
    if(playing) {
        playing = false;
        clearTimeout(frametimer);
    }
    if (slide) setPlayButton();  // slide has already been handled
}

function jumpBegin() {
    stopPlaying();
    I.setImg(0);
    showFrame();
    if (S.slider) S.slider.setValue(I.index+1);
}

function jumpBack() {
    stopPlaying();
    I.prevImg();
    showFrame();
    if (S.slider) S.slider.setValue(I.index+1);
}

function jumpForward() {
    stopPlaying();
    I.nextImg();
    showFrame();
    if (S.slider) S.slider.setValue(I.index+1);

}

function jumpEnd() {
    stopPlaying();
    I.setImg(I.length-1);
    showFrame();
    if (S.slider) S.slider.setValue(I.index+1);
}

function setRate(r) {
    fps = (r > S.fps_max) ? S.fps_max : r;
    fps = (fps < S.fps_min) ? S.fps_min : fps;
    delay = 1000.0/fps;
    delay = (delay < 0 || fps >= S.fps_max) ? 0 : delay;
    delay = (delay > S.delay_max) ? S.delay_max : delay;
    //updateRate();
}

function incRate() {
    setRate(fps + S.fps_step);
}

function decRate() {
    setRate(fps - S.fps_step);
}

/*
 function changeRate() {
    var e = document.getElementById('frame_rate_setting');
    if(e) {
        var r = e.options[e.selectedIndex].value;
        setRate(r);
    }
}

function updateChangeRateSetting(r) {
    var e = document.getElementById('frame_rate_setting');
    if(e) {
        for(var i = 0; i < e.options.length; ++i) {
            if(e.options[i].value == r) {
                break;
            }
        }
        i = (i >= e.options.length) ? 0 : i;
        e.selectedIndex = i;
        setRate(r);
    }
}


function qualifyURL(url) {
    var e = document.createElement('div');
    function escHTML(s) {
        return s.split('&').join('&amp;').split('<').join('&lt;').split('"').join('&quot;');
    }
    e.innerHTML = '<a href="' + escHTML(url) + '">x</a>';
    return e.firstChild.href;
}
*/

function requestList(async) {
    req = new XMLHttpRequest();
    req.open('GET', S.listurl, async);
    req.send(null);
    return true;
}

function handleRequest() {
    if(req != null) {
        if(req.readyState == 4) {
            if(req.status != 200) {
                req.abort();
                req = null;
                return true;
            }
            var e = eval(req.responseText);
            if(filelist) {
                if(filelisthash) {
                    I.updateImgList(filelist, filelisthash);
                } else {
                    I.updateImgList(filelist);
                }
            }
            req = null;
            return true;
        } else {
            return false;
        }
    }
    return true;
}
