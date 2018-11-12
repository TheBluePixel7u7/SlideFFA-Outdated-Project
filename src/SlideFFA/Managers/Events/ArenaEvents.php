<?php
namespace SlideFFA\Managers\Events;

use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use SlideFFA\Central;
use SlideFFA\Utils\Messages;

class ArenaEvents implements Listener{
    private $central;
    public function __construct(Central $central)
    {
        $this->central = $central;
        $this->central->getServer()->getPluginManager()->registerEvents($this, $central);
    }

    /***
     * @return Central
     */
    private function getCentral(){
        return $this->central;
    }

    public function playerRespawn(){}

    public function playerDie(){}

    public function nodelayHacsk(EntityDamageEvent $event){
        if ($event instanceof EntityDamageByEntityEvent){
            $player = $event->getDamager();
            if($player->getLevel()->getName() === $this->central->getConfig()->get("arena.combo.world")){
                $event->setCancelled(false);
                $event->setKnockBack($event->getKnockBack() - ($event->getKnockBack() / 3.5));
            }
        }
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
}