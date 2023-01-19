<?php
class TooltipSingleton
{
    private static $instance;
    
    private function __construct() {}
    
    public function getInstance()
    {
        if (!isset(self::$instance))
        {
            self::$instance = new GtkTooltips;
        }
        return self::$instance;
    }
}
