function autoUpdates(delay) {
    var req = new ajaxRequest()
    var nocache = "?nocache="+Math.random()*1000000
    req.open("GET","updatecase.php"+nocache,true)
    req.onreadystatechange = function() {
        if (this.readyState == 4) {
            if (this.status == 200) {
                if (this.responseText != null) {
                    //alert(config.currdate+this.responseText)
                    eval(this.responseText)                
                    setTDtype()
                }               
            }
        }
    }
    req.send(null)
    setTimeout("autoUpdates()",config.updateDelay)
}

function ajaxRequest() {
    try {
        var request = new XMLHttpRequest()
    } catch (e1) {
        try {
            request = new ActiveXObject("Msxml2.XMLHTTP")
        } catch (e2) {
            try {
                request = new ActiveXObject("Microsoft.XMLHTTP")
            } catch (e3) {
                request = false
            }
        }
    }
    return request
}