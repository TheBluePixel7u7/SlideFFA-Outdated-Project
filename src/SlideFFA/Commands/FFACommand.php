<?php
namespace SlideFFA\Commands;


use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\entity\Entity;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use SlideFFA\Central;
use SlideFFA\SlidePlayer;
use SlideFFA\Utils\Messages;

class FFACommand extends PluginCommand{
    private $central;

    public function __construct(Central $central){
        $this->central = $central;
        parent::__construct("ffa", $central);
        $this->setDescription("Public FFA Command.");
        $this->setAliases(["slideffa","sffa"]);
    }

    /***
     * @return Central
     */
    public function getCentral(){
        return $this->central;
    }

    public function execute(CommandSender $sender, $commandLabel, array $args){
        if(isset($args[0])){
            switch ($args[0]){
                case "help":
                case "info":
                case "ayuda":
                case "ajuda":
                    $this->helpCommand($sender);
                    break;
                case "join":
                    if(isset($args[1])){
                        $haha = $args[1];
                        $sender->sendMessage(Messages::$generalError."§4The game style called §7$haha §4not exist.");
                    }else {
                        if ($sender instanceof Player) {
                            //TODO: Add select menu when  player execute "/ffa join"
                            //$this->getCentral()->getFFAManager()->addPlayer($sender); NO JAJA
                            $this->getCentral()->getFFAManager()->addSelectMenu($sender);
                            $sender->sendMessage(Messages::$prefix.'§aSuccesfully joined!');
                        }else{
                            $sender->sendMessage(Messages::$generalError.'You must be in game to do this.');
                        }
                    }
                    break;
                case "leave":
                case "sair":
                case "salir":
                case "left":
                    if($sender instanceof Player) {
                        $this->getCentral()->getFFAManager()->removePlayer($sender);
                        $sender->sendMessage(Messages::$prefix . '§aYou have been kicked from the arena.');
                    }else{
                        $sender->sendMessage(Messages::$generalError.'You must be in game to do this.');
                    }
                    break;
                case "players":
                case "count":
                case "playing":
                    $sender->sendMessage(Messages::$prefix."§9Current players playing§f: ".$this->getCentral()->getFFAManager()->getPlayersPlayingCount());
                    break;
                /**
                 * Admin commands
                 * - Maybe in a future this will be have a Admin Panel
                 */
                case "spawnNPC":
                case "slapper":
                case "setNPC":
                case "npc":
                    if($sender instanceof Player){
                        $this->getCentral()->getFFAManager()->spawnSimpleNPC($sender);
                        $this->getCentral()->getServer()->dispatchCommand(new ConsoleCommandSender(), "save-all");
                        $sender->sendMessage(Messages::$prefix."§aA new NPC was created at: §bX§f:".$sender->getX()." §bY§F: ".$sender->getY()." §bZ§f: ".$sender->getZ()." §bWorld§f: ".$sender->getLevel()->getName());
                    }
                    return true;
                    break;
                case "kick":
                case "close":
                    if(isset($args[1])){
                        if($this->getCentral()->getServer()->getPlayer($args[1]) !== null){
                            if($args[1] instanceof Player) {
                                $this->getCentral()->getFFAManager()->removePlayer($args[1]);
                                $sender->sendMessage(Messages::$prefix . "§aSuccessfully kicked §9" . $args[1]);
                                return true;
                            }
                        }else{
                            $sender->sendMessage(Messages::$prefix."§cThis player isn't online or don't exist.");
                            return false;
                        }
                    }else{
                        $sender->sendMessage(Messages::$prefix."Missing arguments, try with this§f: §9/ffa kick {target}");
                    }
                    break;
                    //end
                default:
                    $sender->sendMessage(Messages::$prefix."§cWrong command, try §9/ffa help §cfor more information about.");
            break;
            }

        }else{
            $sender->sendMessage(Messages::$prefix. 'Wrong usage!');
            $sender->sendMessage(Messages::$prefix. '§fNeed help?§b /ffa help');
            return true;
        }
        return false;
    }
     // TODO: better system haushuahshuahsuhuashushauhsuah xd.
    public function helpCommand(CommandSender $sender){
        $sender->sendMessage('Tengo que terminar esto :v');
    }
}
/*

Mamadas ;v

else {
                        $nbt = $this->getCentral()->getFFAManager()->advancedNPC($args[2], $sender);
                        $entity = Entity::createEntity("Slide" . $args[2], $sender->getLevel(), $nbt);
                        $entity->setNameTagVisible(true);
                        $entity->setNameTagAlwaysVisible(true);
                        $sender->sendMessage(Messages::$prefix."§aSuccessfully added new NPC.");
                    }
 */