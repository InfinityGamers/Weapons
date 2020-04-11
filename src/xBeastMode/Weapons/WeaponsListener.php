<?php
namespace xBeastMode\Weapons;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
class WeaponsListener implements Listener{
        /** @var Weapons */
        protected $core;

        /**
         * @param Weapons $core
         */
        public function __construct(Weapons $core){
                $this->core = $core;
        }

        /**
         * @param PlayerInteractEvent $event
         */
        public function onInteract(PlayerInteractEvent $event){
                $item = $event->getItem();
                $player = $event->getPlayer();

                if($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_AIR){
                        if(!$this->core->isGunAllowed($item, $player->level)){
                                $player->sendTip("§cWeapons not allowed here.");
                                RandomUtils::playSound("random.break", $player);

                                return;
                        }

                        if($item->hasCustomBlockData() && $item->getCustomBlockData()->hasTag("gunType")){
                                $gunType = $item->getCustomBlockData()->getString("gunType");
                                if(GunData::getFullAuto($gunType)){
                                        $this->core->toggleGun($player);
                                }else{
                                        if($this->core->fire($player, $item)){
                                                $gunType = $item->getCustomBlockData()->getString("gunType");
                                                RandomUtils::playSound("firework.blast", $player, 500, GunData::getShotPitch($gunType));
                                        }else{
                                                RandomUtils::playSound("random.click", $player, 500, 0.5);
                                                $player->sendTip("§cOut of ammo.");
                                        }
                                }
                        }
                }
        }
}