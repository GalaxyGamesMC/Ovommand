<?php
declare(strict_types=1);

namespace galaxygames\ovommand\constraint;

use pocketmine\command\CommandSender;
use pocketmine\utils\Utils;
use shared\galaxygames\ovommand\fetus\BaseConstraint;
use shared\galaxygames\ovommand\fetus\IOvommand;

/**
 * @phpstan-type TConstraintClosure \Closure(CommandSender $sender, string $label, string[] $args) : bool
 * @phpstan-type TFeedbackClosure \Closure(CommandSender $sendder, string $labels, string[] $args) : void
 */
class ClosureConstraint extends BaseConstraint{
	/** @var ?TConstraintClosure */
	private ?\Closure $constraintClosure;
	/** @var ?TFeedbackClosure */
	private ?\Closure $successClosure;
	/** @var ?TFeedbackClosure */
	private ?\Closure $failureClosure;

	/**
	 * @phpstan-param ?TConstraintClosure $constraintClosure
	 * @phpstan-param ?TFeedbackClosure $failureClosure
	 * @phpstan-param ?TFeedbackClosure $successClosure
	 */
	public function __construct(IOvommand $ovommand, ?\Closure $constraintClosure = null, ?\Closure $failureClosure = null, ?\Closure $successClosure = null){
		parent::__construct($ovommand);
		if ($constraintClosure !== null) {
			Utils::validateCallableSignature(fn(CommandSender $sender, string $label, array $args) : bool => true, $constraintClosure);
		}
		if ($failureClosure !== null) {
			Utils::validateCallableSignature(fn(CommandSender $sender, string $label, array $args) => null, $failureClosure);
		}
		if ($successClosure !== null) {
			Utils::validateCallableSignature(fn(CommandSender $sender, string $label, array $args) => null, $successClosure);
		}
		$this->constraintClosure = $constraintClosure;
		$this->failureClosure = $failureClosure;
		$this->successClosure = $successClosure;
	}

	public function constraint(CommandSender $sender, string $label, array $args) : bool{
		if ($this->constraintClosure === null) {
			return false;
		}
		return ($this->constraintClosure)($sender, $label, $args);
	}

	public function onFailure(CommandSender $sender, string $label, array $args) : void{
		if ($this->failureClosure !== null) {
			($this->failureClosure)($sender, $label, $args);
		}
	}

	public function onSuccess(CommandSender $sender, string $label, array $args) : void{
		if ($this->successClosure !== null) {
			($this->successClosure)($sender, $label, $args);
		}
	}
}
