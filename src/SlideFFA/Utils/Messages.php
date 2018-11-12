<?php
namespace SlideFFA\Utils;


use SlideFFA\Central;

class Messages
{

    /***
     * @return Central
     */
    public function getCentral(){
        return new Central();
    }
    public static $prefix;
    public static $generalError;
    public static $playerJoin;
    public static $playerLeave;
    public static $playerDie;
    public static $player;
    public static $ticket;
    public static $gappleName;

    public static function loadMessages(){
        self::$prefix = "§8[§3Slide§4Network§8]§r ";
        self::$generalError = "§4Error§f: ";
        self::$playerJoin = "§8[§3Slide§bFFA]§6 Joining in arena...";
    }
}