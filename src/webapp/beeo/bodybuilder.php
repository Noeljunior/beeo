<?php

    /****************************************************
                        SYS LANGUAGE
    /****************************************************/
    function loadsyslanguage($path, $language) {
        return file_exists($path . $language . ".php") ? include($path . $language . ".php") : [];
    }
    function lpgetstr($strs, $id, $a = "", $b = "") {
        if (isset($strs[$id]))
            return sprintf($strs[$id], $a, $b);
        return "???";
    }

    /****************************************************
                        HELPER
    /****************************************************/
    /* URL management */
    function arraddrem($arr, $more = "", $less = "") {
        /* remove */
        if (is_array($less))
            foreach ($less as $l)
                unset($arr[$l]);
        else if($less !== "")
            unset($arr[$less]);

        /* add */
        if (is_array($more))
            $arr = array_merge($arr, $more);
        else if($more !== "")
            $arr = array_merge($arr, [$more => ""]);
        return $arr;
    }
    /* URL management */
    function arrcpy($arr, $this) {
        $new = [];
        if (!is_array($this) && $this !== "")
            $new[$this] = "";
        if (is_array($this))
            foreach ($this as $k)
                if (isset($arr[$k]))
                    $new[$k] = $arr[$k];
        return $new;
    }
    function arrtostr($arr) {
        $res = "?";
        foreach ($arr as $key => $val) {
            //if ($val === "") continue;
            $res .=  "$key" .
                ($val === "" ? "" : "=") .
                urlencode($val) . "&";
        }
        return substr($res, 0, -1);
    }
    function gettourl($arr, $more = "", $less = "") {
        return arrtostr(arraddrem($arr, $more, $less));
    }
    function gettohidden($arr, $more = "", $less = "") {
        $arr = (arraddrem($arr, $more, $less));
        $res = "";
        foreach ($arr as $key => $val) {
            $res .= bb_af_form_hidden($key, $val);
        }
        return $res;
    }

    /* FORM VALIDATION */
    function isgd($key) {
        return (isset($_GET[$key]) ? $_GET[$key] : "");
    }
    function ispd($key) {
        return (isset($_POST[$key]) ? $_POST[$key] : "");
    }

    function issetg($key) {
        return isset($_GET[$key]);
    }
    function issetp($key) {
        return isset($_POST[$key]);
    }

    function valg($in, $ll = "") {
        if (!isset($_GET[$in]))  return FALSE;
        $in = $_GET[$in];
        if ($ll === "") {
            if ($in === "")      return FALSE;
        } else {
            if ($in !== $ll)     return FALSE;
        }
        return TRUE;
    }
    function valp($in, $ll = "") {
        if (!isset($_POST[$in])) return FALSE;
        $in = $_POST[$in];
        if ($ll === "") {
            if ($in === "")      return FALSE;
        } else {
            if ($in !== $ll)     return FALSE;
        }
        return TRUE;
    }
    function valga($in, $ll = "", $lr = "") {
        if (!isset($_GET[$in]))   return FALSE;
        $in = $_GET[$in];
        if (!is_array($in))       return FALSE;
        if ($ll === "" && $lr === "") {
            /* OK */
        } else
        if ($ll !== "" && $lr === "") {
            if (count($in) < $ll) return FALSE;
        } else
        if ($ll === "" && $lr !== "") {
            if (count($in) > $lr) return FALSE;
        } else {
            if ((count($in) < $ll) ||
                (count($in) > $lr)) return FALSE;
        }
        return TRUE;
    }
    function valpa($in, $ll = "", $lr = "") {
        if (!isset($_POST[$in]))   return FALSE;
        $in = $_POST[$in];
        if (!is_array($in))        return FALSE;
        if ($ll === "" && $lr === "") {
            /* OK */
        } else
        if ($ll !== "" && $lr === "") {
            if (count($in) < $ll)  return FALSE;
        } else
        if ($ll === "" && $lr !== "") {
            if (count($in) > $lr)  return FALSE;
        } else {
            if ((count($in) < $ll) ||
                (count($in) > $lr)) return FALSE;
        }
        return TRUE;
    }
    /* db validation */
    function valdbres($in, $ll = 1, $lr = "") {
        if (!is_array($in))        return FALSE;

        if ($ll !== "" && $lr === "") {
            if (count($in) < $ll)  return FALSE;
        } else
        if ($ll === "" && $lr !== "") {
            if (count($in) > $lr)  return FALSE;
        } else {
            if ((count($in) < $ll) ||
                (count($in) > $lr)) return FALSE;
        }
        return TRUE;
    }
    function valdb($query, $args = [], $ll = 1, $lr = "") {
        $in = db_do($query, $args);
        return valdbres($in, $ll, $lr);
    }
    function valdb_e($query, $args = []) {
        return valdb($query, $args, 1, 1);
    }
    function valdb_ne($query, $args = []) {
        return valdb($query, $args, 0, 0);
    }
    function valdb_upname($oldname, $newname, $query, $args) {
        if ($oldname === $newname) return TRUE;
        return valdb($query, $args, 0, 0);
    }
    function validate_form($args) {
        foreach (func_get_args() as $args) {
            if (!is_array($args)) continue;
            foreach ($args as $val) {
                $vfunc = (isset($val[0]) ? $val[0] : "");
                $vargs = (isset($val[1]) ? $val[1] : "");
                $mfunc = (isset($val[2]) ? $val[2] : "");
                $margs = (isset($val[3]) ? $val[3] : "");
                $wfunc = (isset($val[4]) ? $val[4] : "");
                $wargs = (isset($val[5]) ? $val[5] : "");
                if ($vfunc !== "" && is_array($vargs)) {
                    if (!call_user_func_array($vfunc, $vargs)) {
                        $ret = "";
                        if ($mfunc !== "" && is_array($margs)) {
                            $ret = call_user_func_array($mfunc, $margs);
                        } else {
                            $ret = "NVF: " . $mfunc . $margs;
                        }
                        if ($wfunc !== "" && is_array($wargs)) {
                            call_user_func_array($wfunc, $wargs);
                        }
                        return $ret;
                    }
                }
                else
                    return 'Mal-formatted $args';
            }
        }
        return "";
    }

    $formwarn = array();
    function formalert($key, $class = " bbacyel ") {
        global $formwarn;
        $formwarn[$key] = $class;
    }
    function fia($key) {
        global $formwarn;
        return isset($formwarn[$key]) ? $formwarn[$key] : "";
    }

    /* RANDOM */
    function strinstr($str1, $str2) {
        return (substr_count($str2, $str1) >= 1);
    }
    function gpage() {
        return ["limit" => "1000", "offset" => "0"];
    }
    function wpage($args = []) {
        return array_merge(gpage(), $args);
    }
    function gpager() {
        return "LIMIT :limit OFFSET :offset";
    }
    /* bbformquery helpers */
    function bbdbdatetostr($row, $args) {
        $date = isset($args[0]) ? $row[$args[0]] : $row["date"];
        $format = isset($args[1]) ? $args[1] : "d/m/Y, H:i:s";
        return date($format, strtotime($date));
    }
    function bbdbrolestoballs($row, $args) {
        $thisroles = explode(", ", isset($args[0]) ? $row[$args[0]] : []);
        $allroles = isset($args[1]) ? $args[1] : [];
        $bbroles = "";
        foreach ($allroles as $eachrole) {
            $bbroles .= bb_af_txt($eachrole["letter"], "",
                "bbafin bbafroundbtn small " . (in_array($eachrole["id"], $thisroles) ? "" : "hidethis"));
        }
        return $bbroles;
    }
    /****************************************************
                        BODY PARTS
    /****************************************************/
    /* LEFT MENU BAR */
    function bb_leftmenu($usertitle, $themenu, $seled, $swtxt, $var = "m") {
        $res = "";
        foreach ($themenu as $mitem) {
            $url   = $mitem["url"];
            $title = $mitem["menutxt"];
            $sel   = ($seled === $mitem["url"] ? "selected" : "");
            $subitems = "";
            $res .= <<<EOFL
            <div class="menuitem rootmenu">
                <a href="?$var=$url" class="menutitle $sel" onclick="return getnow(this, event);">$title</a>
                $subitems
            </div>
EOFL;
        }

        return <<<EOFL
            <div class="authinfo">
                <div class="authuser">$usertitle</div>
                <a onclick="return getnow(this, event);" href="?logout" class="bbafin bfafbtn bbacred authoff">$swtxt</a>
            </div>
            <div class="menu" id="menuitems">$res</div>
EOFL;
    }

    /* UP MENU BUILDER */
    function upmenu($prefix, $idseled, $items) {
        $fargs = 1;
        $columns = floor(count($items) / 2);
        $wd = floor(100 / ($columns));
        $res = '<div class="upmenu">';
        for ($i = 0; $i < $columns; $i++) {
            $text = $items[$i * 2];
            $link = $items[$i * 2 + 1];

            $nowseled = ($link === $idseled ? " upseled" : "");
            $first = ($i === 0 ? " upfirst" : "");
            $link = gettourl($prefix, ["up" => $link]);
            $res .= <<<EOFF
            <a onclick="return getnow(this, event);" href="$link" class="upaitem$first$nowseled" style="width: calc($wd% - 15px);">
                <div class="upitem">$text</div>
            </a>
EOFF;
        }
        $res .= ' </div>';
        return $res;
    }
    /* 404 */
    function error404($url = "") {
            $url = $url === "" ? "imgs/404.gif" : "";
            $res = <<<EOFF
    404 <a href="https://www.youtube.com/watch?v=otCpCn0l4Wo">U Can\'t Touch This</a>
            <br><iframe width="267" height="200" src="https://www.youtube.com/embed/otCpCn0l4Wo?start=14&autoplay=1" frameborder="0" allowfullscreen></iframe>
            <img height="200px" src="$url">
EOFF;
        return bb_af_txt($res, "", "bbreterror bbacred");
    }
    function errorfky($url = "") {
            $url = $url === "" ? "imgs/404.gif" : "";
            $res = <<<EOFF
    404 <a href="https://www.youtube.com/watch?v=Y9iYf2CUlzE">Fuck Yourself</a>
            <br><iframe width="267" height="200" src="https://www.youtube.com/embed/Y9iYf2CUlzE?autoplay=1" frameborder="0" allowfullscreen></iframe>
            <img height="200px" src="$url">
EOFF;
        return bb_af_txt($res, "", "bbreterror bbacred");
    }

    /****************************************************
                       THEME MANAGEMENT
    /****************************************************/
    function bb_buildcssref($files = "", $default = "") {
        $res = "";
        foreach ($files as $f) {
            $alt = "alternate";
            $ftitle = $f[0];
            $fname  = $f[1];
            if ($ftitle === $default)
                 $alt = "";
            $res .= <<<EOFF
        <link name="csstheme" title="$ftitle" rel="$alt stylesheet" type="text/css" href="$fname">
EOFF;
        }

        return $res;

    }
    function bb_builthemeselector($class = "", $files = "", $default = "") {
        $res = "";
        foreach ($files as $f) {
            $seled = "";
            $ftitle = $f[0];
            if ($ftitle === $default)
                 $seled = "selected";
            $res .= <<<EOFF
        <option value="$ftitle" $seled>$ftitle</option>
EOFF;
        }


        return <<<EOFF
        <select class="bbafin bbthemechooser $class" id="themeschooser" onchange="settheme()">$res</select>
EOFF;
    }
    function bb_buildlangchooser($langs, $choice = "", $class = "") {
        $langsitems = "";
        foreach ($langs as $key => $lang) {
            $seled = $key === $choice ? "selected" : "";
            $langsitems .= <<<EOFF
                <option value="$key" $seled>$lang</option>
EOFF;
        }
        return <<<EOFF
        <select class="bbafin bblangchooser $class" id="langchooser" onchange="chooselanguage(this)">$langsitems</select>
EOFF;
    }
    function bb_logininfo($username, $role = "", $servname = "") {
        return <<<EOFF
        <span style="display:none;" id="logininfo" userid="$username" roleid="$role", servname="$servname"></span>
EOFF;
    }

    function bbjsonload($call, $attrib = "") { //<span style="display:none;" onload="$call" $attrib> </span>
        return <<<EOFF
        <script type="text/javascript">
        $call
        </script>
        
EOFF;
    }
    function bbjsajaxanim($imgsrc) {
        return <<<EOFF
    <a class="loadingbee removethis" id="loadingbee">
        <div class="removethis" id="errormsg"></div>
        <img src="$imgsrc">
    </a>
EOFF;
    }

    function bbjswhodidthis($who, $where = "") {
        return <<<EOFF
        <a href="$where" target="_blank">
        <span style="display: inline-block; text-align: right; margin: 0px; -moz-transform: scaleX(-1); -o-transform: scaleX(-1); -webkit-transform: scaleX(-1); transform: scaleX(-1); filter: FlipH; -ms-filter: “FlipH">©</span>
        $who
        </a>
EOFF;
    }

    function bbmainskeleton($header, $menu, $body, $footer) {
        return <<<EOFF
        <div class="bktop">
            $header
        </div>
        <div class="bkmiddle">
            <div class="bkmleft">
                $menu
            </div>
            <div class="bkmright" id="bkminnerbody">
                $body
            </div>
        </div>
        <div class="bkbottom">
            $footer
        </div>
EOFF;
    }

    /****************************************************
                         LOGIN PARTS
    /****************************************************/
    function lui_login($title, $error, $usertxt, $passtxt) {
        return <<<EOFS
        <div class="lgtitle">$title</div>
        <div class="lgerror bbacred">$error</div>
        <form method="POST" action="?">
            <div class="bbafsep bbafsitem">
                <div class="bbafser">
                    <div class="bbafsepl">$usertxt</div>
                    <div class="bbafsepc"><div class="bbafsepcl">&nbsp;</div></div>
                    <div class="bbafsepr">
                        <input class="bb bbafin" type="text" name="biblobu" onKeyPress="submitenter(this, event);">
                    </div>
                </div>
            </div>
            <div class="bbafsep bbafsitem">
                <div class="bbafser">
                    <div class="bbafsepl">$passtxt</div>
                    <div class="bbafsepc"><div class="bbafsepcl">&nbsp;</div></div>
                    <div class="bbafsepr">
                        <input class="bb bbafin" type="password" name="bible" onKeyPress="submitenter(this, event);">
                    </div>
                </div>
            </div>
        </form>
EOFS;
    } // <input type="submit" class="removethis">
    function lui_passwd($title, $error, $pass1txt, $pass2txt, $passwdtxt, $gobacktxt) {
        $later = 'onKeyPress="submitenter(this, event);"';
        //$later = '';
        return <<<EOFS
        <div class="lgtitle">$title</div>
        <div class="lgerror bbacred">$error</div>
        <form method="POST" action="?">
            <div class="bbafsep bbafsitem">
                <div class="bbafser">
                    <div class="bbafsepl">$pass1txt</div>
                    <div class="bbafsepc"><div class="bbafsepcl">&nbsp;</div></div>
                    <div class="bbafsepr">
                        <input class="bb bbafin" type="password" name="passwd" $later>
                    </div>
                </div>
            </div>
            <div class="bbafsep bbafsitem">
                <div class="bbafser">
                    <div class="bbafsepl">$pass2txt</div>
                    <div class="bbafsepc"><div class="bbafsepcl">&nbsp;</div></div>
                    <div class="bbafsepr">
                        <input class="bb bbafin" type="password" name="passwdconfirm" $later>
                    </div>
                </div>
            </div>
            <div class="center">
                <input onclick="return postnow(this);" class="bbafin bfafbtn bbacgreen" type="submit" name="passwdsub" value="$passwdtxt">
            </div>
            <div class="center">
                <a class="bbafin bfafbtn bbacred" href="?logout">$gobacktxt</a>
            </div>
        </form>
EOFS;
    }
    function lui_pickrole($title, $error, $roles, $gobacktxt) {
        $rfs = "";
        foreach ($roles as $role) {
            $roleid = $role["id"];
            $roledesc = $role["description"];
            $rfs .= <<<EOFS
            <form class="lgroleform" method="POST" action="?">
                <input onclick="return postnow(this);" class="bbafin bfafbtn" type="submit" name="rolechoser" value="$roledesc">
                <input type="hidden" name="rolepick" value="$roleid">
            </form>
EOFS;
        }
        return <<<EOFS
        <div class="lgtitle">$title</div>
        <div class="lgerror bbacred">$error</div>
        $rfs
        <div class="center">
            <a class="bbafin bfafbtn bbacred" href="?logout">$gobacktxt</a>
        </div>
EOFS;
    }

    function ll_cookies($show = TRUE, $cookiename, $txt) {
        if ($show) {
            return <<<EOFL
            <div class="cookiediv bbacred"
                onclick="this.parentNode.removeChild(this);document.cookie = '$cookiename=yes';">
                $txt
            </div>
EOFL;
        }
        return "";
    }
    function ll_loginskeleton($things, $img = "") {
        return <<<EOFS
    <div class="loginbee removethis">
        <img src="$img">
    </div>
    <div class="loginmain">
        $things
    </div>
EOFS;
    }


    /****************************************************
                       AUTO-TXT ELEMENTS
    /****************************************************/

    function bb_space($space, $style = "", $class = "", $attrib = "") {
        $style .= " margin: ".$space."px; ";
        $style = bb_style($style);
        return <<<EOFF
            <span $style></span>
EOFF;
    }
    function bbit($txt) {
        return <<<EOFF
            <span style="font-style: italic;">$txt</span>
EOFF;
    }
    function bbinfo($txt) {
        return bb_af_txt($txt, "", "bbreterror bbacgreen", 'onclick="removethis(this)"');
    }
    function bbwarn($txt) {
        return bb_af_txt($txt, "", "bbreterror bbacyel", 'onclick="removethis(this)"');
    }
    function bberror($txt, $close = 'onclick="removethis(this)"') {
        return bb_af_txt($txt, "", "bbreterror bbacred", $close);
    }
    function bbbtitlef($txt) {
        return bb_af_sep($txt, "", "", "bbafpsep bbafpsepbig bbafpsepf");
    }
    function bbbtitle($txt) {
        return bb_af_sep($txt, "", "", "bbafpsep bbafpsepbig");
    }
    function bbselitems_fromquery($query, $args, $id, $val, $seled = NULL) {
        $items = db_do($query, $args);
        $soptions = "";
        $checked = "";
        foreach ($items as $dt) {
            if ($seled === NULL || $dt[$id] == $seled) {
                $checked = " selected ";
                $seled = "";
            }
            $soptions .= bb_af_form_inselboxitem($dt[$id], $dt[$val], $checked);
            $checked = "";
        }
        return $soptions;
    }
    /* * * * * * * * * * SIMPLE TEXT * * * * * * * * * * * * */
    function bb_af_txt($txt, $style = "", $class = "", $attrib = "") {
        $style = bb_style($style);
        return <<<EOFF
            <div class="bbaftxt $class" $style $attrib>$txt</div>
EOFF;
    }
    function bb_af_a($txt, $href, $style = "", $class = "", $attrib = "", $ajax = TRUE) {
        $style = bb_style($style);
        $ajax = $ajax === TRUE ? 'onclick="return getnow(this, event);"' : "";
        return <<<EOFF
            <a $ajax href="$href" class="bbafa $class" $style $attrib>$txt</a>
EOFF;
    }
    /* * * * * * * * * * IMGS * * * * * * * * * * * * */
    function bb_img($src, $style = "", $class = "", $attrib = "") {
        $style = bb_style($style);
        return <<<EOFF
            <img src="$src" class="bbafimg $class" $style $attrib>
EOFF;
    }
    /* * * * * * * * * * THREE-PART SEPARATOR * * * * * * * * * * * * */
    function bb_af_sep($left, $right = "", $style = "", $class = "") {
        $style = bb_style($style);
        return <<<EOFF
            <div class="bbafsep $class" $style>
                <div class="bbafser">
                    <div class="bbafsepl">$left</div>
                    <div class="bbafsepc"><div class="bbafsepcl">&nbsp;</div></div>
                    <div class="bbafsepr">$right</div>
                </div>
            </div>
EOFF;
    }
    /* * * * * * * * * * TABLES * * * * * * * * * * * * */
    function bb_atable($groups, $style = "", $class = "", $attrib = "") {
        $style = bb_style($style);
        return <<<EOFF
            <div class="bbatable $class" $style $attrib>$groups</div>
EOFF;
    }
    function bb_atableh($cells, $style = "", $class = "", $attrib = "") {
        $style = bb_style($style);
        return <<<EOFF
            <div class="bbatabler bbatheader $class" $style $attrib>$cells</div>
EOFF;
    }
    function bb_atablef($cells, $style = "", $class = "", $attrib = "") {
        $style = bb_style($style);
        return <<<EOFF
            <div class="bbatabler bbatfooter $class" $style $attrib> $cells</div>
EOFF;
    }
    function bb_atabler($cells, $style = "", $class = "", $attrib = "") {
        $style = bb_style($style);
        return <<<EOFF
            <div class="bbatabler bbatinner $class" $style $attrib> $cells</div>
EOFF;
    }
    function bb_atabler_link($cells, $href = "", $style = "", $class = "", $attrib = "") {
        $style = bb_style($style);
        return <<<EOFF
            <a href="$href" onclick="return getnow(this, event);" class="bbatabler bbatinner rowlink $class" $style $attrib> $cells</a>
EOFF;
    }
    function bb_atabler_empty($txt, $style = "", $class = "", $attrib = "") {
        $style = bb_style($style .
        "text-align: center; border: solid 1px red; right: 0px; left: 0px;");
        return <<<EOFF
        <div class="bbatabler bbatinner">
            <div class="bbatablec $class" $style $attrib>asdad</div>
        </div>
            
EOFF;
    }
    function bb_atablec() {
        $columns = floor(func_num_args() / 2);
        $argv = func_get_args();

        $res = "";
        for ($i = 0; $i < $columns; $i++) {
            $pos = "bbac";
            if ($i == 0)
                $pos = "bbacl";
            if ($i == ($columns - 1))
                $pos = "bbacr";
            $txt  = $argv[$i * 2] == "" ? "&nbsp;" : $argv[$i * 2];
            $style = bb_style($argv[$i * 2 + 1]);
            $res .= <<<EOFF
                <div class="bbatablec $pos" $style>$txt</div>
EOFF;
        }
        return $res;
    }
    function bb_bigdivtable($content, $style = "", $class = "", $attrib = "") {
        $style = bb_style($style);
        return <<<EOFF
eer
EOFF;
    }
    /* * * * * TABLE HELPERS * * * * */
    function bb_atablecs() {
        $columns = func_num_args();
        $argv = func_get_args();
        $arr = [];
        for ($i = 0; $i < $columns; $i++) {
            array_push($arr, $argv[$i], "");
        }
        return call_user_func_array("bb_atablec", $arr);
    }
    function bb_atable_hfi($headers, $trs, $style = "") {
        $thead = call_user_func_array("bb_atablec", $headers);
        return bb_atable(bb_atableh($thead) . $trs . bb_atablef($thead), $style);
    }
    function bb_atable_hfis($headers, $trs, $style = "") {
        $thead = call_user_func_array("bb_atablecs", $headers);
        return bb_atable(bb_atableh($thead) . $trs . bb_atablef($thead), $style);
    }
    function bb_atabler_fromsql($query, $params, $order, $linkage = "", $lid = "id") {
        $sqlres = db_qm($query, $params);
        if (!is_array($sqlres)) return "sqlerror";
        $res = "";
        foreach ($sqlres as $row) {
            $arr = [];
            foreach ($order as $key) {
                if ($key === "") array_push($arr, "");
                else             array_push($arr, $row[$key]);
            }
            if (!$linkage)
                $res .= bb_atabler(call_user_func_array("bb_atablecs", $arr));
            else
                $res .= bb_atabler_link(call_user_func_array("bb_atablecs", $arr),
                    $linkage . $row[$lid]);
        }
        return $res;
    }
    function bb_atabler_fromquery($query, $params, $order, $linkage = "", $lid = "id") {
        $sqlres = db_do($query, $params);
        if (!is_array($sqlres)) return "sqlerror";
        $res = "";
        foreach ($sqlres as $row) {
            $arr = [];
            foreach ($order as $key) {
                if (is_array($key)) { /* call an external function to handle this */
                    $val = call_user_func_array($key[0],
                        [$row, (isset($key[1]) ? $key[1] : [])]);
                    array_push($arr, $val);
                } else
                if ($key === "") array_push($arr, "");
                else             array_push($arr, $row[$key]);
            }
            if (!$linkage)
                $res .= bb_atabler(call_user_func_array("bb_atablecs", $arr));
            else
                $res .= bb_atabler_link(call_user_func_array("bb_atablecs", $arr),
                    $linkage . $row[$lid]);
        }
        return $res;
    }

    /****************************************************
                       AUTO-FORM ELEMENTS
    /****************************************************/
        /* * * * * * * * * * FORMS * * * * * * * * * * * * */
    function bb_af_form($elems, $action = "", $method = "POST", $style = "", $class = "", $attrib = "") {
        $style = bb_style($style);
        return <<<EOFF
        <form class="bbaform $class" method="$method" action="$action" $attrib  style="$style">
        $elems
        </form>
EOFF;
    }
    function bb_af_form_tabler($elems, $action = "", $method = "POST", $style = "", $class = "", $attrib = "") {
        $style = bb_style($style);
        return <<<EOFF
        <form class="bbatabler bbatinner $class" method="$method" action="$action" $attrib  styl="$style">
        $elems
        </form>
EOFF;
    }
        /* * * * * * * * * * INPUTS * * * * * * * * * * * * */
    function bb_af_form_input($input, $name, $value = "", $style = "", $class = "", $attrib = "") {
        $style = bb_style($style);
        return <<<EOFF
        <input class="bb bbafin bbafin$input $class" type="$input" name="$name" value="$value" $attrib  style="$style">
EOFF;
    }
    function bb_af_form_intxt($name, $value = "", $style = "", $class = "", $attrib = "") {
        return bb_af_form_input("text", $name, $value, $style, $class, $attrib);
    }
    function bb_af_form_innum($name, $value = "", $style = "", $class = "", $attrib = "") {
        return bb_af_form_input("number", $name, $value, $style, $class, 'step="any"'. $attrib);
    }
    function bb_af_form_indate($name, $value = "", $style = "", $class = "", $attrib = "") {
        return bb_af_form_input("date", $name, $value, $style, $class, $attrib);
    }
    function bb_af_form_intime($name, $value = "", $style = "", $class = "", $attrib = "") {
        return bb_af_form_input("time", $name, $value, $style, $class, $attrib);
    }
    function bb_af_form_indatetime($name, $value = "", $style = "", $class = "", $attrib = "") {
        return bb_af_form_input("datetime-local", $name, $value, $style, $class, $attrib);
    }
    function bb_af_form_intextarea($name, $value = "", $style = "", $class = "", $attrib = "") {
        $style = bb_style($style);
        return <<<EOFF
        <textarea class="bb bbafin bbafintextarea $class" name="$name" $attrib  style="$style">$value</textarea>
EOFF;
    }
    function bb_af_form_intextareadiv($name, $value = "", $style = "", $class = "", $attrib = "") {
        $style = bb_style($style);
        static $tad = 0;
        $tad++;
        return <<<EOFF
        <input type="hidden" name="$name" value="$value" id="tad$tad">
        <div onkeyup="taupdate(this, event, 'tad$tad');" contenteditable="true" class="bb bbafin bbafintextareadiv $class" $attrib  style="$style"></div>
EOFF;
    }
    function bb_af_form_instate($type, $name, $value = "", $txt = "",
            $symbol = "", $style = "", $class = "", $attrib = "", $checked = "") {
        static $afincheck = 0;
        $afincheck++;
        $symbol = $symbol == "" ? "&#10004;" : $symbol;
        $style = bb_style($style);
        return <<<EOFF
        <input class="bbafinhin" type="$type" name="$name" value="$value" id="afincheck$afincheck" $checked>
            <label class="bbafin $class" for="afincheck$afincheck" $attrib $style><span>$symbol</span>$txt</label>
EOFF;
    }
    function bb_af_form_inradio($name, $value = "", $txt = "",
            $symbol = "", $style = "", $class = "", $attrib = "", $checked = "") {
        return bb_af_form_instate("radio", $name, $value, $txt, $symbol, $style, "bbafinone " . $class, $attrib, $checked);
    }
    function bb_af_form_incheck($name, $value = "", $txt = "",
            $symbol = "", $style = "", $class = "", $attrib = "", $checked = "") {
        return bb_af_form_instate("checkbox", $name, $value, $txt, $symbol, $style, "bbafinmany " . $class, $attrib, $checked);
    }
    function bb_af_form_inbool($name, $value1 = "", $value2 = "", $txt1 = "", $txt2 = "",
            $default = "", $symbol = "", $style = "", $class = "", $attrib = "") {
            $symbol = $symbol == "" ? "&#10004;" : $symbol;
            $ch1 = ($default === "0" || $default === "f" || $default === "l") ? "checked " : "";
            $ch2 = ($default === "1" || $default === "s" || $default === "r") ? "checked " : "";
        return
            bb_af_form_instate("radio", $name, $value1, $txt1, $symbol, $style, "bbafinbool " . $class, $attrib, $ch1) .
            bb_af_form_instate("radio", $name, $value2, $txt2, $symbol, $style, "bbafinbool " . $class, $attrib, $ch2);
    }
    function bb_af_form_inselbox($name, $options = "", $style = "", $class = "", $attrib = "") {
        $style = bb_style($style);
        return <<<EOFF
        <select name="$name" class="bbafin bbafinselbox $class" $style $attrib>$options</select>
EOFF;
    }
    function bb_af_form_inselboxitem($value, $txt = "", $attrib = "") {
        return <<<EOFF
        <option value="$value" $attrib>$txt</option>
EOFF;
    }
    function bb_af_form_hidden($name, $value = "") {
        return <<<EOFF
        <input type="hidden" name="$name" value="$value">
EOFF;
    }
    function bb_af_form_inselbox_auto($name, $dbres, $id, $str, $seled = "", $pre = "", $pos = "") {
        $soptions = $pre;
        foreach ($dbres as $item) {
            $soptions .= bb_af_form_inselboxitem($item[$id], $item[$str],
            ($item[$id] == $seled ? "selected" : ""));
        }
        $soptions .= $pos;
        return bb_af_form_inselbox($name, $soptions);
    }
        /* * * * * * * * * * BUTTONS * * * * * * * * * * * * */
    function bb_af_form_btn($name, $txt = "", $style = "", $class = "", $attrib = "") {
        $style = bb_style($style);
        return <<<EOFF
        <input class="bbafin bfafbtn $class" type="button" name="$name" value="$txt" $style $attrib>
EOFF;
    }
    function bb_af_form_submit($name, $txt = "", $action = FALSE, $style = "", $class = "", $attrib = "", $onclick = FALSE) {
        $style = bb_style($style);
        $action = $action !== FALSE ? "formAction=\"$action\"" : "";
        $onclick = $onclick !== FALSE ? $onclick : 'onclick="return postnow(this);"';
        return <<<EOFF
        <input $onclick class="bbafin bfafbtn $class" type="submit" $action name="$name" value="$txt" $style $attrib>
EOFF;
    }


    /****************************************************
                       MIX/CUSTOM ELEMENTS
    /****************************************************/
    function bb_licontainer($elems, $style = "") {
        return "<div style=\"$style\">$elems</div>";
    }

    function bb_listitem($txtl, $txtr = NULL, $flavor = FALSE , $name = "", $value = "", $class = "", $attrib = "") {
        $lpn = $btn = $stl = $rmtr = $rml = $rmr = $input = "";

        if ($flavor === "add") { /* add mode */
            $flavor = "bbaqiedit";
            $rml   = "visibility:hidden;";
            $btn   = <<<EOFL
            <span class="bbafin bfafbtn bbafroundbtn bbacgreen removeqtsbtn small"
                onclick="addqt(this)">&#10010;</span>
EOFL;
        } else
        if ($flavor === "evadd") { /* add mode */
            $flavor = "bbaqiedit evadd";
            $rml   = "visibility:hidden;";
            $btn   = <<<EOFL
            <span class="bbafin bfafbtn bbafroundbtn bbacgreen removeqtsbtn small"
                onclick="addev(this)">&#10010;</span>
EOFL;
        } else
        if ($flavor === "edit" || $flavor === TRUE) { /* edit mode */
            $flavor = "bbaqiedit";
            $lpn = <<<EOFL
            <div class="ttable updown">
                <div class="trow updownr"><div class="tcell" onclick="rorderqt(this, 'up')">&#9652;</div></div>
                <div class="trow updownr"><div class="tcell" onclick="rorderqt(this, 'down')">&#9662;</div></div>
            </div>
EOFL;
            $btn = <<<EOFL
            <span class="bbafin bfafbtn bbafroundbtn bbacred removeqtsbtn rmbg small"
                  onclick="removeqt(this)">&#10799;</span>
EOFL;
        } else
        if ($flavor === "hidden") { /* eventlist */
            $stl = 'style="display:none"';
            $flavor = "bbaqiedit";
            $txtr = " ";
            $lpn = <<<EOFL
            <div class="ttable updown">
                <div class="trow updownr"><div class="tcell" onclick="rorderqt(this, 'up')">&#9652;</div></div>
                <div class="trow updownr"><div class="tcell" onclick="rorderqt(this, 'down')">&#9662;</div></div>
            </div>
EOFL;
            $btn = <<<EOFL
            <span class="bbafin bfafbtn bbafroundbtn bbacred removeqtsbtn rmbg small"
                  onclick="removeqt(this)">&#10799;</span>
EOFL;
        } else
        if ($flavor === "evlist") { /* eventlist: normal */
            $flavor = "bbaqiedit " . $flavor;
            $lpn = "&#10097;";
            $rml = "visibility:hidden;";
            $rmr = 'display:none';
        } else
        if ($flavor === "evlistsub") { /* eventlist: sub section */
            //&#10551; &#8627; &#8630; &#10097;
            $flavor = "bbaqiedit " . $flavor;
            $lpn = "&#10097;";
            $rmr = 'display:none';
        } else { /* default */
            $rml = $rmr = 'display:none';
        }

        /* include an hidden input */
        if ($name !== "") {
            $input = "<input type=\"hidden\" name=\"$name\" value=\"$value\">";
        }

        /* remove right txt */
        if ($txtr === NULL || $txtr === "") {
            $rmtr = 'display:none';
        }

        return <<<EOFL
        <div class="ttable bbaqi $flavor $class" $stl $attrib>$input
            <div class="tcell bbaqil" style="$rml">$lpn</div>
            <div class="tcell bbaqic">
                <div class="ttable qtxt">
                    <div class="tcell tl">$txtl</div>
                    <div class="tcell tr" style="$rmtr">$txtr</div>
                </div>
            </div>
            <div class="tcell bbaqir" style="$rmr">$btn</div>
        </div>
EOFL;
    }

    function bbbuildexclusivelist($fn, $items, $fia = "") {
        $res = "";
        $usedlist = "";
        $notusedoptions = "";
        $alloptions = "";
        $ct = 0;
        foreach ($items as $item) {
            // [ val, txtl, txtr, used ]
            $left = $item["2"] !== "" ? " [" .$item["2"]. "]" : "";
            $anoption = bb_af_form_inselboxitem($item["0"], $item["1"] . $left,
                        "txtl=\"$item[1]\" txtr=\"$item[2]\" oid=\"$ct\"");
            $alloptions .= $anoption;
            if ($item["3"] === true)
                $usedlist .= bb_listitem($item["1"], $item["2"], TRUE
                    , $fn, $item["0"], "", 'oid="'.$ct.'"');
            else
                $notusedoptions .= $anoption;
            $ct++;
        }
        $res .= $usedlist;
        $res .= bb_listitem(bb_af_form_inselbox("", $notusedoptions, "", fia($fia)), NULL, "add", "", "", "", 'id="theoptionslist"');
        $res .= bb_af_form_inselbox("", $alloptions, "display:none;", "", 'id="theoriginallist"');
        $res .= bb_listitem("", "", "hidden", $fn, "", "", 'id="thehidden"');
        return bb_licontainer($res);
    }

    function bbpathitem($title, $url = "", $rml = FALSE) {
        $title = bb_af_a("<div class=\"tcell tr\">$title</div>", $url); // &#10551;
        $rml = $rml ? "display: none;" : "";
        return <<<EOFL
        <div class="bbpathitem">
        <div class="ttable"><div class="tcell tl" style="$rml">&#10097;</div>$title</div>
        </div>
EOFL;
    }

    /****************************************************
                        STYLEOPTIONS & HELPERS
    /****************************************************/
    function bb_style($opts) {
        if (!isset($opts) || $opts == "") {
            return "";
        }
        $res = 'style="';
        if (is_array($opts))
            foreach ($opts as $key => $val) {
                if      ($key == "width")   $res .= "width: $val;";
                else if ($key == "align")   $res .= "text-align: $val;";
                else                        $res .= "$key$val";
            }
        else
            $res .= "$opts";
        return $res . '"';
    }
    function bb_gets($symbol) {
        switch ($symbol) {
            case "plus":
            case "+":       return "&#10010;";
            case "search":
            case "?":       return "&#9906;";
            case "tick":
            case "*":       return "&#10004;";
            case "remove": case "X":
            case "x":       return "&#10004;";
            default: return "-";
        }
    }














?>
