<?php
    /* check for config file */
    if (isset($noconfigfile)) {
        echo "Please create and configure the config.php file.";
        return;
    }

    /* DB and AUTH core module */
    $dbauth = require_once("dbauth.php");

    /* set timeout */
    $dbauth->setproperty("sessiontimeout",
        isset($sessiontimeoutminutes) ? $sessiontimeoutminutes : 60 * 24);

    /* get rid of every single shit */
    DBAUTH::sanitizerequest();

    /* DB connection */
    if (($res = $dbauth->connectdb("pgsql:host=$dbhost;port=$dbport;dbname=$dbname", $dbuser, $dbpass)) !== TRUE) {
        echo $res;
        return;
    }

    /* start and check for session */
    $dbauth->checksession();

    /* auto-body ui builder */
    require_once("bodybuilder.php");

    /* a ping-pong request - avoid timeout */
    if (isgd("ping") === "pong") {
        return;
    }

    /* check if user wantted to logout */
    $lerror = "";
    if (issetg("logout")) {
        $dbauth->logout();
    }
    /* try to login */
    else if (issetp("biblobu") || issetp("bible")) {
        if (!$dbauth->login(ispd("biblobu"), ispd("bible")) && !$dbauth->loginstate())
            $lerror = 'NOTBELONG';
    } else
    /* try to passwd */
    if (issetp("passwd") || issetp("passwdconfirm")) {
        $lerror = $dbauth->passwd(ispd("passwd"), ispd("passwdconfirm"));
    } else
    /* pick a role */
    if (issetp("rolechoser")) {
        $dbauth->pickrole(ispd("rolepick"));
    }

    /* supported languages */
    $languagepacks = [
        "en-uk" => "English",
        "pt-pt" => "Português",
        //"en-funny" => "Funny",
    ];
    $defaultlp = "en-uk";

    /* get the current user-choice language (cookies) OR defaul */
    $loadedlangue = $defaultlp;
    $setlang = NULL;
    if (issetg("language"))
        $setlang = isgd("language");
    $loadedlangue = $dbauth->cookitizeuser("LP", $loadedlangue, TRUE, $setlang);

    /* available themes */
    if (isset($availablethemes) && is_array($availablethemes)) {
        $themeslist = $availablethemes;
    } else {
        $themeslist = [
            ["The Pink Panther",
                "css/themelightpink.css"],
            ["The Dark Side of The Pink Panther",
                "css/themedarkpink.css"],
            ["Join The Dark Side",
                "css/themedarkcyan.css"],
            ["Join The Force",
                "css/themelightcyan.css"],
            ["Dark as Day",
                "css/themedarkasday.css"],
            ["Light as Night",
                "css/themelightasnight.css"],
            ["Dark Nap",
                "css/themedarknap.css"],
            ["Light Sleep",
                "css/themelightsleep.css"],
            ["Bluetooth",
                "css/themebluetooth.css"],
            ["Blue pie",
                "css/themebluepie.css"],
            ["Red Pipe",
                "css/themeredpipe.css"],
            ["Reddish Month",
                "css/themeredmoth.css"],
        ];
    }
    /* set them based on cookies */
    $loadedtheme = $themeslist[0][0];
    $loadedtheme = $dbauth->cookitizeuser("UT", $loadedtheme, TRUE);

    /* get syslanguage */
    $lp = loadsyslanguage($dbauth->gethomedir() . "uimodules/languages/uilpsystem-", $defaultlp);
    $lp = array_merge($lp, loadsyslanguage($dbauth->gethomedir() . "uimodules/languages/uilpsystem-", $loadedlangue));

    /* if logged in */
    $thebody   = error404();
    $thebodyid = "thebodywhenerror";
    if ($dbauth->loginstate() === TRUE) {
        //call_user_func([$dbauth, "querydb"]);
        // TODO this is a workaround ! !
        function db_do($query, $params = "", $desc = "") {
            global $dbauth;
            return $dbauth->querydb($query, $params, $desc);
        }
        function db_beggin() {
            global $dbauth;
            return $dbauth->beggindb();
        }
        function db_commit() {
            global $dbauth;
            return $dbauth->commitdb();
        }

        /* base UI module */
        require_once("uimodule.php");

        /* desired modules to be loaded */
        $modulespath = [
            "uimodules/sysadmin.php",
            "uimodules/usermanagement.php",
            "uimodules/organizations.php",
            "uimodules/questions.php",
            "uimodules/activities.php",
            "uimodules/events.php",
            "uimodules/patients.php",
            "uimodules/dbdump.php",
        ];

        /* load modules */
        $loadedmodules = [];
        foreach ($modulespath as $m) {
            $module = include_once($m);
            $module->setlanguage($loadedlangue);
            $module->setrole($dbauth->getuserid(), $dbauth->getroleid());
            $loadedmodules[] = $module;
        }

        /* list the ones for the session's role */
        $uimodules = [];
        foreach ($loadedmodules as $module) {
            if ($module->getmenu())
                $uimodules[] = $module;
        }

        /* TODO sort the modules, if needed */
        

        /* build menu based on loaded modules */
        $themenu = [];
        foreach ($uimodules as $m) {
            $mitem = $m->getmenu();
            $themenu[$mitem["url"]] = $mitem;
        }

        $menu = bb_leftmenu($dbauth->getuname(""), $themenu, isgd("m"), lpgetstr($lp, "btn2"));

        /* the body content */
        $thebody   = "";
        $thebodyid = "thebody";
        $title = isset($title) ? $title : "Bee-O";
        $header  = bb_af_sep(bb_img($dbauth->gethomeurl("imgs/bee.gif"), "", "bbticon"), $title, "", "bbaftsep");
        $header .= bb_buildlangchooser($languagepacks, $loadedlangue, "inmain");


        $body = "";
        if (valg("m")) {
            if (isset($themenu[isgd("m")])) {
                $urlhome = arrcpy($_GET, ["m"]);
                $body = $themenu[isgd("m")]["module"]->getpage($urlhome);
                /* we may be asked to do not output a word */
                if ($body === NULL) return;
            }
            else
                $body = error404();
        } else {
            $body .= lpgetstr($lp, "str3");
        }


        /* ...and super jet speed request! (ajax) */
        if (issetg("jetspeed")) {
            echo '<div style="display:none;" id="loadintoinnerbody"></div>';
            echo $body;
            return;
        }

        $footer   = bbjswhodidthis("Noeljunior", "https://github.com/Noeljunior/beeo");
        $footer  .= bb_builthemeselector("inmain", $themeslist, $loadedtheme);

        /* build the skeleton */
        $thebody .= bbmainskeleton($header, $menu, $body, $footer);
    }
    /* LOGGED OUT */
    else {
        $thebody    = "";
        $thebodyid  = "bodylogin";
        $thebody    = bb_builthemeselector("inlogin", $themeslist, $loadedtheme);
        $thebody   .= bb_buildlangchooser($languagepacks, $loadedlangue, "inlogin");

        if      ($lerror === "NOTBELONG")
            $lerror = lpgetstr($lp, "err1");
        else if ($lerror === "NOMATCH")
            $lerror = lpgetstr($lp, "err3");
        else if ($lerror === "EQUALSUSERNAME")
            $lerror = lpgetstr($lp, "err5");
        else if ($lerror === "TOOSMALL")
            $lerror = lpgetstr($lp, "err2", $dbauth->getminpasslen());
        else if ($lerror === "NOSTATE")
            $lerror = "";
        else if ($lerror !== "")
            $lerror = lpgetstr($lp, "err4");


        $body = "";
        if ($dbauth->loginstate('PICKROLE')) {
            $ltitle = lpgetstr($lp, "str2");
            $body .= lui_pickrole($ltitle, $lerror,
                $dbauth->getuseroles(), lpgetstr($lp, "btn4"));
        } else if ($dbauth->loginstate('PICKPASS')) {
            $ltitle = lpgetstr($lp, "inf1");
            $body .= lui_passwd($ltitle, $lerror,
                lpgetstr($lp, "lbl2"), lpgetstr($lp, "lbl3"),
                lpgetstr($lp, "btn1"), lpgetstr($lp, "btn4"));
        } else {
            $ltitle = lpgetstr($lp, "str1");
            $body .= lui_login($ltitle, $lerror,
                lpgetstr($lp, "lbl1"), lpgetstr($lp, "lbl2"));
        }
        $thebody .= ll_loginskeleton($body);
    }

    /****************************************************
                       TO THE CLIENT SIDE
    /****************************************************/
    $favicon   = $dbauth->gethomeurl("imgs/icon.png");
    $cssthemes = bb_buildcssref($themeslist, $loadedtheme);
    $htmltitle = isset($htmltitle) ? $htmltitle : "Bee-O";

    /* log-state independent components */
    $prethebody = "";
    /* show cookies warning */
    $prethebody .= ll_cookies($dbauth->showcookie(), $dbauth->getakname(), lpgetstr($lp, "btn3"));
    /* tell the browser who he is */
    $prethebody .= bb_logininfo($dbauth->getuserid("sys"),
                                $dbauth->getroleid("sys"),
                                $dbauth->getsessionname());
    /* ajax load/error animation */
    $prethebody .= bbjsajaxanim($dbauth->gethomeurl("imgs/bee.gif"));

    echo <<<HELLYEAH
    <html><head>
        <link rel="icon shortcut" type="image/png" href="$favicon">
        <title>$htmltitle</title>

        <link rel="stylesheet" type="text/css" href="css/main.css">

        $cssthemes

        <script src="js.js"></script>

        </head>
        <body id="$thebodyid">
            $prethebody
            $thebody
        </body>
    </html>
HELLYEAH;
?>
