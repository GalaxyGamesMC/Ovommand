<?php
declare(strict_types=1);

namespace galaxygames\ovommand\parameter\result;

use galaxygames\ovommand\utils\MessageParser;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\world\Position;

final class CoordinateResult extends BaseResult{
	public const TYPE_DEFAULT = 0; //plain number
	public const TYPE_RELATIVE = 1; //tilde
	public const TYPE_LOCAL = 2; //caret notation

	protected int $xType;
	protected int $yType;
	protected int $zType;

	protected float | int $x;
	protected float | int $y;
	protected float | int $z;

	protected bool $hasCaret;
	protected bool $isBlockPos; //TODO: deal with this later

	public function __construct(float|int $x, float|int $y, float|int $z, int $xType = self::TYPE_DEFAULT, int $yType = self::TYPE_DEFAULT, int $zType = self::TYPE_DEFAULT, bool $isBlockPos = false){
		$this->x = $x;
		$this->y = $y;
		$this->z = $z;
		$this->isBlockPos = $isBlockPos;

		$this->xType = match ($xType) {
			self::TYPE_DEFAULT => self::TYPE_DEFAULT,
			self::TYPE_RELATIVE => self::TYPE_RELATIVE,
			self::TYPE_LOCAL => self::TYPE_LOCAL,
			default => throw new \InvalidArgumentException(MessageParser::EXCEPTION_COORDINATE_RESULT_INVALID_TYPE->translate(["name" => "x", "type" => (string) $xType]))
		};
		$this->yType = match ($yType) {
			self::TYPE_DEFAULT => self::TYPE_DEFAULT,
			self::TYPE_RELATIVE => self::TYPE_RELATIVE,
			self::TYPE_LOCAL => self::TYPE_LOCAL,
			default => throw new \InvalidArgumentException(MessageParser::EXCEPTION_COORDINATE_RESULT_INVALID_TYPE->translate(["name" => "y", "type" => (string) $yType]))
		};
		$this->zType = match ($zType) {
			self::TYPE_DEFAULT => self::TYPE_DEFAULT,
			self::TYPE_RELATIVE => self::TYPE_RELATIVE,
			self::TYPE_LOCAL => self::TYPE_LOCAL,
			default => throw new \InvalidArgumentException(MessageParser::EXCEPTION_COORDINATE_RESULT_INVALID_TYPE->translate(["name" => "z", "type" => (string) $zType]))
		};
		$this->hasCaret = $this->xType === self::TYPE_LOCAL || $this->yType === self::TYPE_LOCAL || $this->zType === self::TYPE_LOCAL;
		if (!($this->xType === self::TYPE_LOCAL && $this->yType === self::TYPE_LOCAL && $this->zType === self::TYPE_LOCAL) && $this->hasCaret) {
			throw new \InvalidArgumentException(MessageParser::EXCEPTION_COORDINATE_RESULT_COLLIDED_TYPE->value);
		}
	}

	public static function fromData(float|int $x, float|int $y, float|int $z, int $xType = self::TYPE_DEFAULT, int $yType = self::TYPE_DEFAULT, int $zType = self::TYPE_DEFAULT, bool $isBlockPos = false) : self{
		return new CoordinateResult($x, $y, $z, $xType, $yType, $zType, $isBlockPos);
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
				throw new \InvalidArgumentException(MessageParser::EXCEPTION_COORDINATE_RESULT_ENTITY_REQUIRED->value);
			}
			if ($this->hasCaret) {
				return $this->parseLocal($entity);
			}
			return $this->parseRelative($entity);
		}
		return new Position($this->x, $this->y, $this->z, $entity?->getWorld());
	}

	public function asBlockPosition(Entity $entity = null) : Position{
		$pos = $this->asPosition($entity);
		$pos->x = $pos->getFloorX();
		$pos->y = $pos->getFloorY();
		$pos->z = $pos->getFloorZ();
		return $pos;
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

	private function addLength(Vector3 $vector, float|int $num) : Vector3{
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
