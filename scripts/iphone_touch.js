// TOUCH-EVENTS SINGLE-FINGER SWIPE-SENSING JAVASCRIPT
// this script can be used with one or more page elements to perform actions based on them being swiped with a single finger

function ios_touch() {
			this.minLength = 72; // the shortest distance the user may swipe

			this.triggerElementID = null; // this variable is used to identity the triggering element
			this.fingerCount = 0;
			this.startX = 0;
			this.startY = 0;
			this.curX = 0;
			this.curY = 0;
			this.deltaX = 0;
			this.deltaY = 0;
			this.horzDiff = 0;
			this.vertDiff = 0;
			this.swipeLength = 0;
			this.swipeAngle = null;
			this.swipeDirection = null;

			this.reset = function() {
				// reset the variables back to default values
				this.fingerCount = 0;
				this.startX = 0;
				this.startY = 0;
				this.curX = 0;
				this.curY = 0;
				this.deltaX = 0;
				this.deltaY = 0;
				this.horzDiff = 0;
				this.vertDiff = 0;
				this.swipeLength = 0;
				this.swipeAngle = null;
				this.swipeDirection = null;
				this.triggerElementID = null;
			}

      this.caluculateAngle = function () {
      	var X = this.startX-this.curX;
      	var Y = this.curY-this.startY;
      	var Z = Math.round(Math.sqrt(Math.pow(X,2)+Math.pow(Y,2))); //the distance - rounded - in pixels
      	var r = Math.atan2(Y,X); //angle in radians (Cartesian system)
      	this.swipeAngle = Math.round(r*180/Math.PI); //angle in degrees
      	if ( this.swipeAngle < 0 ) { this.swipeAngle =  360 - Math.abs(this.swipeAngle); }

      }

      this.determineSwipeDirection = function () {
      	if ( (this.swipeAngle <= 45) && (this.swipeAngle >= 0) ) {
      		this.swipeDirection = 'left';
      	} else if ( (this.swipeAngle <= 360) && (this.swipeAngle >= 315) ) {
      		this.swipeDirection = 'left';
      	} else if ( (this.swipeAngle >= 135) && (this.swipeAngle <= 225) ) {
      		this.swipeDirection = 'right';
      	} else if ( (this.swipeAngle > 45) && (this.swipeAngle < 135) ) {
      		this.swipeDirection = 'down';
      	} else {
      		this.swipeDirection = 'up';
      	}
      }

      // The 4 Touch Event Handlers

      // NOTE: the touchStart handler should also receive the ID of the triggering element
      // make sure its ID is passed in the event call placed in the element declaration, like:
      // <div id="picture-frame" ontouchstart="touchStart(event,'picture-frame');"  ontouchend="touchEnd(event);" ontouchmove="touchMove(event);" ontouchcancel="touchCancel(event);">

      this.touchStart = function (event,passedName) {
      	// get the total number of fingers touching the screen
      	this.fingerCount = event.touches.length;
      	// since we're looking for a swipe (single finger) and not a gesture (multiple fingers),
      	// check that only one finger was used
      	if ( this.fingerCount == 1 ) {
        	// disable the standard ability to select the touched object
        	event.preventDefault();
      		// get the coordinates of the touch
      		this.startX = event.touches[0].pageX;
      		this.startY = event.touches[0].pageY;
      		// store the triggering element ID
      		this.triggerElementID = passedName;
      	} else {
      		// more than one finger touched so cancel
      		this.reset();
      	}
      }

      this.touchMove = function (event) {
      	if ( event.touches.length == 1 ) {
        	event.preventDefault();
      		this.curX = event.touches[0].pageX;
      		this.curY = event.touches[0].pageY;
      	} else {
      		this.reset();
      	}
      }

      this.touchEnd = function (event) {
      	if ( this.fingerCount == 1 ) {
        	event.preventDefault();
        	// check to see if more than one finger was used and that there is an ending coordinate
      		if ( this.curX == 0 ) {
             this.swipeDirection = 'none';  // touch only
      		} else {
      		  // use the Distance Formula to determine the length of the swipe
      		  this.swipeLength = Math.round(Math.sqrt(Math.pow(this.curX - this.startX,2) + Math.pow(this.curY - this.startY,2)));
      		  // if the user swiped more than the minimum length, perform the appropriate action
      		  if ( this.swipeLength >= this.minLength ) {
      		  	this.caluculateAngle();
      		    this.determineSwipeDirection();
      		  }
      		}
      		processingTouchRoutine();
      		this.reset(); // reset the variables
      	} else {
      		this.reset();
      	}
      }

      this.touchCancel = function (event) {
      	// reset the variables back to default values
      	this.reset();
      }
}
