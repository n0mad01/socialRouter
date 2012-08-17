/* vim:set ft=javascript: */

<?php
//header('Content-Type: text/javascript; charset=utf8');
//header('Access-Control-Allow-Origin: http://www.soluch.at/');
//header('Access-Control-Max-Age: 3628800');
//header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

//$content = file_get_contents(__DIR__ . '/divcontent.php');
require(__DIR__ . '/divcontent.php');

//dumper($this->referer);die();
//$html = new Html;
//$content = $html->getHTML();
//$options = '';
if( isset($this->twitterAccounts) ) :
    $options = new stdClass;
    $options->shorturl = $this->referer;
    $options->twitter = $this->twitterAccounts;
    $options->shortener = $this->shortenerAccounts;
endif;

$content = Html::getHTML($options);

$cookies = $_COOKIE;

/*$data = array(
    'content'=>$content,
    'cookies'=>$cookies,
    'shorturl'=>$this->referer
);*/
//echo $_GET['callback'] . '(' . json_encode($data) . ');';
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

        //theDiv.innerHTML = 'SOCIALROUTERB';
        theDiv.innerHTML = '<?php echo $content; ?>';
        
        // SET GLOBALS*
        textarea = document.getElementById('sr_textarea');
        charCountBox = document.getElementById('sr_charCount');
        //content = textarea.value;

        textarea.value = document.title + "\n" + textarea.value; //content //document.location.href;
        charCountSR();

        theDiv.onmousedown = dragDown;

        callListeners();
    }

    var callListeners = function() {
        document.getElementById("sr_closeButton").addEventListener("click", removeSR, false);
        textarea.onkeyup = charCountSR;

        document.getElementById("submitSocial").addEventListener("click", submitSocial, false);
    }

    var callJSONP = function( string )
    {
        //JSONPurl = 'http://sr2.soluch.at/load/delegateMessageJSONP?callback=socialrouter.callback';
        JSONPurl = 'http://sr2.soluch.at/load/delegateMessageJSONP?callback=socialrouter.callback&' + string ;
        //alert(JSONPurl);

        script = document.createElement('script');
        script.setAttribute( 'src', JSONPurl );
        document.getElementsByTagName('head')[0].appendChild(script);
    }

    var parseRequest = function(response) {

        document.getElementById('socialRouter_main').innerHTML = response.html;
        
        //for (var username in response.user ) {
            //alert( username );
        //}
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
            //j = 0;

        string += 'textarea=' + encodeURIComponent( document.getElementById("sr_textarea").value );
        string += '&twitterUsers=';
        
        for( i=0; i<l; i++) {
            if( elem[i].checked == true ) {
                //string += 'twitterUser[' + j + ']=' + encodeURIComponent( elem[i].value ) + "&"; 
                users += encodeURIComponent( elem[i].value ) + '|';
                //j++;
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

    return {
        init : createDiv,
        callback : parseRequest
    }
}());

socialrouter.init();


function dragDown(e){
  e = (e ? e : event);
  //var top  = (isNaN(parseInt(this.style.top))  ? 0 : this.style.top);
  var top  = (isNaN(parseInt(this.style.top))  ? 0 : this.style.top);
  var left = (isNaN(parseInt(this.style.left)) ? 0 : this.style.left);
//alert(this.style.bottom);
  var y = Math.abs(parseInt(top) - e.clientY);
  var x = Math.abs(parseInt(left) - e.clientX);

  var oldCursor = this.style.cursor;
  this.style.cursor = "move";

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
