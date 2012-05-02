/**
 * Adrian Soluch 2012
 * n0mad_10@yahoo.de
 */

//alert('TEST');

var socialrouter = (function(){

    var theDiv,
        script,
        JSONPurl = 'http://sr2.soluch.at/load?callback=socialrouter.callback';

    var createDiv = function() {

        if(document.getElementById('socialrouterMaindiv') !== null) {
            removeSR(); // div already exists - remove
        }

        theDiv = document.createElement("div");
        theDiv.id = "socialrouterMaindiv";
        theDiv.style.zIndex = 2147483647;

        //theDiv.innerHTML = "socialrouterMaindiv";
        document.body.appendChild(theDiv)

        callJSONP();
    }

    var callJSONP = function() {
        script = document.createElement('script');
        script.setAttribute('src', JSONPurl);
        document.getElementsByTagName('head')[0].appendChild(script);
    }

    var parseRequest = function(response) {
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
        document.getElementById("sr_closeButton").addEventListener("click", removeSR, false);
    }

    var removeSR = function() {
        var el = document.getElementById('socialrouterMaindiv');
        var remElement = (el.parentNode).removeChild(el);
    }

    return {
        init : createDiv,
        callback : parseRequest
    }
}());

socialrouter.init();

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
