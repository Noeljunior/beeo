<?php
    class DBAUTH {
        /****************************************************
                          PUBLIC INTERFACE
        /****************************************************/
        /* * * * * * * * * * * * * * * * * * * * * DB STUFF * * */
        public function connectdb($dsn, $user, $password) {
            return $this->doconnectdb($dsn, $user, $password);
        }

        /* query something on db */
        public function querydb($query, $params = "", $desc = "", $type = "AUTO") {
            $desc = $desc === FALSE ? "" : $desc;
            return $this->doquerydb($query, $params, $desc, $type);
        }

        /* transaction control */
        public function beggindb() {
            return $this->dbo->beginTransaction();
        }
        public function commitdb() {
            return $this->dbo->commit();
        }
        public function rollbackdb() {
            return $this->dbo->rollBack();
        }

        /* log something to DB */
        public function logdb($str) {
            $this->dologdb("USER", $str);
        }

        /* * * * * * * * * * * * * * * * * * * * * AUTH STUFF * * */
        public function checksession() {
            return $this->_checksession();
        }
        public function showcookie() {
            return isset($_COOKIE[$this->SESSIONNAME . DBAUTH::$USECOOKIE]) && $_COOKIE[$this->SESSIONNAME . DBAUTH::$USECOOKIE] === "yes" ? FALSE : TRUE;
        }
        public function cookitizeuser($cookie, $default, $json = FALSE, $setval = NULL) {
            return $this->_cookitizeuser($cookie, $default, $json, $setval);
        }
        public function login($user = "", $pass = "") {
            return $this->_login($user, $pass);
        }
        public function pickrole($roleid = "") {
            return $this->_pickrole($roleid);
        }
        public function loginstate($state = "") {
            return $this->_loginstate($state);
        }
        public function logout($killall = FALSE) {
            return $this->_logout("Explicit logged out", $killall);
        }
        public function passwd($passa, $passb) {
            return $this->_passwd($passa, $passb);
        }
        public function getuname($def = FALSE) {
            return isset($_SESSION[$this->SESSIONNAME]["uname"]) ? $_SESSION[$this->SESSIONNAME]["uname"] : $def;
        }
        public function getusername($def = FALSE) {
            return isset($_SESSION[$this->SESSIONNAME]["username"]) ? $_SESSION[$this->SESSIONNAME]["username"] : $def;
        }
        public function getuserid($def = FALSE) {
            return isset($_SESSION[$this->SESSIONNAME]["userid"]) ? $_SESSION[$this->SESSIONNAME]["userid"] : $def;
        }
        public function getroleid($def = FALSE) {
            return isset($_SESSION[$this->SESSIONNAME]["roleid"]) ? $_SESSION[$this->SESSIONNAME]["roleid"] : $def;
        }
        public function getrolename($def = FALSE) {
            return isset($_SESSION[$this->SESSIONNAME]["rolename"]) ? $_SESSION[$this->SESSIONNAME]["rolename"] : $def;
        }
        public function getuseroles() {
            return isset($_SESSION[$this->SESSIONNAME]["userroles"]) ? $_SESSION[$this->SESSIONNAME]["userroles"] : FALSE;
        }
        public function getminpasslen() {
            return $this->llmpl;
        }

        /* * * * * * * * * * * * * * * * * * * * * MISC STUFF * * */
        public function getsessionname() {
            return $this->SESSIONNAME;
        }
        public function getakname() {
            return $this->SESSIONNAME . DBAUTH::$USECOOKIE;
        }
        public function gethomedir($suffix = "") {
            return $this->llhomedir . $suffix;
        }

        public function gethomeurl($suffix = "") {
            return $this->llhomeurl . $suffix;
        }

        public function setproperty($key, $val) {
            switch ($key) {
                case "passcost":         $this->PASSWORDCOST = $val;     break;
                case "sessname":         $this->SESSIONNAME  = $val;     break;
                case "minpasslen":       $this->llmpl        = $val;     break;
                case "refreshstate":     $this->llcer        = $val;     break;
                case "sessiontimeout":   $this->llcto        = $val;     break;
            }
        }

        /* * * * * * * * * * * * * * * * * * * * * static public helpers * * */
        public static function sanitizerequest() {
            /* TODO REFER:
                http://php.net/manual/en/filter.filters.sanitize.php
                http://php.net/manual/en/function.array-filter.php
            */
            /* before everything, remove all trash from GET and POST vars */
            function array_strip($arr) {
                foreach($arr as $key => $val) {
                    if (is_array($arr[$key]))
                        array_strip($arr[$key]);
                    else
                        $arr[$key] = strip_tags(trim($val));
                }
            }

            /* remove html trash */
            array_strip($_POST);
            array_strip($_GET);
        }

        /****************************************************
                           DATA BASE STUFF
        /****************************************************/
        private $PASSWORDCOST   = 12;
        private $dbo            = NULL;

        private function doconnectdb($dsn, $user, $password) {
            try {
                $this->dbo = new PDO($dsn, $user, $password);
            }
            catch (Exception $e) {
                $this->dbo = NULL;
                return "It's beeing too hard to connect to the database. Double check your settings";
            }
            $this->dbo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            /* connection is OK, check for correct dbschema */
            if (!($res = $this->querydb(self::getsql("checkua")))) {
                if (!isset($_POST["confirmresetdbschema"])) {
                    die(self::html_newschema());
                } else {
                    $schemapath = dirname(__FILE__) . "/dbschema.php";
                    if (!file_exists($schemapath)) {
                        die("There is no schema installed. Get one and repeat the procedure.");
                    }
                    $theschema = include($schemapath);

                    $this->dbo->setAttribute(PDO::ATTR_EMULATE_PREPARES, 0);
                    try {
                        if ($this->dbo->exec($theschema) === FALSE) {
                            die("Some error occurred while creating a new DB schema. Check your PostgreSQL's user permissions and/or your PostgreSQL's version.");
                        }
                    }
                    catch {
                        die("Some error occurred while creating a new DB schema. Check your PostgreSQL's user permissions and/or your PostgreSQL's version.");
                    }
                    $this->dbo->setAttribute(PDO::ATTR_EMULATE_PREPARES, 1);

                    die(self::html_newschemacreated());
                }
            }

            return TRUE;
        }

        private function doquerydb($query, $params = "", $desc = FALSE, $type = "AUTO") {
            if ($this->dbo === NULL) return NULL;
            try {
                $sth = $this->dbo->prepare($query);
                if (is_array($params))
                    foreach ($params as $key => &$val)
                        $sth->bindParam(":".$key, $val);
                $sth->execute();
            } catch (PDOException $e) {
                error_log("DBQERROR: " . $e->getMessage());
                return NULL;
            }
            $dbres = $sth->fetchAll();

            /* auto log */
            if ($desc !== FALSE && is_array($dbres) && count($dbres) >= 1) {
                if ($desc == "") {
                    if (
                        (strpos($query, 'INSERT INTO') !== false) ||
                        (strpos($query, 'UPDATE') !== false) ||
                        (strpos($query, 'DELETE') !== false)
                        ) {
                        $desc = "SQL: \"" . $query . "\"";
                        $type = "AUTOCOMP";
                        error_log("AUTOCOMPLOG: " . $query);
                    }
                }
                if ($desc != "" && $desc !== "IGNOREPLAEASE") {
                    $this->dologdb($type, $desc);
                    //error_log("DBLOG: " . $query . " ::: " . $desc);
                }
            }
            return $dbres;
        }

        private function dologindb($user, $pass) {
            $log["STATUS"] = FALSE;

            $res = $this->doquerydb(self::getsql("getuserbyusername"), ["user" => $user]); // TODO bring the user roles already

            if ($res !== FALSE && count($res) === 1) {
                $res = $res[0];
                $upass = $res["password"];

                /* give userid for logging purporses */
                $log["userid"] = $res["id"];
                $log["name"]   = $res["name"];

                /* bad password or expired password */
                if ($upass === "" || password_verify($user, $upass)) {
                    $log["STATUS"] = DBAUTH::$LSPICKPASS;
                    return $log;
                }

                /* password does not match */
                if (!password_verify($pass, $upass))
                    return $log;

                /* return login info */
                $log["STATUS"] = TRUE;

                return $log;
            }
            return $log;
        }

        private function dopasswddb($user, $pass) {
            $pass = password_hash($pass, PASSWORD_DEFAULT, ['cost' => $this->PASSWORDCOST]);
            $res = $this->doquerydb(self::getsql("setuserpasswd"), ["user" => $user, "pass" => $pass]);
            if (is_array($res) && count($res) === 1)
                return TRUE;
            else
                return FALSE;
        }

        private function dologdb($type, $desc = "", $who = "", $userid = NULL, $roleid = NULL) {
            $userid = $this->getuserid("0");
            $roleid = $this->getroleid(NULL);
            $roleid = $roleid < 0 ? NULL : $roleid;
            if ($who == "") {
                $who .= '"' . $this->getuname() . '" ';
                $who .= '(' . $this->getusername() . ') ';
                if ($roleid !== NULL)
                    $who .= 'as "' . $this->getrolename("?") . '"';
                else
                    $who .= "";
            }
            $desc = ($desc === "" ? "UNKNOWN" : $desc);
            $type = ($type === "" ? "LAUTO" : $type);

            $this->doquerydb(self::getsql("log"), [
                "userid"        => $userid,
                "roleid"        => $roleid,
                "type"          => $type,
                "who"           => $who,
                "description"   => $desc
            ]);
        }

        /****************************************************
                           AUTH STUFF
        /****************************************************/
        private static $USECOOKIE   = 'AK';
        private static $LSOFF       = FALSE;
        private static $LSPICKROLE  = 'PICKROLE';
        private static $LSPICKPASS  = 'PICKPASS';
        private static $LSON        = TRUE;
                                            /* CONFIGS */
        private $SESSIONNAME        = 'BEEO';   /* session name */
        private $llcer              = FALSE;    /* check every request login state */
        private $llcto              = FALSE;    /* check for session timeout, in minuts */
        private $llmpl              = 6;        /* minimum pass lengh */


        private function session($key, $val = NULL, $default = FALSE) {
            if ($val === NULL)
                return isset($_SESSION[$this->SESSIONNAME][$key]) ? $_SESSION[$this->SESSIONNAME][$key] : $default;
            return ($_SESSION[$this->SESSIONNAME][$key] = $val);
        }

        private function _checksession() {
            session_name($this->SESSIONNAME);
            session_set_cookie_params(0, dirname($_SERVER["PHP_SELF"]), $_SERVER["HTTP_HOST"], TRUE);
            session_start();

            /* check if there is a session */
            if (!isset($_SESSION[$this->SESSIONNAME])) {
                $_SESSION[$this->SESSIONNAME] = [];
            }

            /* check timeout */
            if ($this->llcto !== FALSE &&
                (isset($_SESSION[$this->SESSIONNAME]["LASTREQ"]) && (time() - $_SESSION[$this->SESSIONNAME]["LASTREQ"] > ($this->llcto * 60)))) {
                $this->_logout("timout");
            }
            $this->session("LASTREQ", time());

            /* avoid session fixation atack: every 5 mins */
            if (!isset($_SESSION[$this->SESSIONNAME]["FIRSTREQ"])) {
                $_SESSION[$this->SESSIONNAME]["FIRSTREQ"] = time();
            } else if (time() - $_SESSION[$this->SESSIONNAME]["FIRSTREQ"] > (5 * 60)) {
                session_regenerate_id(true);
                $_SESSION[$this->SESSIONNAME]["FIRSTREQ"] = time();
            }

            /* set home directory and home url */
            $this->llhomedir = dirname(__FILE__) . "/";
            $this->llhomeurl = dirname($_SERVER["PHP_SELF"]) . "/";

            /* TODO config: check every request login state */
            if ($this->llcer === TRUE) {
                error_log("DBAUTH: [NYI] checking every request login state");
                // update session
            }

            /* OK, go on */
            return TRUE;
        }

        private function _cookitizeuser($cookie, $default, $json = FALSE, $setval = NULL) {
            $userid = $this->getuserid("sys");
            $roleid = $this->getroleid("sys");

            $cookie = $this->SESSIONNAME . $cookie;

            $array = [];
            if ($json && isset($_COOKIE[$cookie]))
                $array = json_decode($_COOKIE[$cookie], TRUE);
            else if (isset($_COOKIE[$cookie]))
                $array = unserialize($_COOKIE[$cookie]);

            $array = is_array($array) ? $array : [];
            //var_dump($array);

            if ($setval !== NULL) {
                $array[$userid][$roleid] = $setval;
                if ($json)
                    $serialize = json_encode($array);
                else
                    $serialize = serialize($array);

                setcookie($cookie, $serialize);
            }

            if (isset($array[$userid][$roleid]))
                $default = $array[$userid][$roleid];


            return $default;
        }

        private function _login($user = "", $pass = "") {
            if ($this->session("LOGGEDIN") !== DBAUTH::$LSOFF) {
                return FALSE;
            }

            $log = $this->dologindb($user, $pass);
            if (!is_array($log) || $log["STATUS"] === FALSE) {
                return FALSE;
                //$lerror = "you do not belong here";
            } else
            if ($log["STATUS"] === DBAUTH::$LSPICKPASS) { /* NON-SET OR EXPIRED PASSWORD */
                $this->session("LOGGEDIN", DBAUTH::$LSPICKPASS);
                $this->session("username", $user);
                $this->session("userid",   $log["userid"]);
                $this->session("uname",    $log["name"]);
                $this->dologdb("LOGIN", "Logged in and was asked to change the password");
                return FALSE;
            } else
            if ($log["STATUS"] === TRUE) {
                /* check the user roles */
                $roles = $this->doquerydb(self::getsql("getuserroles"), ["user" => $log["userid"]]);
                if (count($roles) < 1) {
                    $this->_logout("user with no roles assigned");
                    return FALSE;
                } else if (count($roles) === 1) {
                    $this->session("LOGGEDIN", DBAUTH::$LSON);
                    $this->session("username", $user);
                    $this->session("userid",   $log["userid"]);
                    $this->session("uname",    $log["name"]);
                    $this->session("roleid",   $roles["0"]["id"]);
                    $this->session("rolename", $roles["0"]["name"]);
                    $this->dologdb("LOGIN", "Now acting as '".$roles["0"]["name"]."'");
                    return TRUE;
                } else if (count($roles) > 1) {
                    $this->session("LOGGEDIN",  DBAUTH::$LSPICKROLE);
                    $this->session("username",  $user);
                    $this->session("userid",    $log["userid"]);
                    $this->session("uname",     $log["name"]);
                    $this->session("roleid",    -1);
                    $this->session("userroles", $roles);
                    $this->dologdb("LOGIN", "Logged in and was asked to pick a role");
                    return FALSE;
                }
            }
            return FALSE;
        }

        private function _pickrole($roleid = "") {
            if ($this->session("LOGGEDIN") === DBAUTH::$LSPICKROLE &&
                $this->session("roleid")   === -1) {
                foreach ($this->session("userroles") as $role) {
                    if ("$role[id]" === $roleid) {
                        $this->session("LOGGEDIN", DBAUTH::$LSON);
                        $this->session("roleid",   $role["id"]);
                        $this->session("rolename", $role["name"]);
                        $this->dologdb("LOGIN", "Now acting as '".$role["name"]."'");
                        return TRUE;
                    }
                }
            }
            return FALSE;
        }

        private function _passwd($passa, $passb) {
            if ($this->session("LOGGEDIN") !== DBAUTH::$LSPICKPASS) {
                return 'NOSTATE';
            } else
            if ($passa !== $passb) {
                return 'NOMATCH';
            } else
            if (strlen($passa) < $this->llmpl) {
                return 'TOOSMALL';
            } else
            if ($passa === $this->session("username")) {
                return 'EQUALSUSERNAME';
            } else
            if ($this->dopasswddb($this->session("username"), $passa)) {
                $this->dologdb("LOGIN", "Changed the password");
                $this->session("LOGGEDIN", DBAUTH::$LSOFF);
                return $this->_login($this->session("username"), $passb);
            }
            return FALSE;
        }

        private function _loginstate($state = "") {
            if ($state !== "")
                return $this->session("LOGGEDIN") === $state;
            return $this->session("LOGGEDIN");
        }

        private function _logout($why = "", $killall = FALSE) {
            $why = $why !== "" ? $why : "don't know why";
            $this->dologdb("LOGIN", "Logged out: " . $why);
            if ($killall === TRUE) { /* remove all session */
                session_destroy();
                session_unset();
                session_start();
            } else {
                unset($_SESSION[$this->SESSIONNAME]);
                $this->session("LOGGEDIN",  DBAUTH::$LSOFF);
            }
        }

        /****************************************************
                                HTML
        /****************************************************/
        private static function html_newschema() {
            return <<<EOFF
<html><head><style>
body { background: #881122; }
.dcenter { position:absolute; left:50%; top:50%; transform:translate(-50%, -50%);
max-width: 300px; padding:20px; background: #551122; color: #FFFFFF;}
</style></head><body>
<div class="dcenter"> Oops! It looks like the DB is not OK. I can tell you that
I <b>DO can</b> connect to the DBMS but I <b>can't find</b> the tables I would
expect.<br>
This may happen when there is no DB at all at the first install or when some
strange error occurred. If you want, I can create, or recreate, everything from
scratch. Doing that you agree that everything will be deleted! If you
think this is an error or if you want to manualy check for what happen, get out
of here now!
<br><br>
<form method="POST"><input type="submit" name="confirmresetdbschema" value="I understand and I want to reset the DB"></form>
</div>
</body></html>

EOFF;
        }
        private static function html_newschemacreated() {
            return <<<EOFF
<html><head><META HTTP-EQUIV="REFRESH" CONTENT="3"><style>
body { background: #118822; }
.dcenter { position:absolute; left:50%; top:50%; transform:translate(-50%, -50%);
max-width: 300px; padding:20px; background: #115522; color: #FFFFFF;}
</style></head><body>
<div class="dcenter">
Ok, it's done. Refresh page or wait a second.
</div>
</body></html>

EOFF;
        }

        /****************************************************
                           SQL STATEMENTS
        /****************************************************/
        private static function getsql($what) {
            switch ($what) {
                /* * * * * * * * * * * * * * * * * * * * * AUTH STUFF * * */
                case "getuserbyusername": return <<<EOFF
                SELECT useraccount.id, password, name
                    FROM useraccount
                        JOIN userroles
                            ON userroles.userid = useraccount.id
                    WHERE username = :user
                    GROUP BY useraccount.id;
EOFF;
                case "getuserroles": return <<<EOFF
                SELECT role.id, role.name, role.description
                    FROM role
                        JOIN userroles
                            ON userroles.roleid = role.id
                        JOIN useraccount
                            ON userroles.userid = useraccount.id
                    WHERE useraccount.id = :user;
EOFF;
                case "setuserpasswd": return <<<EOFF
                UPDATE useraccount SET password = :pass
                    WHERE username = :user;
EOFF;
                case "log": return <<<EOFF
                INSERT INTO userlog (date, userid, roleid, type, who, description)
                    VALUES (CURRENT_TIMESTAMP, :userid, :roleid, :type, :who, :description);
EOFF;
                case "checkua": return <<<EOFF
                SELECT id from useraccount LIMIT 1;
EOFF;
            }
            return "";
        }
    }
    return new DBAUTH();
?>
