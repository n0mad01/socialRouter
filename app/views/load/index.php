/* vim:set ft=javascript: */

<?php
require(__DIR__ . '/divcontent.php');

$options = new stdClass;
if( isset($this->twitterAccounts) ) :
    $options->shorturl = $this->referer;
    $options->twitter = $this->twitterAccounts;
    $options->shortener = $this->shortenerAccounts;
else :
endif;

$content = Html::getHTML($options);

$cookies = $_COOKIE;
?>

// JavaScript
var socialrouter = (function(){

    var theDiv,
        script,
        textarea,
        charCountBox,
        content;

    var createDiv = function() {

        if(document.getElementById('socialrouterMaindiv') !== null) {
            removeSR(); // if div already exists - remove
            return;
        }

        theDiv = document.createElement("div");
        theDiv.id = "socialrouterMaindiv";
        theDiv.style.zIndex = 2147483647;

        //theDiv.innerHTML = "socialrouterMaindiv";
        document.body.appendChild(theDiv)

        theDiv.innerHTML = '<?php echo $content; ?>';
        
        // SET GLOBALS*
        textarea = document.getElementById('sr_textarea');
        charCountBox = document.getElementById('sr_charCount');
        //content = textarea.value;

        if( textarea ) {
            textarea.value = document.title + "\n" + textarea.value; //content //document.location.href;
            charCountSR();
        }

        theDiv.onmousedown = dragDown;

        callListeners();
    }

    var callListeners = function() {
        document.getElementById("sr_closeButton").addEventListener("click", removeSR, false);

        if( textarea ) { 
            textarea.onkeyup = charCountSR;
        }

        var submitSoc = document.getElementById("submitSocial");
        if( submitSoc ) {
            submitSoc.addEventListener("click", submitSocial, false);
        }

        var submitLog = document.getElementById("submitLogin");
        if( submitLog ) {
            submitLog.addEventListener("click", submitLogin, false);
        }
    }

    var callJSONP = function( string ) {
        JSONPurl = 'http://sr2.soluch.at/load/delegateMessageJSONP?callback=socialrouter.callback&' + string ;

        script = document.createElement('script');
        script.setAttribute( 'src', JSONPurl );
        document.getElementsByTagName('head')[0].appendChild(script);
    }

    var loginJSONP = function( string ) {
        JSONPurl = 'http://sr2.soluch.at/users/login/jsonp?callback=socialrouter.logincallback' + string;

        script = document.createElement('script');
        script.setAttribute( 'src', JSONPurl );
        document.getElementsByTagName('head')[0].appendChild(script);
    }

    var parseRequest = function( response ) {

        document.getElementById('socialRouter_main').innerHTML = response.html;
        
    }

    var parseLogin = function( response ) {
        if( response.login ) {
            removeSR();
        }
        else {
            alert('Please try again!');
        }
    }

    var charCountSR = function() {
        //if(textarea.value.length > 140) {
            //charCountBox.style.fontSize = 12px;
        //}
        charCountBox.innerHTML = textarea.value.length;
    }

    var removeSR = function() {
        var el = document.getElementById('socialrouterMaindiv');
        el.parentNode.removeChild(el);
    }

    var submitSocial = function( event ) {
        // get data from form & put GET string together
        var string = "", 
            elem = document.getElementsByName('postdata[twitterUser][]'),
            l = elem.length,
            users = '';

        string += 'textarea=' + encodeURIComponent( document.getElementById("sr_textarea").value );
        string += '&twitterUsers=';
        
        for( i=0; i<l; i++) {
            if( elem[i].checked == true ) {
                users += encodeURIComponent( elem[i].value ) + '|';
            }
        }
        if( users != '' ) {
            string += users.slice(0, -1);
            string += '&shortener=' + document.getElementsByName('postdata[shortener]')[0].value;

            callJSONP( string );
        }
        else {
            alert('Please choose at least one Twitter Account!');
        }

        if ( event.preventDefault ) event.preventDefault();
        event.returnValue = false;
    }

    // JSONP Login
    var submitLogin = function( event ) {
        var string = ""; 
        string += '&email=' + document.getElementById('socialrouter_email').value;
        string += '&password=' + document.getElementById('socialrouter_password').value;
//alert(string);

        loginJSONP( string );

        if ( event.preventDefault ) event.preventDefault();
        event.returnValue = false;
    }

    return {
        init : createDiv,
        callback : parseRequest,
        logincallback : parseLogin
    }
}());

socialrouter.init();

// DRAG N DROP
var dragDown = function(e)
{
    // fixate height
    var h = this.clientHeight;
    this.style.height = ( h - 10 ) + 'px';

    e = (e ? e : event);
    var top  = ( isNaN( parseInt( this.offsetTop ) )  ? 0 : this.offsetTop );
    var left = ( isNaN( parseInt( this.offsetLeft ) ) ? 0 : this.offsetLeft );

    var y = Math.abs( parseInt( top ) - e.clientY );
    var x = Math.abs( parseInt( left ) - e.clientX );

    var oldCursor = this.style.cursor;
    this.style.cursor = 'move';

    var oldMousemove = document.onmousemove;
    var oldMouseup   = document.onmouseup;
    document.onmousemove = dragMakeMoveFunc(this, y, x);
    document.onmouseup   = dragMakeStopFunc(this, oldMousemove, oldMouseup, oldCursor);
}

function dragMakeMoveFunc(elem, y, x){
    return function(e){
        e = (e ? e : event);
          elem.style.top  = (e.clientY - y) + 'px';
          elem.style.left = (e.clientX - x) + 'px';
    }
}

function dragMakeStopFunc(elem, oldMousemove, oldMouseup, oldCursor){
    return function(){
        document.onmousemove  = oldMousemove;
        document.onmouseup    = oldMouseup;
        elem.style.cursor     = oldCursor;
    }
}

// HELPER FUNCTIONS
// OBJECT DUMPER HELPER
/*function dump(arr,level) {
	var dumped_text = "";
	if(!level) level = 0;

	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";

		if(typeof(arr) == 'object') { //Array/Hashes/Objects
			for(var item in arr) {
				var value = arr[item];

				if(typeof(value) == 'object') { //If it is an array,
					dumped_text += level_padding + "'" + item + "' ---<br />";
					dumped_text += dump(value,level+1);
				} else {
					dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
				}
			}
		} else { //Stings/Chars/Numbers etc.
			dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
		}
	return dumped_text;
}*/
