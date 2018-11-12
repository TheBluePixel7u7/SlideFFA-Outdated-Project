<?php
namespace SlideFFA\Managers;

use pocketmine\entity\Effect;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\level\format\FullChunk;
use pocketmine\Player;

use SlideFFA\Central;
use SlideFFA\SlidePlayer;
use SlideFFA\Utils\Messages;

class FFAManager{
    private $central;
    private $playersPlaying = [];
    public function __construct(Central $central)
    {
        $this->central = $central;
    }

    /***
     * @return Central
     */
    public function getCentral(){
        return $this->central;
    }

    public function addPlayer(Player $player){
        if(!isset($this->playersPlaying[$player->getName()])){
            $this->playersPlaying[$player->getName()] = $player;
        }else{
            $this->removePlayer($player);
            $player->sendMessage(Messages::$prefix."§8Nope dont do it this again.");
        }
    }


    public function removePlayer(Player $player){
        if(isset($this->playersPlaying[$player->getName()])){
            unset($this->playersPlaying[$player->getName()]);
            $player->getInventory()->clearAll();
            $player->removeAllEffects();
            $player->setFood($player->getMaxFood());
            $player->setHealth($player->getMaxHealth());
            $player->sendMessage(Messages::$prefix."§cLeaving from FFA§7...");
            $player->teleport($this->getCentral()->getServer()->getDefaultLevel()->getSafeSpawn());
        }

    }

    /***
     * @param Player $player
     * @param $level
     * @param Player $damager
     *
     * Hhmmhm maybe is this better :,3
     */
    public function calculateKnockBack(Player $player, $level, Player $damager){
        switch ($level) {
            case 1:
                $level = $level + 0.5;
                break;
        }
        if ($damager->getDirection() == 0) {
            $player->knockBack($player, 0, 1, 0, $level);
        } elseif ($damager->getDirection() == 1) {
            $player->knockBack($player, 0, 0, 1, $level);
        } elseif ($damager->getDirection() == 2) {
            $player->knockBack($player, 0, -1, 0, $level);
        } elseif ($damager->getDirection() == 3) {
            $player->knockBack($player, 0, 0, -1, $level);
        }
    }

    /***
     * @return int
     */
    public function getPlayersPlayingCount(){
        return count($this->playersPlaying);
    }
    public function getPlayersPlaying(){
        return $this->playersPlaying;
    }
    /*
     * Stats
     * Config players files.
     */
    public function spawnSimpleNPC(Player $player){
        $nbt = new CompoundTag;
        $nbt->Pos = new ListTag("Pos", [
            new DoubleTag("", $player->getX()),
            new DoubleTag("", $player->getY()),
            new DoubleTag("", $player->getZ())
        ]);

        $nbt->Motion = new ListTag("Motion", [
           new DoubleTag("", 0) ,
           new DoubleTag("", 0),
           new DoubleTag("", 0)
        ]);

        $nbt->Rotation = new ListTag("Rotation", [
           new FloatTag("", $player->getYaw()),
           new FloatTag("", $player->getPitch())
        ]);
        $nbt->Health = new ShortTag("Health", 20);
        $nbt->Skin = new CompoundTag("Skin", [
           "Data" => new StringTag("Data", $player->getSkinData()),
           "Name" => new StringTag("Name", $player->getSkinId())
        ]);

        $npc = new Human($player->chunk, $nbt);
        $space = str_repeat(" ", 10);
        $npc->setNameTag($space . Messages::$prefix . $space . "\n§9Choose a gamemode and play! \n §9Current Players§f: ".$this->getPlayersPlayingCount());
        $npc->setNameTagVisible((bool) true);
        $npc->spawnToAll();
        $npc->sendData($player);
        $npc->setNameTagAlwaysVisible(true);
    }

    public function advancedNPC($type, $player){
        $nbt = new CompoundTag;
        $space = str_repeat(" ", 10);
        $nbt->Pos = new ListTag("Pos", [
            new DoubleTag(0, $player->getX()),
            new DoubleTag(1, $player->getY()),
            new DoubleTag(2, $player->getZ())
        ]);

        $nbt->Motion = new ListTag("Motion", [
            new DoubleTag(0, 0),
            new DoubleTag(1, 0),
            new DoubleTag(2, 0)
        ]);

        $nbt->Rotation = new ListTag("Rotation", [
            new FloatTag(0, $player->getYaw()),
            new FloatTag(1, $player->getPitch())
        ]);

        $nbt->Health = new ShortTag("Health", 1);
        $nbt->MenuName = new StringTag("MenuName", $space . Messages::$prefix . $space . "\n§9Choose a gamemode and play! \n §9Current Players§f: ".$this->getPlayersPlayingCount());
        if ($type === "Human"){
            $nbt->Skin = new CompoundTag("Skin", [
               "Data" => new StringTag("Data", $player->getSkinData()),
                "Name" => new StringTag("Name", $player->getSkinId())
            ]);
        }
        return $nbt;
    }


    /*
     * TODO: Better system haha.
     * Kit System
     */
    public function setGappleKit(Player $player){
        $player->getInventory()->clearAll();

        $player->getInventory()->setItem(0, Item::get(276, 0, 1));
        $player->getInventory()->setItem(0, Item::get(276, 0, 1));
        $player->getInventory()->setHelmet(Item::get(310, 0, 1));
        $player->getInventory()->setChestplate(Item::get(311, 0, 1));
        $player->getInventory()->setLeggings(Item::get(312, 0, 1));
        $player->getInventory()->setBoots(Item::get(313, 0, 1));
    }
    public function setNodebuffKit(){}
    public function setDebuffKit(){}
    public function setBrasilStyleKit(){}
    public function setSGKit(){}
    public function setBUHCKit(){}

    //Join uff
    public function joinGapple(){

    }

    /*
     * -----------Select Style Menu-----------
     */
    public function addSelectMenu(Player $player){
        $player->getInventory()->setItem(1, Item::get(322, 0, 1)->setCustomName($this->getCentral()->getConfig()->get("gapple.name")));
        $player->getInventory()->setItem(2, Item::get(322, 0, 1)->setCustomName($this->getCentral()->getConfig()->get("nodebuff.name")));
        $player->getInventory()->setItem(3, Item::get(322, 0, 1)->setCustomName($this->getCentral()->getConfig()->get("debuff.name")));
        $player->getInventory()->setItem(4, Item::get(322, 0, 1)->setCustomName($this->getCentral()->getConfig()->get("br.name")));
        $player->getInventory()->setItem(5, Item::get(322, 0, 1)->setCustomName($this->getCentral()->getConfig()->get("combo.name")));
        $player->getInventory()->setItem(6, Item::get(322, 0, 1)->setCustomName($this->getCentral()->getConfig()->get("buhc.name")));
        $player->getInventory()->setItem(20, Item::get(264, 0, 1)->setCustomName("received"));
    }
    public function removeSelectMenu(Player $player){
        if($player->getInventory()->getItem(264)->getCustomName() === "received"){
            $player->getInventory()->clearAll();
        }else{
            $player->sendMessage("Error");
            $player->kill();
        }
    }
}