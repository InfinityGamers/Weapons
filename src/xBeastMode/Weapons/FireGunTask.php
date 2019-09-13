<?php
namespace xBeastMode\Weapons;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\Player;
use pocketmine\scheduler\Task;
class FireGunTask extends Task{
        /** @var Weapons */
        protected $core;
        /** @var Player */
        protected $player;
        /** @var Item */
        protected $gun;
        /** @var Item */
        protected $ammo;
        /** @var int */
        protected $amount = 0;
        protected $slot = 0;

        /**
         * FireGunTask constructor.
         *
         * @param Weapons $core
         * @param Player              $player
         * @param Item                $gun
         */
        public function __construct(Weapons $core, Player $player, Item $gun){
                $this->core = $core;
                $this->player = $player;
                $this->gun = $gun;

                $ammo = $this->findAmmo();
                if($ammo !== false){
                        $this->ammo = $ammo;
                }
        }

        protected function cancel(){
                $this->core->toggleGun($this->player);
        }

        protected function findAmmo(){
                foreach($this->player->getInventory()->getContents() as $slot => $item){
                        if($item->hasCustomBlockData() && $item->getCustomBlockData()->hasTag("ammoAmount")){
                                $this->slot = $slot;
                                $this->amount = $item->getCustomBlockData()->getInt("ammoAmount");
                                return $item;
                        }
                }
                return false;
        }

        public function onCancel(){
                if($this->ammo !== null){
                        $this->ammo->setCustomBlockData(new CompoundTag("", [new IntTag("ammoAmount", $this->amount)]));
                        $this->player->getInventory()->setItem($this->slot, $this->ammo);
                }
        }

        /**
         * Actions to execute when run
         *
         * @param int $currentTick
         *
         * @return void
         */
        public function onRun(int $currentTick){
                if($this->amount <= 0){
                        if($this->ammo !== null){
                                if($this->ammo->count > 1){
                                        $this->player->getInventory()->setItem($this->slot, $this->ammo->setCount($this->ammo->count - 1));
                                }else{
                                        $this->ammo->setCustomBlockData(new CompoundTag("", [new IntTag("ammoAmount", $this->amount)]));
                                        $this->player->getInventory()->setItem($this->slot, $this->ammo->setCount($this->ammo->count - 1));
                                }
                        }

                        $ammo = $this->findAmmo();
                        if($ammo !== false){
                                $this->ammo = $ammo;
                        }else{
                                RandomUtils::playSound("random.click", $this->player, 500, 0.5);
                                $this->player->sendTip("§cOut of ammo.");

                                $this->cancel();
                                return;
                        }
                }

                if(!$this->player->getInventory()->getItemInHand()->equals($this->gun)){
                        $this->cancel();
                        return;
                }

                $this->core->fire($this->player, $this->gun, $this->ammo);
                $gunType = $this->gun->getCustomBlockData()->getString("gunType");;

                $this->player->sendTip("§c{$this->amount} rounds left");

                RandomUtils::playSound("firework.blast", $this->player, 500, GunData::SHOT_PITCH[$gunType]);
                --$this->amount;
        }
}