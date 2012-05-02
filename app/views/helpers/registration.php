REGISTER
<form id="Register" accept-charset="utf-8" action="/users/register" method="post">
	<div class="input">
		<label for="email"><?php echo _('email address'); ?>:</label>
		<input id="email" type="text" maxlength="30" name="postdata[email]" 
		<?php echo ' value="' . $this->postdata['email'] . '"'; ?> />
	</div>
	<?php if (isset($this->errorMsg['invalid']['email'])) {
			echo '<div class="errormsg">' . $this->errorMsg['invalid']['email'] . '</div>';
	} ?>
	<div class="input">
		<label for="password"><?php echo _('Password'); ?>:</label>
		<input id="password" type="password" name="postdata[password]" />
	</div>
	<div class="input">
		<label for="password2"><?php echo _('Password repeat'); ?>:</label>
		<input id="password2" type="password" name="postdata[password2]" />
	</div>
	<?php if (isset($this->errorMsg['invalid']['passwords'])) {
			echo '<div class="errormsg">' . $this->errorMsg['invalid']['passwords'] . '</div>';
	} ?>
    <div class="checkbox">
    	<label for="businessTerms">
            <?php echo _('I agree to the'); ?>
            <a href="/terms/" target="_blank"><?php echo _('Terms of use'); ?></a>
        </label>
    	<input id="businessTerms" type="checkbox" name="postdata[businessTerms]" checked="checked" />
	    <?php if (isset($this->errorMsg['invalid']['businessTerms'])) {
			echo '<div class="errormsg">' . $this->errorMsg['invalid']['businessTerms'] . '</div>';
    	} ?>
    	<?php //echo '<span style="font-size:10px;">' . _('(until the end of time)') . '</span>'; ?>
    </div>
	<input type="submit" value="<?php echo _('sign up'); ?>"/>
</form>
