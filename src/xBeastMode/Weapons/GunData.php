<?php
namespace xBeastMode\Weapons;
interface GunData{
        const GUN_LIST = [
            "mg42",
            "mp40",
            "minigun",
            "thompson",
            "m1911",
            "panzerfaust"
        ];

        const FIRE_RATES = [
            "mg42" => 1,
            "mp40" => 3,
            "minigun" => 0,
            "thompson" => 2,
        ];

        const SHOT_PITCH = [
            "mg42" => 0.4,
            "mp40" => 0.7,
            "minigun" => 0.6,
            "thompson" => 0.5,
            "m1911" => 0.5,
            "panzerfaust" => 0.1
        ];

        const DAMAGES = [
            "mg42" => 1,
            "mp40" => 1,
            "minigun" => 4,
            "thompson" => 1,
            "m1911" => 1,
            "panzerfaust" => 1,
        ];

        const FULL_AUTO = [
            "mg42",
            "mp40",
            "minigun",
            "thompson"
        ];

        const EXPLODE = [
            "panzerfaust" => 5
        ];
}