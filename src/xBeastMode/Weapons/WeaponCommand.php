<?php
namespace xBeastMode\Weapons;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
class WeaponCommand extends Command implements PluginIdentifiableCommand{
        /** @var Weapons */
        protected $core;

        public function __construct(Weapons $c){
                $this->core = $c;

                $this->setPermission("command.weapon");
                parent::__construct("weapon", "weapons command", "§eUsage: /weapon", []);
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return mixed
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args){
                if(!$this->testPermission($sender)){
                        return false;
                }

                if(!$sender instanceof Player){
                        $sender->sendMessage("Please run this command in-game.");
                        return false;
                }

                /** @var Player $sender */

                if(count($args) <= 0){
                        $sender->sendMessage(RandomUtils::colorMessage("&c--=[&d&lWeapons&r&c]=--"));
                        $sender->sendMessage(RandomUtils::colorMessage("&e/weapon guns : &flist guns"));
                        $sender->sendMessage(RandomUtils::colorMessage("&e/weapon gun <gun> [player] : &fgive a weapon"));
                        $sender->sendMessage(RandomUtils::colorMessage("&e/weapon ammo <amount> [player] : &fgives a player ammo"));
                        return true;
                }

                if(strtolower($args[0]) === "guns"){
                        $sender->sendMessage(RandomUtils::colorMessage("&eGun list: &f" . implode(", ", GunData::getGunList())));
                        return true;
                }elseif(strtolower($args[0]) === "gun"){
                        if(count($args) < 2){
                                $sender->sendMessage(RandomUtils::colorMessage("&eUsage: /weapon gun <gun> [player]"));
                                return false;
                        }

                        $gun = strtolower($args[1]);

                        if(!in_array($gun, GunData::getGunList())){
                                $sender->sendMessage(TextFormat::RED . "$args[1] is not a known gun.");
                                return false;
                        }

                        $player = $sender;

                        if(isset($args[2]) && ($player = $sender->level->getServer()->getPlayer($args[2])) === null){
                                $sender->sendMessage(TextFormat::RED . "$args[2] is offline.");
                                return false;
                        }

                        $item = GunData::getGunItem($gun);
                        $item->setCustomName(RandomUtils::colorMessage("&l&4{$gun} &7[Right Click]"));
                        $item->setCustomBlockData(new CompoundTag("", [new StringTag("gunType", $gun)]));

                        $player->getInventory()->addItem($item);

                        return true;
                }elseif(strtolower($args[0]) === "ammo"){
                        if(count($args) < 2){
                                $sender->sendMessage(RandomUtils::colorMessage("&eUsage: /weapon ammo <amount> [player]"));
                                return false;
                        }

                        $amount = strtolower($args[1]);
                        $player = $sender;

                        if(isset($args[2]) && ($player = $sender->level->getServer()->getPlayer($args[2])) === null){
                                $sender->sendMessage(TextFormat::RED . "$args[2] is offline.");
                                return false;
                        }

                        $item = GunData::getAmmoItem();
                        $item->setCustomName(RandomUtils::colorMessage("&l&4AMMO"));
                        $item->setCustomBlockData(new CompoundTag("", [new IntTag("ammoAmount", $amount)]));

                        $player->getInventory()->addItem($item);
                        return true;
                }

                return true;
        }

        /**
         * @return Plugin
         */
        public function getPlugin(): Plugin{
                return $this->core;
        }
}