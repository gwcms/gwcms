<?php

class GW_TplEngine_Security extends Smarty_Security 
{

    public $php_modifiers = null;
     public $php_functions = null;
    
				public function isTrustedPhpFunction($function_name, $compiler){ return true; } 
				public function isTrustedResourceDir($filepath, $isConfig = null){ return true; } 
				public function isTrustedTag($tag_name, $compiler){ return true; } 
				public function isTrustedStaticClassAccess($class_name, $params, $compiler){ return true; } 
				public function isTrustedPhpModifier($modifier_name, $compiler){ return true; } 
				public function isTrustedConstant($const, $compiler){ return true; } 
				public function isTrustedModifier($modifier_name, $compiler){ return true; } 
				public function isTrustedSpecialSmartyVar($var_name, $compiler){ return true; } 
}
