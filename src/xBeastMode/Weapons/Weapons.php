<?php
namespace xBeastMode\Weapons;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
class Weapons extends PluginBase{
        /** @var FireGunTask[] */
        protected $tasks = [];

        public function onEnable(){
                Entity::registerEntity(BulletEntity::class);

                $this->getServer()->getCommandMap()->register("weapons", new WeaponCommand($this));
                $this->getServer()->getPluginManager()->registerEvents(new WeaponsListener($this), $this);

                $this->saveDefaultConfig();

                GunData::parseGunData($this->getConfig()->getAll());
        }

        /**
         * @param Player $player
         */
        public function toggleGun(Player $player){
                if(isset($this->tasks[spl_object_hash($player)])){
                        $this->tasks[spl_object_hash($player)]->getHandler()->cancel();

                        unset($this->tasks[spl_object_hash($player)]);
                }else{
                        $gun = $player->getInventory()->getItemInHand();
                        $gunType = $gun->getCustomBlockData()->getString("gunType");

                        $task = new FireGunTask($this, $player, $gun);
                        $this->getScheduler()->scheduleRepeatingTask($task, GunData::getFireRate($gunType));

                        $this->tasks[spl_object_hash($player)] = $task;
                }
        }

        /**
         * @param Player $player
         * @param Item   $gun
         * @param Item   $ammo
         * @param bool   $tip
         *
         * @return bool
         */
        public function fire(Player $player, Item $gun, Item $ammo = null, bool $tip = true){
                if($ammo === null){
                        $slot = 0;
                        foreach($player->getInventory()->getContents() as $i => $item){
                                if($item->hasCustomBlockData() && $item->getCustomBlockData()->hasTag("ammoAmount")){
                                        $slot = $i;
                                        $ammo = $item;
                                        break;
                                }
                        }
                        if($ammo === null) return false;

                        $amount = $ammo->getCustomBlockData()->getInt("ammoAmount");

                        --$amount;
                        if($amount <= 0){
                                if($ammo->count > 1){
                                        $player->getInventory()->setItem($slot, $ammo->setCount($ammo->count - 1));
                                }else{
                                        $ammo->setCustomBlockData(new CompoundTag("", [new IntTag("ammoAmount", $amount)]));
                                        $player->getInventory()->setItem($slot, $ammo->setCount($ammo->count - 1));
                                }
                        }else{
                                $ammo->setCustomBlockData(new CompoundTag("", [new IntTag("ammoAmount", $amount)]));
                                $player->getInventory()->setItem($slot, $ammo);
                        }

                        if($tip && $amount >= 1) $player->sendTip("§c{$amount} rounds left");
                }

                $gunType = $gun->getCustomBlockData()->getString("gunType");;

                $itemTag = $ammo->setCount(1)->nbtSerialize();
                $itemTag->setName("Item");

                $mot = $player->getDirectionVector()->multiply(2);
                $nbt = Entity::createBaseNBT($player->add(0, 1, 0), $mot, lcg_value() * 360, 0);
                $nbt->setShort("Health", 5);
                $nbt->setShort("PickupDelay", 10);
                $nbt->setTag($itemTag);

                $entity = Entity::createEntity("BulletEntity", $player->level, $nbt);
                if($entity instanceof BulletEntity){
                        $entity->exempt = $player;
                        $entity->gunType = $gunType;
                        $entity->spawnToAll();
                }

                return true;
        }
}