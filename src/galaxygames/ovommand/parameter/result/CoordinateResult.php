<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter\result;

use galaxygames\ovommand\utils\Messages;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\world\Position;

class CoordinateResult extends BaseResult{
	public const TYPE_DEFAULT = 0; //number
	public const TYPE_RELATIVE = 1; //tilde notation
	public const TYPE_LOCAL = 2; //caret notation

	public function __construct(protected float|int $x = 0, protected float|int $y = 0, protected float|int $z = 0, protected int $xType = self::TYPE_DEFAULT, protected int $yType = self::TYPE_DEFAULT, protected int $zType = self::TYPE_DEFAULT){
		if ($xType !== self::TYPE_DEFAULT && $xType !== self::TYPE_RELATIVE && $xType !== self::TYPE_LOCAL) {
			throw new \InvalidArgumentException(Messages::EXCEPTION_COORDINATE_RESULT_INVALID_TYPE->translate(["name" => "x", "type" => (string) $xType]));
		}
		if ($yType !== self::TYPE_DEFAULT && $yType !== self::TYPE_RELATIVE && $yType !== self::TYPE_LOCAL) {
			throw new \InvalidArgumentException(Messages::EXCEPTION_COORDINATE_RESULT_INVALID_TYPE->translate(["name" => "y", "type" => (string) $yType]));
		}
		if ($zType !== self::TYPE_DEFAULT && $zType !== self::TYPE_RELATIVE && $zType !== self::TYPE_LOCAL) {
			throw new \InvalidArgumentException(Messages::EXCEPTION_COORDINATE_RESULT_INVALID_TYPE->translate(["name" => "z", "type" => (string) $zType]));
		}
		$localCount = (int) (($xType === self::TYPE_LOCAL) + ($yType === self::TYPE_LOCAL) + ($zType === self::TYPE_LOCAL));
		if ($localCount === 1 || $localCount === 2) {
			throw new \InvalidArgumentException(Messages::EXCEPTION_COORDINATE_RESULT_COLLIDED_TYPE->value);
		}
	}

	public static function create(float|int $x = 0, float|int $y = 0, float|int $z = 0, int $xType = self::TYPE_DEFAULT, int $yType = self::TYPE_DEFAULT, int $zType = self::TYPE_DEFAULT) : self{
		return new CoordinateResult($x, $y, $z, $xType, $yType, $zType);
	}

	public static function here() : self{
		return new CoordinateResult(0, 0, 0, self::TYPE_RELATIVE, self::TYPE_RELATIVE, self::TYPE_RELATIVE);
	}

	public function __toString() : string{
		return "Coordinates(x=" . $this->x . ",y=" . $this->y . ",z=" . $this->z . ",xType=" . $this->xType . ",yType=" . $this->yType . ",zType=" . $this->zType . ")";
	}

	public function asPosition(?Entity $entity = null) : Position{
		if ($this->xType !== self::TYPE_DEFAULT || $this->yType !== self::TYPE_DEFAULT || $this->zType !== self::TYPE_DEFAULT) {
			if ($entity === null) {
				throw new \InvalidArgumentException(Messages::EXCEPTION_COORDINATE_RESULT_ENTITY_REQUIRED->value);
			}
			if ($this->xType === self::TYPE_LOCAL) {
				return $this->asLocalPosition($entity);
			}
			return $this->asRelativePosition($entity);
		}
		return new Position($this->x, $this->y, $this->z, $entity?->getWorld());
	}

	public function asBlockPosition(?Entity $entity = null) : Position{
		return Position::fromObject($this->asPosition($entity)->floor(), $entity?->getWorld());
	}

	private function asRelativePosition(Entity $entity) : Position{
		return Position::fromObject($entity->getPosition()->add($this->x, $this->y, $this->z), $entity->getWorld());
	}

	private function getUpSideDirectionVector(Entity $entity) : Vector3{
		$location = $entity->getLocation();
		$pitchRad = deg2rad($location->pitch + 90); // rotate 90 degrees to get the up direction
		$yawRad = deg2rad($location->yaw);
		$y = -sin($pitchRad);
		$xz = cos($pitchRad);
		$x = -$xz * sin($yawRad);
		$z = $xz * sin($yawRad);

		return (new Vector3($x, $y, $z))->normalize();
	}

	private function getLeftSideDirectionVector(Entity $entity) : Vector3{
		$location = $entity->getLocation();
		$pitchRad = deg2rad($location->pitch);
		$yawRad = deg2rad($location->yaw + 90); // rotate 90 degrees to get the left direction
		$y = -sin($pitchRad);
		$xz = cos($pitchRad);
		$x = -$xz * sin($yawRad);
		$z = $xz * sin($yawRad);

		return (new Vector3($x, $y, $z))->normalize();
	}

	private function addLength(Vector3 $vector, float|int $num) : Vector3{
		$len = max($vector->length(), 0.0);
		return $vector->normalize()->multiply($len + $num);
	}

	private function asLocalPosition(Entity $entity) : Position{
		$forward = $this->addLength($entity->getDirectionVector(), $this->z);
		$up = $this->addLength($this->getUpSideDirectionVector($entity), $this->y);
		$left = $this->addLength($this->getLeftSideDirectionVector($entity), $this->x);

		return Position::fromObject($entity->getPosition()->addVector($forward)->addVector($up)->addVector($left), $entity->getWorld());
	}
}
