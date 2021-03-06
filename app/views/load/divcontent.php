/* vim:set ft=javascript: */
/* //vim:set ft=css: */

<?php

class Html extends Load {

	public function __construct() {}

    public static function getHTML($data = NULL)
    {
        $css = Html::getCSS();

        if( parent::isAuth() ) {

            $html = Html::loggendIn( $css, $data );
        }
        else {
            $html = Html::notLoggendIn( $css );
        }

        return str_replace( "\n", '', $html );
    }

    /**
     *  HTML for not logged in users
     */
    private function notLoggendIn( $css )
    {
        $html = Html::header( $css, '' );
        $html .= <<<HTMLSTUFF

<div id="socialRouter_main">
You have to log in in order to use SocialRouter!
    <form id="socialRouterLoginForm" name="socialRouterLoginForm" accept-charset="utf-8" action="http://sr2.soluch.at/users/login/" method="post" target="_blank" >
        <input id="socialrouter_email" type="text" value="" name="postdata[email]" maxlength="30">
        <input id="socialrouter_password" type="password" name="postdata[password]">
        <br />
    	<input type="submit" value="sign in" id="submitLogin" />
    </form>
    <div id="otherContent">
    </div>
</div>
HTMLSTUFF;

        $html .= Html::footer();

        return $html;
    }

    /**
     *  HTML for logged in users
     */
    private function loggendIn( $css, $data )
    {
        $mail = '';
        if(isset($_SESSION['__sessiondata']['email'])) :
            $mail = '<span style="font-size:10px;">';
                $mail .= $_SESSION['__sessiondata']['email'];
            $mail .= '</span>';
        endif;

        $twitterUserForm = '';
        $shortenerForm = 'URL shortener: ';
        if($data) :
            if(isset($data->twitter) && $data->twitter) :
                foreach($data->twitter as $tw) :
                    $twitterUserForm .= '<input type="checkbox" name="postdata[twitterUser][]" value="' . $tw['username'] . '" style="float:none;" ><span style="margin-right:3px;">' . $tw['username'] . '</span>';// . '<br />';
                endforeach;
            endif;
            if(isset($data->shortener) && $data->shortener) :
                $i = 1;
                foreach($data->shortener as $sh) :
                    $shortenerForm .= '<input type="radio" name="postdata[shortener]" value="' . $sh['service'] . '|' . $sh['username'] . '" ' . (($i===1) ? 'checked=checked' : '') . ' style="margin:0;float:left;" /><span style="margin-right:3px;">' . $sh['username'] . ' ' . $sh['service'] . '</span>';
                    $i++;
                endforeach;
            endif;
        endif;

        // get Header
        $html = Html::header( $css, $mail );

        $html .= <<<HTMLSTUFF
<div id="socialRouter_main">
    <form id="socialRouterForm" name="socialRouterForm" accept-charset="utf-8" action="http://sr2.soluch.at/load/delegateMessage/" method="post" target="_blank" >
        <textarea id="sr_textarea" name="postdata[message]" cols="35" rows="3" >
    
    $data->shorturl
    
    </textarea>
    
    $shortenerForm
    
    <div style="float:right;">chars used: <span id="sr_charCount" style="color:#D94432;font-weight:bold;"></span></div>
    <hr />
        <!--input id="twitteruser" type="text" maxlength="30" name="postdata[twitteruser]" value="php_live" /-->
        <a href="http://sr2.soluch.at/twitter/add/" target="_blank" >add a twitter account</a><br />
    
        $twitterUserForm
    
        <br />
        <hr />
    	<input type="submit" value="Route" id="submitSocial" />
    </form>
    
    
    <div id="otherContent">
    </div>
</div>
HTMLSTUFF;

        // get Footer
        $html .= Html::footer();
    
        return $html;
    }

    /**
     * Header
     */
    private function header( $css, $mail = NULL )
    {
        $html = <<<HTMLSTUFF
$css
<a href="http://sr2.soluch.at" target="_blank" id="sr_title" class="" >
<img src="data:image/gif;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAACvUlEQVQ4y3VU7U4aQRRdEn7wLPoU/uZnw67SiLuLgIAuO7M0RIPWaqElbaEomph+bZvQNqk1pf1h/WirtvgMPIKPICHM7Z3ZARZrN7mZ2fk4c86dc0dR5KcZdoC3UYOGNIOGMVzVoB2MLvavZd/lc2o8H+Rrp+Wef76ISSZVkxyqBgEemmxFmKN/zSQt1bQnxjZruhXwWntKNeyrwSb/Rv+/NjrgSsU9gpkumUVMe1JMeIv6GEwyYShFtAg0HMPoDcEMOilA7szREA5IOVSA+Jlo5hAIfJIZyutLlodqzAopOBHmi/nJAyZ302tgPfsOhlMby1H20QGk7r+TY47HVhxEwwq/Cb/+KIJkNj8A2f4N2fIBJFbfQKL4CpIIYFePwaoegX6vPmInWBNXEddqDkFYevMjkNopJNfewmLlGzg7lwh6DrTRhtSaC5nSPuRw3iw0UK4zSHwH/YM+QaBoagXSG03I77ZhsbwPs9l1lOECqf9EJkdAnp9AzHrIzHwN8o0LcLbPQCeVAVCXA13zHFli8Q8gW2fI6ATs2jHkUIpd/4X9EzGee3ooxjhDguP8EHmrXQVlCWl8YaL4EuZX9hDwFJzdSyGLS6Jb56LvNP4A3Wlj0r+ATp8wC4E9RrTDb831gC4glnsMMbsimCVWX8P88p5I9HzxhfiPF3ZYtvwZMgjEE71Y+SqTTV1F1A7+LFVaMGuVGQfLlj4JC3ieGTdkHJO8sPGeb2YLD5qDNWFlRneCuKg1k1xGTzjcsWzOKsES0tadqs+QFNJ4Y6n1pvQO6U0nCrxtadNOUJYIHZYIRh81M8+glPkMyXym7cl5rDfqFW9EzwXkMzLlA/tv9Y+KFkFM4hWtQQI3nhFkZtIbzwiVMQbcQskTt75Fmu6hRuMkxGtHlA46lntERkfV8YZ1Eo4kLe9hiztDJn8BOjN4ZVpamhcAAAAASUVORK5CYII=" alt="logo" width="18" height="18" style="border:none;position:absolute;top:8px;" />
<span style="margin-left:22px;">SocialRouter</span>
</a>
<span style="float:left;margin:6px 0 0 3px;font-size:7px;color:#697176;">v0.6.5</span>
<div style="clear:both;width:100%;height:1px;margin:9px 0 2px 0;/*border-bottom:1px dotted #754741;*/"></div>
$mail
<div id="sr_closeButton" class="sr_topButtons" >close x</div>
HTMLSTUFF;
    
        return $html;
    }

    /**
     * Footer
     */
    private function footer()
    {
        $html = <<<HTMLSTUFF
<div style="width:100%;height:1px;margin:9px 0 2px 0;border-bottom:1px dotted #754741;"></div>
<span style="float:right;font-size:9px;color:#C1C1C1;">Adrian Soluch 2012</span>
HTMLSTUFF;
    
        return $html;

    }

    /**
     *  CSS
     */
    private function getCSS()
    {
        $boxShadow = '#303540';
        $boxShadowInset = '#EAEAEA';

$css = <<<CSS
<style type="text/css">
/*#socialrouterMaindiv > div, span, object, h1, h2, h3, h4, h5, h6, p, a, img, b, u, i, center, dl, dt, dd, ol, ul, li, fieldset, form, label, table, caption, tbody, tfoot, thead, tr, th, td {
margin:0;
padding:0;
border:0;
font-size: 100%;
font: inherit;
vertical-align: baseline;
}*/
#logo {

}

#socialrouterMaindiv {
position:fixed;
overflow:hidden;
right:5px;
bottom:5px;
width:300px;
/*height:250px;*/
padding:5px 14px;
background-color:#FFF;
/*background:url("data:image/gif;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAANCAMAAACTkM4rAAAAM1BMVEXy8vLz8/P5+fn19fXt7e329vb4+Pj09PTv7+/u7u739/fw8PD7+/vx8fHr6+v6+vrs7Oz2LjW2AAAAkUlEQVR42g3KyXHAQAwDQYAQj12ItvOP1qqZZwMMPVnd06XToQvz4L2HDQ2iRgkvA7yPPB+JD+OUPnfzZ0JNZh6kkQus5NUmR7g4Jpxv5XN6nYWNmtlq9o3zuK6w3XRsE1pQIEGPIsdtTP3m2cYwlPv6MbL8/QASsKppZefyDmJPbxvxa/NrX1TJ1yp20fhj9D+SiAWWLU8myQAAAABJRU5ErkJggg==") repeat;*/
border:1px solid #235E52;
-webkit-border-radius:5px;
-moz-border-radius:5px;
border-radius:5px;
-webkit-box-shadow:0px 0px 3px 0px $boxShadow, 0 0 5px $boxShadowInset inset;
-moz-box-shadow:0px 0px 3px 0px $boxShadow, 0 0 5px $boxShadowInset inset;
box-shadow:0px 0px 3px 0px $boxShadow, 0 0 5px $boxShadowInset inset;
font-family:arial;
font-size:12px;
text-align:left;
color:#15302A;
zoom:1;
}

#socialrouterMaindiv a {
border:none;
text-decoration:none;
color:#000;
}

#socialrouterMaindiv textarea {
clear:both;
width:295px;
height:90px;
margin:0 0 5px 0;
padding:3px;
border:1px solid #607E78;
resize:none;
font-size:12px;
font-family:arial;
}
    #socialrouterMaindiv textarea:focus {
    border:1px solid #9F9C89;
    }

#socialrouterMaindiv a#sr_title {
display:block;
float:left;
/*position:absolute;
top:8px;
left:15px;*/
margin:1px 0 2px 0;
color:#2C413D;
font-size:13px;
font-weight:bold;
}
#socialrouterMaindiv div#sr_closeButton {
position:absolute;
top:5px;
right:5px;
padding-left:4px;
padding-right:4px;
border-radius:5px; 
-moz-border-radius:5px; 
-webkit-border-radius:5px; 
background-color:#6F6F6F;
color:#FFF;
text-shadow:#000 1px 1px 0;
cursor:pointer;cursor:hand;
}
    #socialrouterMaindiv div#sr_closeButton:hover {
    background-color:#868686;
    text-shadow:#2F2F2F 1px 1px 0;
    }

.socialrouter_button {
width:30%;
padding:3px;
text-align:center;
color:#FFF;
cursor:pointer;cursor:hand;
}

#socialrouterMaindiv hr {
border:1px dashed #BACDC9;
}
</style>
CSS;

    return $css;

    }
}
