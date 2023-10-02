<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter\result;

use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\world\Position;
use shared\galaxygames\ovommand\fetus\BaseResult;

final class CoordinateResult extends BaseResult{
	public const TYPE_DEFAULT = 0; //plain number
	public const TYPE_RELATIVE = 1; //tilde
	public const TYPE_LOCAL = 2; //caret notation

	protected int $xType;
	protected int $yType;
	protected int $zType;

	protected int|float $x;
	protected int|float $y;
	protected int|float $z;

	protected bool $hasCaret;
	protected bool $isBlockPos; //TODO: deal with this later

	public function __construct(int|float $x, int|float $y, int|float $z, int $xType = self::TYPE_DEFAULT, int $yType = self::TYPE_DEFAULT, int $zType = self::TYPE_DEFAULT, bool $isBlockPos = false){
		$this->x = $x;
		$this->y = $y;
		$this->z = $z;
		$this->isBlockPos = $isBlockPos;

		$this->xType = match ($xType) {
			self::TYPE_DEFAULT => self::TYPE_DEFAULT,
			self::TYPE_RELATIVE => self::TYPE_RELATIVE,
			self::TYPE_LOCAL => self::TYPE_LOCAL,
			default => throw new \InvalidArgumentException("Unknown coordinate's x value type set in self::class")
		};
		$this->yType = match ($yType) {
			self::TYPE_DEFAULT => self::TYPE_DEFAULT,
			self::TYPE_RELATIVE => self::TYPE_RELATIVE,
			self::TYPE_LOCAL => self::TYPE_LOCAL,
			default => throw new \InvalidArgumentException("Unknown coordinate's y value type set in self::class")
		};
		$this->zType = match ($zType) {
			self::TYPE_DEFAULT => self::TYPE_DEFAULT,
			self::TYPE_RELATIVE => self::TYPE_RELATIVE,
			self::TYPE_LOCAL => self::TYPE_LOCAL,
			default => throw new \InvalidArgumentException("Unknown coordinate's z value type set in self::class")
		};
		$this->hasCaret = $this->x === self::TYPE_LOCAL || $this->y === self::TYPE_LOCAL || $this->z === self::TYPE_LOCAL;
		if (!($this->xType === self::TYPE_LOCAL && $this->yType === self::TYPE_LOCAL && $this->zType === self::TYPE_LOCAL) && $this->hasCaret) {
			throw new \InvalidArgumentException("Once caret, all caret!"); //Todo: better msg
		}
	}

	public static function fromData(int|float $x, int|float $y, int|float $z, int $xType = self::TYPE_DEFAULT, int $yType = self::TYPE_DEFAULT, int $zType = self::TYPE_DEFAULT) : self{
		return new CoordinateResult($x, $y, $z, $xType, $yType, $zType);
	}

	public static function here() : self{
		return new CoordinateResult(0, 0, 0, self::TYPE_RELATIVE, self::TYPE_RELATIVE, self::TYPE_RELATIVE);
	}

	public function __toString(){
		return "Coordinates(x=" . $this->x . ",y=" . $this->y . ",z=" . $this->z . ",xType=" . $this->xType . ",yType=" . $this->y . ",zType=" . $this->zType . ")";
	}

	public function asPosition(Entity $entity = null) : Position{
		if ($this->xType !== self::TYPE_DEFAULT || $this->yType !== self::TYPE_DEFAULT || $this->zType !== self::TYPE_DEFAULT) {
			if ($entity === null) {
				throw new \InvalidArgumentException("Coords must be returned from the execution by an entity!");
			}
			if ($this->hasCaret) {
				return $this->parseLocal($entity);
			}
			return $this->parseRelative($entity);
		}
		return new Position($this->x, $this->y, $this->z, $entity?->getWorld());
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

	public function getUpSideDirectionVector(Entity $entity) : Vector3{
		$pitch = $entity->getLocation()->pitch + 90;
		$yaw = $entity->getLocation()->yaw;
		$y = -sin(deg2rad($pitch));
		$xz = cos(deg2rad($pitch));
		$x = -$xz * sin(deg2rad($yaw));
		$z = $xz * sin(deg2rad($yaw));

		return (new Vector3($x, $y, $z))->normalize();
	}

	public function getLeftSideDirectionVector(Entity $entity) : Vector3{
		$pitch = $entity->getLocation()->pitch;
		$yaw = $entity->getLocation()->yaw + 90; //Left?
		$y = -sin(deg2rad($pitch));
		$xz = cos(deg2rad($pitch));
		$x = -$xz * sin(deg2rad($yaw));
		$z = $xz * sin(deg2rad($yaw));

		return (new Vector3($x, $y, $z))->normalize();
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
		$forward = $entity->getDirectionVector();
		$up = $this->getUpSideDirectionVector($entity);
		$left = $this->getLeftSideDirectionVector($entity);

		$forward = $this->addLength($forward, $this->z);
		$up = $this->addLength($up, $this->y);
		$left = $this->addLength($left, $this->x);

		$vec = $entity->getPosition()->addVector($forward)->addVector($up)->addVector($left)->addVector($left)->addVector($left);
		return Position::fromObject($vec, $entity->getWorld());
	}
}
