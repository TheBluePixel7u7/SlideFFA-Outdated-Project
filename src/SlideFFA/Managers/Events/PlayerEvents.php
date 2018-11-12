<?php
namespace SlideFFA\Managers\Events;
use pocketmine\entity\Human;
use pocketmine\entity\Pig;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use SlideFFA\Central;
use SlideFFA\Utils\Messages;

class PlayerEvents implements \pocketmine\event\Listener{
    private $central;
    public function __construct(Central $central)
    {
        $this->central = $central;
        $this->central->getServer()->getPluginManager()->registerEvents($this, $central);
    }

    /***
     * Return to the central class
     * @return Central
     */
    public function getCentral(){
        return $this->central;
    }
    public function globalJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $name = $player->getName();
        $player->sendMessage("Welcome back, David! :)");
        foreach ($player->getLevel()->getEntities() as $entity){
                $entity->spawnTo($player);
                $entity->setNameTagVisible(true);
                $entity->setNameTag($entity->getNameTag());
        }
    }
    public function playerDeath(PlayerDeathEvent $event){
        $player = $event->getPlayer();
        $this->getCentral()->getFFAManager()->removePlayer($player);
    }
    public function playerQuit(PlayerQuitEvent $event){
        $player = $event->getPlayer();
        $this->getCentral()->getFFAManager()->removePlayer($player);
    }
    public function entityDamage(EntityDamageEvent $event){
        $entity = $event->getEntity();
        if($event instanceof EntityDamageByEntityEvent){
            $player = $event->getDamager();
            if($player instanceof Player){
                $space = str_repeat(" ", 10);
                if($entity->getNameTag() === $space . Messages::$prefix . $space . "\n§9Choose a gamemode and play! \n §9Current Players§f: ".$this->getCentral()->getFFAManager()->getPlayersPlayingCount()){
                    $event->setCancelled(true);
                    if($player->isCreative() && $player->isSneaking()){
                        $entity = $event->getEntity();
                        if($entity instanceof Human){
                            $entity->getInventory()->clearAll();
                            $entity->kill();
                            $player->sendMessage(Messages::$prefix."§aSuccesfully §oremoved§r§a NPC§f: ".$entity->getNameTag());
                        }
                    }else{
                        $this->getCentral()->getFFAManager()->addSelectMenu($player);
                        $player->sendMessage(Messages::$prefix."§9Select FFA Style and join in arena.");
                    }
                }
            }
        }
    }
    public function touchNPC(EntityDamageEvent $event){
        $entity = $event->getEntity();
        $space = str_repeat(" ", 10);
        if($entity->getNameTag() === $space . Messages::$prefix . $space . "\n§9Choose a gamemode and play! \n §9Current Players§f: ".$this->getCentral()->getFFAManager()->getPlayersPlayingCount()){
            $event->setCancelled(true);
        }
    }
    /*
     * PlayerInteractEvent :: Select Menu
     */
    public function playerPress(PlayerInteractEvent $event){
        $player = $event->getPlayer();
        $item = $event->getItem();
        switch ($item->getName()){
            // Gapple
            case $this->getCentral()->getConfig()->get("gapple.name"):
                if(!$this->getCentral()->getServer()->isLevelLoaded($this->getCentral()->getConfig()->get("arena.gapple.world"))){
                    $this->getCentral()->getServer()->loadLevel($this->getCentral()->getConfig()->get("arena.gapple.world"));
                    $player->sendMessage("§aLoading arena...");
                }else{
                    $x = $this->getCentral()->getConfig()->get("arena.gapple.x");
                    $y = $this->getCentral()->getConfig()->get("arena.gapple.y");
                    $z = $this->getCentral()->getConfig()->get("arena.gapple.z");
                    $arena = $this->getCentral()->getConfig()->get("arena.gapple.world");
                    $player->teleport(new Position($x, $y, $z, $this->getCentral()->getServer()->getLevelByName($arena)));
                    $this->getCentral()->getFFAManager()->setGappleKit($player);
                    $player->sendMessage(Messages::$prefix."§aSuccessfully joined in §fGapple §astyle.");
                    $this->getCentral()->getFFAManager()->addPlayer($player);
                }
                break;

            // Combo
            case $this->getCentral()->getConfig()->get("combo.name"):
                if(!$this->getCentral()->getServer()->isLevelLoaded($this->getCentral()->getConfig()->get("arena.combo.world"))){
                    $this->getCentral()->getServer()->loadLevel($this->getCentral()->getConfig()->get("arena.combo.world"));
                    $player->sendMessage("§aLoading arena...");
                }else{
                    $x = $this->getCentral()->getConfig()->get("arena.combo.x");
                    $y = $this->getCentral()->getConfig()->get("arena.combo.y");
                    $z = $this->getCentral()->getConfig()->get("arena.combo.z");
                    $arena = $this->getCentral()->getConfig()->get("arena.combo.world");
                    $player->teleport(new Position($x, $y, $z, $this->getCentral()->getServer()->getLevelByName($arena)));
                    $this->getCentral()->getFFAManager()->setGappleKit($player);
                    $player->sendMessage(Messages::$prefix."§aSuccessfully joined in §9Combo §astyle.");
                    $this->getCentral()->getFFAManager()->addPlayer($player);
                }
                break;

            // Nodebuff
            case $this->getCentral()->getConfig()->get("nodebuff.name"):
                if(!$this->getCentral()->getServer()->isLevelLoaded($this->getCentral()->getConfig()->get("arena.nodebuff.world"))){
                    $this->getCentral()->getServer()->loadLevel($this->getCentral()->getConfig()->get("arena.nodebuff.world"));
                    $player->sendMessage("§aLoading arena...");
                }else{
                    $x = $this->getCentral()->getConfig()->get("arena.nodebuff.x");
                    $y = $this->getCentral()->getConfig()->get("arena.nodebuff.y");
                    $z = $this->getCentral()->getConfig()->get("arena.nodebuff.z");
                    $arena = $this->getCentral()->getConfig()->get("arena.nodebuff.world");
                    $player->teleport(new Position($x, $y, $z, $this->getCentral()->getServer()->getLevelByName($arena)));
                    $player->sendMessage(Messages::$prefix."§aSuccessfully joined in §fNodebuff §astyle.");
                    $this->getCentral()->getFFAManager()->addPlayer($player);
                }
                break;
            // Debuff
            case $this->getCentral()->getConfig()->get("debuff.name"):
                if(!$this->getCentral()->getServer()->isLevelLoaded($this->getCentral()->getConfig()->get("arena.debuff.world"))){
                    $this->getCentral()->getServer()->loadLevel($this->getCentral()->getConfig()->get("arena.debuff.world"));
                    $player->sendMessage("§aLoading arena...");
                }else{
                    $x = $this->getCentral()->getConfig()->get("arena.debuff.x");
                    $y = $this->getCentral()->getConfig()->get("arena.debuff.y");
                    $z = $this->getCentral()->getConfig()->get("arena.debuff.z");
                    $arena = $this->getCentral()->getConfig()->get("arena.debuff.world");
                    $player->teleport(new Position($x, $y, $z, $this->getCentral()->getServer()->getLevelByName($arena)));
                    $player->sendMessage(Messages::$prefix."§aSuccessfully joined in §fDebuff §astyle.");
                }
                break;
            // Brasil
            case $this->getCentral()->getConfig()->get("br.name"):
                if(!$this->getCentral()->getServer()->isLevelLoaded($this->getCentral()->getConfig()->get("arena.br.world"))){
                    $this->getCentral()->getServer()->loadLevel($this->getCentral()->getConfig()->get("arena.br.world"));
                    $player->sendMessage("§aLoading arena...");
                }else{
                    $x = $this->getCentral()->getConfig()->get("arena.br.x");
                    $y = $this->getCentral()->getConfig()->get("arena.br.y");
                    $z = $this->getCentral()->getConfig()->get("arena.br.z");
                    $arena = $this->getCentral()->getConfig()->get("arena.br.world");
                    $player->teleport(new Position($x, $y, $z, $this->getCentral()->getServer()->getLevelByName($arena)));
                    $player->sendMessage(Messages::$prefix."§aSuccessfully joined in §fBrasil §astyle.");
                }
                break;
        }
    }
}