/* vim:set ft=javascript: */

/*<![CDATA[*/

jQuery.noConflict();  

jQuery(document).ready(function(){

	// change twitter account
	jQuery('#changeTwitterAccount').change(function(){
		document.location.href="/twitter/account/" + jQuery(this).val(); 
	}); 

	// remove twitter account ok/no?
	// TODO: i18n !
	jQuery('#removeTwitterAccounts input').click(function(e){
		if (!confirm('<?php echo _('Do you really want to remove\n'); ?>'+' '+jQuery('#removeTwitterAccount').val()+'?')) { e.preventDefault(); }
		//alert(jQuery('#removeTwitterAccount').val());
	});

	jQuery('#loginbox_opener').toggle(function( event ){
        jQuery('#loginbox').fadeIn();
        if ( event.preventDefault ) event.preventDefault(); event.returnValue = false;
    }, function() {
        jQuery('#loginbox').fadeOut();
        if ( event.preventDefault ) event.preventDefault(); event.returnValue = false;
    });
});

// ARRAY DUMPER
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
/*]]>*/
