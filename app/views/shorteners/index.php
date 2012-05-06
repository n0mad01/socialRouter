<div class="container">
	<div class="row">
		<div class="twelvecol">
        <?php
//dumper( $this->data );
        echo '<a href="http://' . $_SERVER['HTTP_HOST'] . '/shorteners/bitly/">' . _('add a bitly account') . '</a><br />';
        foreach($this->data as $d) :

            if($d['service'] === 'bitly') :
                echo $d['username'] . ' ' . $d['apikey'] . '<a href="http://' . $_SERVER['HTTP_HOST'] . '/shorteners/remove/bitly/' . $d['username'] . '"> ' . _('remove') . '</a><br />';
            endif;
        endforeach;
        ?>
		</div>
	</div>
</div>
