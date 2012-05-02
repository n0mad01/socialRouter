<?php

class Html extends Load {

	public function __construct() {}

    public static function getHTML($data = NULL)
    {
        $css = Html::getCSS();

        if(parent::isAuth()) {

            $html = Html::loggendIn($css, $data);
        }
        else {
            $html = Html::notLoggendIn($css);
        }

        return str_replace("\n", '', $html);
    }

    /**
     *  HTML for logged in users
     */
    private function loggendIn($css, $data)
    {
        //dumper($data);
        $mail = '';
        if(isset($_SESSION['__sessiondata']['email'])) :
            $mail = '<span style="font-size:10px;">'; 
                $mail .= $_SESSION['__sessiondata']['email'];
            $mail .= '<span>';
        endif;

        $twitterUserForm = '';
        $shortenerForm = '';
        if($data) :
            if(isset($data['twitter']) && $data['twitter']) :
                foreach($data['twitter'] as $tw) :
                    $twitterUserForm .= '<input type="checkbox" name="postdata[twitterUser][]" value="' . $tw['username'] . '" ><span style="margin-right:3px;">' . $tw['username'] . '</span>';// . '<br />';
                endforeach;
            endif;
            if(isset($data['shortener']) && $data['shortener']) :
                $i = 1;
                foreach($data['shortener'] as $sh) :
                    $shortenerForm .= '<input type="radio" name="postdata[shortener]" value="' . $sh['service'] . '|' . $sh['username'] . '" ' . (($i===1) ? 'checked=checked' : '') . ' ><span style="margin-right:3px;">' . $sh['username'] . ' ' . $sh['service'] . '</span>';
                    $i++;
                endforeach;
            endif;
        endif;

        $html = <<<HTMLSTUFF
$css
<a href="http://sr.soluch.at" target="_blank" id="sr_title" class="" >
<img src="data:image/gif;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAACvUlEQVQ4y3VU7U4aQRRdEn7wLPoU/uZnw67SiLuLgIAuO7M0RIPWaqElbaEomph+bZvQNqk1pf1h/WirtvgMPIKPICHM7Z3ZARZrN7mZ2fk4c86dc0dR5KcZdoC3UYOGNIOGMVzVoB2MLvavZd/lc2o8H+Rrp+Wef76ISSZVkxyqBgEemmxFmKN/zSQt1bQnxjZruhXwWntKNeyrwSb/Rv+/NjrgSsU9gpkumUVMe1JMeIv6GEwyYShFtAg0HMPoDcEMOilA7szREA5IOVSA+Jlo5hAIfJIZyutLlodqzAopOBHmi/nJAyZ302tgPfsOhlMby1H20QGk7r+TY47HVhxEwwq/Cb/+KIJkNj8A2f4N2fIBJFbfQKL4CpIIYFePwaoegX6vPmInWBNXEddqDkFYevMjkNopJNfewmLlGzg7lwh6DrTRhtSaC5nSPuRw3iw0UK4zSHwH/YM+QaBoagXSG03I77ZhsbwPs9l1lOECqf9EJkdAnp9AzHrIzHwN8o0LcLbPQCeVAVCXA13zHFli8Q8gW2fI6ATs2jHkUIpd/4X9EzGee3ooxjhDguP8EHmrXQVlCWl8YaL4EuZX9hDwFJzdSyGLS6Jb56LvNP4A3Wlj0r+ATp8wC4E9RrTDb831gC4glnsMMbsimCVWX8P88p5I9HzxhfiPF3ZYtvwZMgjEE71Y+SqTTV1F1A7+LFVaMGuVGQfLlj4JC3ieGTdkHJO8sPGeb2YLD5qDNWFlRneCuKg1k1xGTzjcsWzOKsES0tadqs+QFNJ4Y6n1pvQO6U0nCrxtadNOUJYIHZYIRh81M8+glPkMyXym7cl5rDfqFW9EzwXkMzLlA/tv9Y+KFkFM4hWtQQI3nhFkZtIbzwiVMQbcQskTt75Fmu6hRuMkxGtHlA46lntERkfV8YZ1Eo4kLe9hiztDJn8BOjN4ZVpamhcAAAAASUVORK5CYII=" alt="logo" width="18" height="18" style="border:none;position:absolute;top:4px;" />
<span style="margin-left:22px;">SocialRouter</span>
socB
</a>
<span style="float:left;margin:6px 0 0 3px;font-size:7px;color:#697176;">v0.6.5</span>
<div style="clear:both;width:100%;height:1px;margin:9px 0 2px 0;border-bottom:1px dotted #754741;"></div>
$mail
<div id="sr_closeButton" class="sr_topButtons" >◧ ◨ x</div>

<form id="" accept-charset="utf-8" action="http://sr.soluch.at/load/delegateMessage/" method="post">
    $shortenerForm
    <textarea id="sr_theURL" name="postdata[message]" cols="35" rows="3"></textarea>
    <!--input id="twitteruser" type="text" maxlength="30" name="postdata[twitteruser]" value="php_live" /-->
    <a href="http://sr.soluch.at/twitter/add/">add twitter account</a><br />
    $twitterUserForm
    <br />
	<input type="submit" value="Submit" />
</form>


<div id="otherContent">
</div>

<div style="width:100%;height:1px;margin:9px 0 2px 0;border-bottom:1px dotted #754741;"></div>
<span style="float:right;">Adrian Soluch 2012</span>
HTMLSTUFF;
    
        return $html;
        //return str_replace("\n", '', $html);
    
    }

    /**
     *  HTML for not logged in users
     */
    private function notLoggendIn($css)
    {
        return <<<HTMLSTUFF
$css
<a href="http://sr.soluch.at" target="_blank" id="sr_title" class="" >SocialRouter</a>
<span style="float:left;margin:6px 0 0 3px;font-size:7px;color:#697176;">v0.5.2</span>
<div style="clear:both;width:100%;height:1px;margin:9px 0 2px 0;border-bottom:1px dotted #754741;"></div>
<div id="sr_closeButton" class="sr_topButtons" >◧ ◨ x</div>
NOT LOGGED IN!
<div id="otherContent">
</div>

<div style="width:100%;height:1px;margin:9px 0 2px 0;border-bottom:1px dotted #754741;"></div>
<span style="float:right;">Adrian Soluch 2012</span>
HTMLSTUFF;
    }

    private function getCSS()
    {
$css = <<<CSS
<style type="text/css">
#socialrouterMaindiv {
position:fixed;
overflow:hidden;
right:5px;
bottom:5px;
width:300px;
padding:5px 14px;
background-color:#E5EBEE;
border:1px solid #B0BFC9;
-webkit-border-radius:5px;
-moz-border-radius:5px;
border-radius:5px;
-webkit-box-shadow:0px 0px 3px 0px #929A9E, 0 0 5px #CACACA inset;
-moz-box-shadow:0px 0px 3px 0px #929A9E, 0 0 5px #CACACA inset;
box-shadow:0px 0px 3px 0px #929A9E, 0 0 5px #D0DDE4 inset;
font-family:arial;
font-size:12px;
text-align:left;
color:#000;
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
height:70px;
margin:10px 0;
padding:3px;
resize:none;
}

#socialrouterMaindiv a#sr_title {
display:block;
float:left;
/*position:absolute;
top:8px;
left:15px;*/
margin:1px 0 2px 0;
color:#A44135;
font-size:13px;
font-weight:bold;
}
#socialrouterMaindiv div#sr_closeButton {
position:absolute;
top:5px;
right:5px;
}

</style>
CSS;

    return $css;

    }
}
