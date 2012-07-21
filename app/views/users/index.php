<div class="container">
	<div class="row">
		<div class="twelvecol secondMenu">
            <ul id="secondMenu">
                <li>first
                    <div class="hidden secondMenuFirstHidden">hidden</div>
                </li>
		    </ul>
		</div>
		<div class="twelvecol">
<?php
            foreach( $this->viewdata['accounts'] as $data) :
                //dumper($data);
                if($data['service'] === 'twitter') :
                    echo '<img alt="userimage ' . $data['username'] . '" src="' . $data['image'] . '" class="twitterimage" />';
                    echo $data['username'];
                    echo ' created on: ';
                    $date = new DateTime($data['created']);
                    echo $date->format('Y.m.d');
                    echo '<br />';
                endif;
            endforeach;
?>
		</div>
	</div>
</div>
