/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    misc.js
 *
 * Created on Jul 01, 2011
 * Updated on Jul 01, 2011
 *
 * Description: Miscellaneous java scripts.
 *
 *
 */

// Toggle a HTML element to make it visible or invisible.
// Credits: http://www.dustindiaz.com/seven-togglers/
function $() {
	var elements = new Array();
	for (var i = 0; i < arguments.length; i++) {
		var element = arguments[i];
		if (typeof element == 'string')
			element = document.getElementById(element);
		if (arguments.length == 1)
			return element;
		elements.push(element);
	}
	return elements;
}
	
function toggle() {
	for ( var i=0; i < arguments.length; i++ ) {
		$(arguments[i]).style.display = ($(arguments[i]).style.display != 'none' ? 'none' : '' );
	}
}