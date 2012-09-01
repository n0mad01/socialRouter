<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>SocialRouter social distribution tool by Adrian Soluch 2012</p></title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <!-- The 1140px Grid -->
    <!-- 1140px Grid styles for IE -->
    <!--[if lte IE 9]><link rel="stylesheet" href="<?php echo CSS_DIR; ?>ie.css" type="text/css" media="screen" /><![endif]-->

    <!-- The 1140px Grid - http://cssgrid.net/ -->
    <link rel="stylesheet" href="<?php echo CSS_DIR; ?>1140.css" type="text/css" media="screen" />
    
    <!-- Your styles -->
    <link rel="stylesheet" href="<?php echo CSS_DIR; ?>styles.css" type="text/css" media="screen" />

    <!--link rel="stylesheet" href="<?php echo CSS_DIR; ?>1140.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php echo CSS_DIR; ?>typeimg.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php echo CSS_DIR; ?>smallerscreen.css" media="only screen and (max-width: 1023px)" />
    <link rel="stylesheet" href="<?php echo CSS_DIR; ?>mobile.css" media="handheld, only screen and (max-width: 767px)" />
    <link rel="stylesheet" href="<?php echo CSS_DIR; ?>layout.css" type="text/css" media="screen" /-->


    <!-- Google Webfonts -->
    <link href='http://fonts.googleapis.com/css?family=Averia+Serif+Libre' rel='stylesheet' type='text/css'>

    <!-- jQuery/Scripts-->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo JS_DIR; ?>scripts.php"></script>

</head>
<body>
<div class="container header">
    <div class="row">
        <div class="eightcol">
            <!--a href="/" id="sitelogo"></a-->
            <a href="/" id="siteheader">Socialrouter</a>
        </div>
        <div class="fourcol last">
            <!-- SIGN IN -->
<?php 
                if($_GET['path'] !== 'users/register' ) :
                    if( ! $this->isAuth() ) :
?>
                        <a href="/users/login" alt="login-box" id="loginbox_opener">
                            login
                            <!--div class="arrow"></div-->
                        </a>
                        <div id="loginbox">
                            <form id="login" accept-charset="utf-8" action="/users/login" method="post">
<?php 
                                if (isset($this->errorMsg['invalid']['notfound'])) :
                                    echo '<div class="errormsg">' . $this->errorMsg['invalid']['notfound'] . '</div>';
                                endif; 
?>
                                <div class="input">
                                    <label for="email"><?php echo _('Email'); ?>:</label>
                                    <input id="email" type="text" maxlength="30" name="postdata[email]" 
                                    <?php echo ' value="' . $this->postdata['email'] . '"'; ?> />
                                </div>
                                <div class="input">
                                    <label for="password"><?php echo _('Password'); ?>:</label>
                                    <input id="password" type="password" name="postdata[password]" />
                                </div>
                                <!--div class="checkbox">
                                    <label for="stayLoggedIn"><?php echo _('stay logged in'); ?>:</label>
                                    <input id="stayLoggedIn" type="checkbox" name="postdata[stayLoggedIn]" checked="checked" disabled="disabled"/>
                                    <?php echo '<span style="font-size:10px;">' . _('(until the end of time)') . '</span>'; ?>
                                </div-->
                                <input type="submit" value="<?php echo _('sign in'); ?>"/>
                            </form>
                        </div>
                    <?php 
                    else:
                        echo '<a href="/users/home/">' . $_SESSION['__sessiondata']['email'] . '</a> ';
                        echo '<a href="/users/logout/">' . _('logout') . '</a>';
                    endif; ?>
                <?php 
                endif; ?>
        </div>
    </div>
</div>
