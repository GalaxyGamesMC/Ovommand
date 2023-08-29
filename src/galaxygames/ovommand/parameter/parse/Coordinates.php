<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter\parse;

use pocketmine\command\CommandExecutor;
use pocketmine\entity\Entity;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\world\Position;
use pocketmine\world\World;
use Vector;

class Coordinates{
    public const TYPE_DEFAULT = 0; //plain number
    public const TYPE_RELATIVE = 1; //tilde
    public const TYPE_LOCAL = 2; //caret notation

    protected int $xType;
    protected int $yType;
    protected int $zType;

    protected bool $hasCaret = false;

    protected int|float $x;
    protected int|float $y;
    protected int|float $z;

    public function __construct(int|float $x, int|float $y, int|float $z, int $xType = self::TYPE_DEFAULT, int $yType = self::TYPE_DEFAULT, int $zType = self::TYPE_DEFAULT){
        $this->setData($x, $y, $z, $xType, $yType, $zType);
    }

    public function setData(int|float $x, int|float $y, int|float $z, int $xType = self::TYPE_DEFAULT, int $yType = self::TYPE_DEFAULT, int $zType = self::TYPE_DEFAULT) : self{
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;

        $this->xType = match ($xType) {
            self::TYPE_DEFAULT => self::TYPE_DEFAULT,
            self::TYPE_RELATIVE => self::TYPE_RELATIVE,
            self::TYPE_LOCAL => self::TYPE_LOCAL,
            default => throw new \RuntimeException("Unknown coordinate's x value type set in self::class")
        };
        $this->yType = match ($yType) {
            self::TYPE_DEFAULT => self::TYPE_DEFAULT,
            self::TYPE_RELATIVE => self::TYPE_RELATIVE,
            self::TYPE_LOCAL => self::TYPE_LOCAL,
            default => throw new \RuntimeException("Unknown coordinate's y value type set in self::class")
        };
        $this->zType = match ($yType) {
            self::TYPE_DEFAULT => self::TYPE_DEFAULT,
            self::TYPE_RELATIVE => self::TYPE_RELATIVE,
            self::TYPE_LOCAL => self::TYPE_LOCAL,
            default => throw new \RuntimeException("Unknown coordinate's z value type set in self::class")
        };
        return $this;
    }

    public function hasCaret() : bool{
        return $this->x === self::TYPE_LOCAL || $this->y === self::TYPE_LOCAL || $this->z === self::TYPE_LOCAL;
    }

    public function parsePosition(CommandExecutor $executor = null, World $world = null) : Position{
        if ($this->xType !== self::TYPE_DEFAULT || $this->yType !== self::TYPE_DEFAULT || $this->zType !== self::TYPE_DEFAULT) {
            if (!$executor instanceof Entity) {
                throw new \InvalidArgumentException("Coords must be returned from the execution by an entity!");
            }
        }
        if ($this->hasCaret && ($this->xType === self::TYPE_LOCAL && $this->yType === self::TYPE_LOCAL && $this->zType === self::TYPE_LOCAL)) {
            throw new \InvalidArgumentException("Unexpected! All must be caret");
        }
        $pos = $executor?->getPosition() ?? new Position($this->x, $this->y, $this->z, $world);
        if  ($this->hasCaret()) {
            $forwardSide = $executor->getHorizontalFacing();
            $leftSide = Facing::rotateY($forwardSide, false);
            $facing = $executor->getDirectionVector();
            $facing->getSide($leftSide, $this->x);
            $facing->getSide($forwardSide, $this->z);
            $facing->getSide(Facing::UP, $this->y);
        }
        if ($executor instanceof Entity) {
            $pos->world = $executor->getWorld();
        }

        return new Position($this->x, $this->y, $this->z, $world);
    }

    private function parseRelative(Entity $entity) : Position{
        $pos = $entity->getPosition();
        if ($this->xType === self::TYPE_RELATIVE) {
            $pos->add($this->x, 0, 0);
        }
        if ($this->yType === self::TYPE_RELATIVE) {
            $pos->add(0, $this->y, 0);
        }
        if ($this->zType === self::TYPE_RELATIVE) {
            $pos->add(0, 0, $this->z);
        }
        return $pos;
    }

    private function getUpSideDirection(Entity $entity) : Vector3{
        $pitch = $entity->getLocation()->pitch + 90;
        $yaw = $entity->getLocation()->yaw;
        $y = -sin(deg2rad($pitch));
        $xz = cos(deg2rad($pitch));
        $x = -$xz * sin(deg2rad($yaw));
        $z = $xz * sin(deg2rad($yaw));

        return (new Vector3($x, $y, $z))->normalize();
    }

    private function getLeftSideDirection(Entity $entity) : Vector3{
        $pitch = $entity->getLocation()->pitch;
        $yaw = $entity->getLocation()->yaw + 90; //Left?
        $y = -sin(deg2rad($pitch));
        $xz = cos(deg2rad($pitch));
        $x = -$xz * sin(deg2rad($yaw));
        $z = $xz * sin(deg2rad($yaw));
    }

    private function addLength(Vector3 $vector, int|float $num) : Vector3{
        $len = $vector->length();
        if (!($len > 0)) {
            $len = 0;
        }
        $normal = $vector->normalize();
        $normal->multiply($len + $num);
        return $normal;
    }

    private function parseLocal(Entity $entity) : Position{
//        $forwardSide = $entity->getHorizontalFacing();
//        $leftSide = Facing::rotateY($forwardSide, false);
//        $facing = $entity->getDirectionVector();
//        $facing->getSide($leftSide, $this->x);
//        $facing->getSide(Facing::UP, $this->y); //No :c, this is wrong but I suck at geometry
//        $facing->getSide($forwardSide, $this->z);

        //shit, the adding was into its length...
//        $forward = $entity->getDirectionVector();
//        $up = $this->getEntityUpDirection($entity);
//        $pos = $entity->getPosition();
//        $forward = $this->addLength($entity->getDirectionVector(), $this->z);

        $forward = $entity->getDirectionVector();
        $up = $this->getUpSideDirection($entity);
        $left = $this->getLeftSideDirection($entity);

        $forward = $this->addLength($forward, $this->z);
        $up = $this->addLength($up, $this->y);
        $left = $this->addLength($left, $this->x);

        $pos = $entity->getPosition()
            ->addVector($forward)
            ->addVector($up)
            ->addVector($left);
        return Position::fromObject($pos, $entity->getWorld());
    }
}
