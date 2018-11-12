<?php
namespace SlideFFA;

use pocketmine\entity\Entity;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use SlideFFA\Commands\FFACommand;
use SlideFFA\Managers\Events\ArenaEvents;
use SlideFFA\Managers\Events\PlayerEvents;
use SlideFFA\Managers\FFAManager;
use SlideFFA\Utils\NPC\SlideEntity;
use SlideFFA\Utils\NPC\SlideHuman;
use SlideFFA\Utils\Messages;

class Central extends PluginBase{
    /***
     * @var Central
     */
    private static $central;
    /*** @var FFAManager */
    private $ffaManager;
    /***
     * @var Config $config2
     */
    public $config2;
    public static function getCentral(){
        return self::$central;
    }
    public function onEnable()
    {
        Entity::registerEntity(SlideHuman::class, true);
        Entity::registerEntity(SlideEntity::class, true);
        $this->getServer()->getCommandMap()->register("/ffa", new FFACommand($this));
       // $this->getServer()->getCommandMap()->register("/adminffa", new CreatorCommand($this));
        //$this->getServer()->getCommandMap()->register("/ccffa", new AdminCommand($this));
        $this->loadListeners();
        $this->loadManagers();
        $this->saveDefaultConfig();
        //Load messages
        Messages::loadMessages();
        @mkdir($this->getDataFolder());
        $config2 = new Config($this->getDataFolder(). "data.yml", Config::YAML);
        $this->getLogger()->notice("§9A public project by
        §3Slide§4Network§9 & §3Slide§6Plugins§9!");
        /*
         * Load Areans (si no se va ala mrd :v)
         */
        $this->getServer()->loadLevel("gapple-ffa");
    }

    /***
     * @return FFAManager
     */
    public function getFFAManager(){
        return $this->ffaManager;
    }

    /***
     * @return Config
     */
    public function getDataConfig(){
        return $this->config2;
    }
    public function loadManagers(){
        $this->ffaManager = new FFAManager($this);
    }
    public function loadListeners(){
        new PlayerEvents($this);
        new ArenaEvents($this);
    }
}