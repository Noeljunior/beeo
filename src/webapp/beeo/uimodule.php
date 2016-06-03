<?php
    /* an UI module to be used everywhere */
    class uimodule {
        protected $moduleid    = 'This is an invalid or bad used ui_module';
        protected $menutxt     = 'This is an invalid or bad used ui_module';
        protected $url         = 'This is an invalid or bad used ui_module';
        protected $mpref       = 0;
        protected $role        = 'This is an invalid or bad used ui_module';
        protected $permissions = [];

        protected $linfo       = "";
        protected $lerror      = "";

        protected $lang        = "en-uk";
        protected $langdir     = "uimodules/languages/";
        protected $langpack    = [];

        public function setrole($userid, $roleid) {
            $this->role = $roleid;
            $this->user = $userid;
            /* update permissions */
            $dbres = db_do(self::getsql("getperms"),
                ["rid"    => $roleid,
                 "module" => "uim_" . $this->moduleid,
                ]);
            foreach ($dbres as $row) {
                $this->permissions[$row["mode"]] = true;
            }
        }

        public function getmenu() {
            if (isset($this->permissions["dv"]))
                return [
                    "menutxt" => $this->menutxt,
                    "url"     => $this->url,
                    "mpref"   => $this->mpref,
                    "module"  => $this,
                 ];
            return FALSE;
        }

        public function getname() {
            return isset($this->modulename) ? $this->modulename : $this->moduleid;
        }

        public function getid() {
            return $this->moduleid;
        }

        public function getpage($home) {
            return 'This is an invalid or bad used ui_module';
        }

        public function getpermissions() {
            /* on child modules, this should return an array containing the
                possible permissions for the given module */
            return [];
        }

        protected function cando($what) {
            if (isset($this->permissions[$what]))
                return $this->permissions[$what];
            return FALSE;
        }

        function __construct() {
            $this->langdir = dirname(__FILE__) . "/" . $this->langdir;
            $this->setlanguage();
        }

        /* languages packs */
        public function setlanguage($lang = "en-uk", $com = "uilpcom-") {
            /* load language pack */
            $commonlpf      = $this->langdir . $com   .                         $lang . ".php";
            $modulelpf      = $this->langdir . "uilp" . $this->moduleid . "-" . $lang . ".php";
            $commonlp       = file_exists($commonlpf) ? include($commonlpf) : [];
            $modulelp       = file_exists($modulelpf) ? include($modulelpf) : [];
            $this->langpack = array_merge($this->langpack, $commonlp, $modulelp);

            $this->modulename = $this->getstr("modulename");
            $this->menutxt    = $this->getstr("menutxt");
        }

        protected function getstr($id, $a = "?", $b = "?", $c = "?", $d = "?", $e = "?") {
            if (isset($this->langpack[$id]))
                return sprintf($this->langpack[$id], $a, $b, $c, $d, $e);
            return "???" . $id . "???";
        }

        /****************************************************
                           SQL STATEMENTS
        /****************************************************/
        public static function getsql($what) {
            if ($what === "getperms") return <<<EOFF
            SELECT * FROM permissions
                WHERE roleid = :rid
                  AND module = :module;
EOFF;
            else if ($what === "") return <<<EOFF
EOFF;
            return "";
        }
    }
?>
