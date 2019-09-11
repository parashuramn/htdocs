<?php
class GrassBladeAddons {
        private $addons;
        private $addons_dir;
        function __construct($addon_dir = null)
        {
                if(empty($addon_dir))
                {
                        if(defined('GRASSBLADE_ADDON_DIR'))
                        $this->addons_dir = GRASSBLADE_ADDON_DIR;
                        else
                        $this->addons_dir = APP."AddOns".DS;
                }
                else
                $this->addons_dir = $addon_dir;
                $this->addons = $this->GetAddons();
        }

        public function GetAddons()
        {
                if(!empty($this->addons))
                return $this->addons;

                $list = scandir($this->addons_dir);

                $addons = array();

                foreach($list as $list_item)
                {
                        if(is_dir($this->addons_dir."/".$list_item) && $list_item != "." && $list_item != "..")
                        $addons[] = $list_item;
                }

                $this->addons = modified("addons", $addons);
                return $addons;
        }

        public function GetAddonFile($addon, $filepath)
        {
                $file = $this->addons_dir."/".$addon."/".$filepath;

                if(file_exists($file))
                return $file;
                else
                return false;
        }
        public function GetHelpFile($addon)
        {
                return $this->GetAddonFile($addon, "help.php");
        }
        public function GetFunctionFile($addon)
        {
                return $this->GetAddonFile($addon, "functions.php");
        }

        public function IncludeFile($file)
        {
                if(file_exists($file))
                {
                        include($file);
                        return true;
                }
                else
                        return false;
        }

        public function IncludeFunctionFiles()
        {
                $addons = $this->addons;
                if(count($addons))
                foreach($addons as $addon)
                {
                        $this->IncludeFile($this->GetFunctionFile($addon));
                }
        }

        public function IncludeHelpFiles()
        {
                $addons = $this->addons;

                if(count($addons))
                foreach($addons as $addon)
                {
                        $this->IncludeFile($this->GetHelpFile($addon));
                }
        }
}
