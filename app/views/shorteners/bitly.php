<div class="container">
	<div class="row">
		<div class="twelvecol">
            <p>
                In order to use your bit.ly account please provide your username and API key.<br />
                (Your API key can be obtained here: <a href="http://bitly.com/a/your_api_key">http://bitly.com/a/your_api_key</a>)
            </p>
            <form id="shortener_bitly" accept-charset="utf-8" action="/shorteners/bitly" method="post">
            	<div class="input">
            		<label for="bitly_username"><?php echo _('bitly Username'); ?>:</label>
            		<input id="bitly_username" type="text" maxlength="30" name="postdata[bitly_username]" 
            		<?php echo ' value="' . $this->postdata['bitly_username'] . '"'; ?> />
            	</div>
            	<div class="input">
            		<label for="bitly_api_key"><?php echo _('bitly API Key'); ?>:</label>
            		<input id="bitly_api_key" type="text" maxlength="255" name="postdata[bitly_api_key]" 
            		<?php echo ' value="' . $this->postdata['bitly_api_key'] . '"'; ?> />
            	</div>
            	<?php if (isset($this->errorMsg['invalid']['error'])) :
            			echo '<div class="errormsg">' . $this->errorMsg['invalid']['error'] . '</div>';
            	endif; ?>
            	<input type="submit" value="<?php echo _('add'); ?>"/>
            </form>
		</div>
	</div>
</div>
