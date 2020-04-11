<?php
namespace xBeastMode\Weapons;
use pocketmine\level\Position;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
class RandomUtils{
        /**
         *
         * @param $str
         *
         * @return mixed
         *
         */
        public static function colorMessage($str){
                $str = str_replace("&", "\xc2\xa7", $str);
                return $str;
        }

        /**
         * @param $var
         *
         * @return bool
         */
        public static function toBool($var) {
                if (!is_string($var)) return (bool) $var;
                switch (strtolower($var)) {
                        case '1':
                        case 'true':
                        case 'on':
                        case 'yes':
                        case 'y':
                                return true;
                        default:
                                return false;
                }
        }

        /**
         * @param string   $soundName
         * @param Position $position
         * @param int      $volume
         * @param float    $pitch
         */
        public static function playSound(string $soundName, Position $position, int $volume = 500, float $pitch = 1){
                $pk = new PlaySoundPacket;
                $pk->soundName = $soundName;
                $pk->x = $position->x;
                $pk->y = $position->y;
                $pk->z = $position->z;
                $pk->volume = $volume;
                $pk->pitch = $pitch;

                $position->level->broadcastGlobalPacket($pk);
        }
}