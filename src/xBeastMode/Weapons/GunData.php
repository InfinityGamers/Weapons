<?php
namespace xBeastMode\Weapons;
use pocketmine\item\Item;
class GunData{
        public const GUN_TAG = "gunType";
        public const AMMO_TAG = "ammoAmount";

        /** @var int[] */
        protected static $ammoItem = [];
        /** @var string[][]|int[][]|float[][] */
        protected static $gunData = [];

        /**
         * @param array $config
         */
        public static function parseGunData(array $config){
                static::$ammoItem = explode(":", array_shift($config));

                foreach($config as $gunData){
                        list(
                            $gunType,
                            $itemId,
                            $itemMeta,
                            $fireRate,
                            $shotPitch,
                            $damage,
                            $fullAuto,
                            $explodes,
                            $radius,
                            $affectBlocks
                            ) = explode(":", $gunData);

                        static::$gunData[$gunType] = [
                            (int) $itemId,
                            (int) $itemMeta,
                            (int) $fireRate,
                            (float) $shotPitch,
                            (int) $damage,
                            RandomUtils::toBool($fullAuto),
                            RandomUtils::toBool($explodes),
                            (int) $radius,
                            RandomUtils::toBool($affectBlocks)
                        ];
                }
        }

        /**
         * @return Item
         */
        public static function getAmmoItem(): Item{
                return Item::get((int) static::$ammoItem[0], (int) static::$ammoItem[1]);
        }

        /**
         * @return array
         */
        public static function getGunList(): array{
                return array_keys(static::$gunData);
        }

        /**
         * @param string $gunType
         *
         * @return int
         */
        public static function getItemId(string $gunType): int{
                return isset(static::$gunData[$gunType]) ? static::$gunData[$gunType][0] : -1;
        }

        /**
         * @param string $gunType
         *
         * @return int
         */
        public static function getItemMeta(string $gunType): int{
                return isset(static::$gunData[$gunType]) ? static::$gunData[$gunType][1] : -1;
        }

        /**
         * @param string $gunType
         *
         * @return Item
         */
        public static function getGunItem(string $gunType): Item{
                return Item::get(static::getItemId($gunType), static::getItemMeta($gunType));
        }

        /**
         * @param string $gunType
         *
         * @return int
         */
        public static function getFireRate(string $gunType): int{
                return isset(static::$gunData[$gunType]) ? static::$gunData[$gunType][2] : -1;
        }

        /**
         * @param string $gunType
         *
         * @return float
         */
        public static function getShotPitch(string $gunType): float {
                return isset(static::$gunData[$gunType]) ? static::$gunData[$gunType][3] : 0.0;
        }

        /**
         * @param string $gunType
         *
         * @return int
         */
        public static function getDamage(string $gunType): int{
                return isset(static::$gunData[$gunType]) ? static::$gunData[$gunType][4] : -1;
        }

        /**
         * @param string $gunType
         *
         * @return bool
         */
        public static function getFullAuto(string $gunType): bool {
                return isset(static::$gunData[$gunType]) ? static::$gunData[$gunType][5] : false;
        }

        /**
         * @param string $gunType
         *
         * @return bool
         */
        public static function getExplodes(string $gunType): bool {
                return isset(static::$gunData[$gunType]) ? static::$gunData[$gunType][6] : false;
        }

        /**
         * @param string $gunType
         *
         * @return int
         */
        public static function getRadius(string $gunType): int{
                return isset(static::$gunData[$gunType]) ? static::$gunData[$gunType][7] : -1;
        }

        /**
         * @param string $gunType
         *
         * @return bool
         */
        public static function getAffectsBlocks(string $gunType): bool {
                return isset(static::$gunData[$gunType]) ? static::$gunData[$gunType][8] : false;
        }

        /**
         * @param Item $item
         *
         * @return bool
         */
        public static function isGunType(Item $item): bool{
                return static::isAmmoItem($item) || static::isGunItem($item);
        }

        /**
         * @param Item $item
         *
         * @return bool
         */
        public static function isAmmoItem(Item $item): bool{
                if(!$item->hasCustomBlockData()) return false;

                $blockData = $item->getCustomBlockData();
                return $blockData->hasTag(static::AMMO_TAG);
        }

        /**
         * @param Item $item
         *
         * @return bool
         */
        public static function isGunItem(Item $item): bool{
                if(!$item->hasCustomBlockData()) return false;

                $blockData = $item->getCustomBlockData();
                return $blockData->hasTag(static::GUN_TAG);
        }
}