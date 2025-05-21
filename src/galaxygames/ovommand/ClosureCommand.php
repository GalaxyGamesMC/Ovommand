<?php
declare(strict_types=1);

namespace galaxygames\ovommand;

use galaxygames\ovommand\parameter\result\BaseResult;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\utils\Utils;

/**
 * @phpstan-type TSetupClosure \Closure(Ovommand $command) : void
 * @phpstan-type TPreRunClosure \Closure(Ovommand $command, CommandSender $sender, BaseResult[] $args, list<string> $nonParsedArgs) : bool
 * @phpstan-type TRunClosure \Closure(Ovommand $command, string $label, BaseResult[] $args) : void
 */
class ClosureCommand extends Ovommand{
	/** @var ?TSetupClosure */
	private ?\Closure $setupClosure;
	/** @var ?TPreRunClosure */
	private ?\Closure $preRunClosure;
	/** @var ?TRunClosure */
	private ?\Closure $runClosure;

	/**
	 * @phpstan-param ?TSetupClosure $setupClosure
	 * @phpstan-param ?TPreRunClosure $preRunClosure
	 * @phpstan-param ?TRunClosure $runClosure
	 */
	public function __construct(
		Translatable|string $description = "", Translatable|string|null $usageMessage = null, ?string $permission = null,
		?\Closure $setupClosure = null, ?\Closure $preRunClosure = null, ?\Closure $runClosure = null
	){
		parent::__construct($description, $usageMessage, $permission);
		if ($setupClosure !== null) {
			Utils::validateCallableSignature(fn (Ovommand $command) => null, $setupClosure);
		}
		if ($preRunClosure !== null) {
			Utils::validateCallableSignature(
				fn (Ovommand $command, CommandSender $sender, array $args, array $nonParsedArgs) : bool => true,
				$preRunClosure
			);
		}
		if ($runClosure !== null) {
			Utils::validateCallableSignature(
				fn (Ovommand $command, string $label, array $args) => null,
				$runClosure
			);
		}
		$this->setupClosure = $setupClosure;
		$this->preRunClosure = $preRunClosure;
		$this->runClosure = $runClosure;
	}

	public function setup() : void{
		if ($this->setupClosure !== null) ($this->setupClosure)($this);
	}

	public function onPreRun(CommandSender $sender, array $args, array $nonParsedArgs = []) : bool{
		if ($this->preRunClosure !== null) {
			return ($this->preRunClosure)($this, $sender, $args, $nonParsedArgs);
		}
		return parent::onPreRun($sender, $args, $nonParsedArgs);
	}

	public function onRun(CommandSender $sender, string $label, array $args) : void{
		if ($this->runClosure !== null) ($this->runClosure)($this, $label, $args);
	}
}
