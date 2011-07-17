/*
 * Title:   Snuffel
 * Author:  Qzofp Productions
 * Version: 0.4
 *
 * File:    misc.js
 *
 * Created on Jul 01, 2011
 * Updated on Jul 17, 2011
 *
 * Description: Miscellaneous java scripts.
 *
 *
 */

/*
 * Function:	$
 *
 * Created on Jul 09, 2011
 * Updated on Jul 09, 2011
 *
 * Description: Dollar function.
 * 
 * Credits: http://www.dustindiaz.com/seven-togglers/
 *
 * In:	-
 * Out:	elements
 *
 */
function $() 
{
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

/*
 * Function:	toggle
 *
 * Created on Jul 09, 2011
 * Updated on Jul 09, 2011
 *
 * Description: Toggle a HTML element to make it visible or invisible.
 * 
 * Credits: http://www.dustindiaz.com/seven-togglers/
 *
 * In:	-
 * Out:	-
 *
 */
function toggle() 
{
    for ( var i=0; i < arguments.length; i++ ) {
	$(arguments[i]).style.display = ($(arguments[i]).style.display != 'none' ? 'none' : '' );
    }
}


/*
 * Function:    checkclear
 *
 * Created on Jul 09, 2011
 * Updated on Jul 09, 2011
 *
 * Description: Checks if a input text box has cleared and change the font color and style.
 *
 * In:	what
 * Out:	-
 *
 */
function checkclear(what)
{
    if(!what._haschanged)
    {
        what.value=''
        what.style.color='#000'
        what.style.fontStyle='normal'
    };
    what._haschanged=true;
}

/*
 * Function:    wopen
 *
 * Created on Jul 16, 2011
 * Updated on Jul 16, 2011
 *
 * Description: Open a new window in the cewnter of the page.
 * 
 * Credits: http://www.boutell.com/newfaq/creating/windowcenter.html
 *
 * In:	url, name, w, h
 * Out:	window
 *
 */
function wopen(url, name, w, h)
{
  // Fudge factors for window decoration space.
  // In my tests these work well on all platforms & browsers.
  w += 32;
  h += 96;
  wleft = ((screen.width - w) / 2) + 40;
  wtop = ((screen.height - h) / 2) - 84;

  var win = window.open(url,
    name,
    'width=' + w + ', height=' + h + ', ' +
    'left=' + wleft + ', top=' + wtop + ', ' +
    'location=no, menubar=no, ' +
    'status=no, toolbar=no, scrollbars=no, resizable=no');
  // Just in case width and height are ignored
  win.resizeTo(w, h);
  // Just in case left and top are ignored
  win.moveTo(wleft, wtop);
  win.focus();
 
  return win;
}

/*
 * Function:    nzb
 *
 * Created on Jul 16, 2011
 * Updated on Jul 17, 2011
 *
 * Description: Download nzb.
 * 
 * Credits: http://www.atashbahar.com/post/Detect-when-a-JavaScript-popup-window-gets-closed.aspx
 *
 * In:	id
 * Out: 
 *
 */
function nzb(id) 
{
    $('h'+id).innerHTML = "<img src=\"img/loading.gif\" />";

    var url = "nzb.php?id="+id;
 
    var win = wopen(url, 'NZB', 368, 195)
    var timer = setInterval(function() {   
        if(win.closed) {  
            clearInterval(timer);  
            $('h'+id).innerHTML = "<img src=\"img/tick.png\" />";
            //alert('closed');
        }  
    }, 100);  
}
