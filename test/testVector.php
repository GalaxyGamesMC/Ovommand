<?php
declare(strict_types=1);

use pocketmine\math\Vector3;

class Vector{
    public function __construct(
        public int|float $x,
        public int|float $y,
        public int|float $z,
    ){}

    public function dot(Vector $v) : int|float{
        return $this->x * $v->x + $this->y * $v->y + $this->z * $v->z;
    }

    public function lengthSquared() : float{
        return $this->x * $this->x + $this->y * $this->y + $this->z * $this->z;
    }

    public function divide(float $number) : self{
        $this->x /= $number;
        $this->y /= $number;
        $this->z /= $number;
        return $this;
    }

    public function normalize() : Vector{
        return $this;
        $len = $this->lengthSquared();
        if($len > 0){
            $this->divide(sqrt($len));
        } else {
            $this->x = 0;
            $this->y = 0;
            $this->z = 0;
        }

        return $this;
    }

    public function __toString() : string{
        return "vector($this->x, $this->y, $this->z)";
    }
}
class Rotation{
    public function __construct(
        public int|float $pitch = 0,
        public int|float $yaw = 0,
    ){}
}

function getFacingV(Rotation $ro) : Vector{
    $y = -sin(deg2rad($ro->pitch)); //so down is positive?
    $xz = cos(deg2rad($ro->pitch));
    $x = -$xz * sin(deg2rad($ro->yaw));
    $z = $xz * cos(deg2rad($ro->yaw));
    return (new Vector($x, $y, $z))->normalize();
}

function getHeadingV(Rotation $ro) : Vector{
    // minecraft: x z y
    $y = -sin(deg2rad($ro->pitch + 90)); //so down is positive?
    $xz = cos(deg2rad($ro->pitch + 90));
    $x = -$xz * sin(deg2rad($ro->yaw));
    $z = $xz * cos(deg2rad($ro->yaw));

    return (new Vector($x, $y, $z))->normalize();
}

$ro = new Rotation(30, 80);

echo ($f = getFacingV($ro)) . "\n";
echo ($v = getHeadingV($ro)) . "\n";
echo ($r = $f->dot($v)) . "\n";
echo round($r, 14) . "\n";

//vector(-0.55667039922642, -0.5, 0.66341394816894)
//vector(0.32139380484327, -0.86602540378444, -0.38302222155949)
//2.2204460492503E-16
//0