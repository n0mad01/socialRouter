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
$options = '';
if(isset($this->twitterAccounts)) :
/*    $options = array(
        'twitter'=>$this->twitterAccounts,
        'shortener'=>$this->shortenerAccounts,
        'shorturl'=>$this->referer,
    );*/
    
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
        content;

    var createDiv = function() {

        if(document.getElementById('socialrouterMaindiv') !== null) {
            removeSR(); // div already exists - remove
        }

        theDiv = document.createElement("div");
        theDiv.id = "socialrouterMaindiv";
        theDiv.style.zIndex = 2147483647;

        //theDiv.innerHTML = "socialrouterMaindiv";
        document.body.appendChild(theDiv)

        //theDiv.innerHTML = 'SOCIALROUTERB';
        theDiv.innerHTML = '<?php echo $content; ?>';
        //callJSONP();
        
        theURL = document.getElementById('sr_theURL');
        content = theURL.value;
        theURL.value = document.title + "\n" + content //document.location.href;

        callListeners();
    }

    var callListeners = function() {
        document.getElementById("sr_closeButton").addEventListener("click", removeSR, false);
    }

/*    var callJSONP = function() {
        script = document.createElement('script');
        script.setAttribute('src', JSONPurl);
        document.getElementsByTagName('head')[0].appendChild(script);
    }*/

/*    var parseRequest = function(response) {
        // inject the new elements
        theDiv.innerHTML = response.content;
 
        theURL = document.getElementById('sr_theURL');
        theURL.value = document.title + "\n" + response.shorturl  //document.location.href;

        otherContent = document.getElementById('otherContent');
        //otherContent.innerHTML = dump(response.cookies);
        //otherContent.innerHTML = response.token;
        otherContent.innerHTML = response.cookies.logintoken;
        //otherContent.innerHTML = dump(response.cookies);

        // Close button
        //document.getElementById("sr_closeButton").addEventListener("click", removeSR, false);
    }*/

    var removeSR = function() {
        var el = document.getElementById('socialrouterMaindiv');
        var remElement = (el.parentNode).removeChild(el);
    }

    return {
        init : createDiv,
        //callback : parseRequest
    }
}());

socialrouter.init();



// HELPER FUNCTIONS
// OBJECT DUMPER HELPER
function dump(arr,level) {
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
}
