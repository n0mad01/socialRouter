<?php
//print_r($this->getErrorMsg());
//echo $this->getFlashMsg();
//print_r($this->errorMsg);
//print_r($this->postdata);
?>
<div class="container">
	<div class="row">
		<div class="eightcol">
<?php
            if( $this->isAuth() ) :

                echo '<a href="twitter/add">' . _('Add a new twitter Account') . '</a>';
                echo '<br />';
                echo '<a href="shorteners/add/bitly">' . _('Add a new bitly Account') . '</a>';
                echo '<br />';

                foreach( $this->viewdata['services'] as $service ) :
                //dumper($service);
                    echo '<img alt="" src="' . $service['image'] . '" />';
                endforeach;
            else :
                    echo '<p>';
                        echo 'drag this to your bookmarks or bookmarks toolbar:';
                        require('app/views/helpers/bookmark.php');
                    echo '</p>';
            endif;
?>
		</div>
		<div class="fourcol last">
<?php 
            if(!$this->isAuth()) :

                require('app/views/helpers/registration.php');
            else :
                echo '<p>';
                    echo 'drag this to your bookmarks or bookmarks toolbar:';
                    require('app/views/helpers/bookmark.php');
                echo '</p>';
            endif; 
?>
		</div>
	</div>
</div>
<div class="container">
	<div class="row">
		<div class="threecol">
		</div>
		<div class="sixcol">
		</div>
		<div class="threecol last">
		</div>
	</div>
</div>
