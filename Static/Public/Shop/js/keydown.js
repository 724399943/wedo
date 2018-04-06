/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2015-12-18 16:25:59
 * @version $Id$
 */
var bFlash = (function() {
	var version = ($try(function() {
		return navigator.plugins['Shockwave Flash'].description;
	}, function() {
		return new ActiveXObject('ShockwaveFlash.ShockwaveFlash').GetVariable('$version');
	}) || '0 r0').match(/\d+/g);
	return (parseInt(version[0] || 0 + '.' + version[1], 10) || 0) >= 9;
})();

var oPass = document.getElementById("password");
var oTips = document.getElementById("tips");
var oSwf = null;
var sLetters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
var iMode = 0; //0未知，1小写，2大写 

function $try() {
	for (var i = 0, l = arguments.length; i < l; i++) {
		try {
			return arguments[i]();
		} catch (e) {}
	}
	return null;
}

function getSwf() {
	if (navigator.appName.indexOf("Microsoft") != -1)
		oSwf = window["capslock"];
	else
		oSwf = document["capslock"];
	oPass.disabled = false;
}

function warning() {
	// oTips.innerHTML = "请注意，您的大写锁定键已打开！";
	var t = $('#password').offset().top;
	var l = $('#password').offset().left;
	$('#tips').css({'top':t-44,"left":l-40});
	oTips.style.display = "block";
}

function hide() {
	oTips.style.display = "none";
}

oPass.onfocus = function() {
	if (oSwf) {
		//oSwf.focus();
		//oSwf.getCapslock();
		//oPass.focus();
	}
}
oPass.onkeydown = function(event) {
	event = event || window.event;
	var keyCode = event.which || event.keyCode;
	if (keyCode == 20) {
		if (bFlash) {
			//oSwf.focus();
			//oSwf.getCapslock();
			oPass.focus();
		} else if (iMode == 1) {
			warning();
		} else if (iMode == 2) {
			hide();
		}
	}
}
oPass.onkeypress = function(event) {
	if (!oSwf) {
		event = event || window.event;
		var origin = oPass.value;
		var c;
		setTimeout(function() {
			var now = oPass.value;
			for (var i = 0, length = Math.min(origin.length, now.length); i < origin; i++) {
				if (origin.charAt(i) !== now.charAt(i)) {
					c = now.charAt(i);
					break;
				}
			}
			if (!c) {
				c = now.charAt(now.length - 1);
			}
			if (sLetters.indexOf(c) > -1) {
				if (event.shiftKey) {
					iMode = 1;
					hide();
				} else {
					iMode = 2;
					warning();
				}
			} else if (sLetters.toLowerCase().indexOf(c) > -1) {
				if (event.shiftKey) {
					iMode = 2;
					warning();
				} else {
					iMode = 1;
					hide();
				}
			}
		}, 0);
	}
}

if (!bFlash) {
	oPass.disabled = false;
}
