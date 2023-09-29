<?php
declare(strict_types=1);

namespace galaxygames\ovommand\fetus;

use galaxygames\ovommand\exception\ExceptionMessage;
use galaxygames\ovommand\exception\ParameterOrderException;
use galaxygames\ovommand\parameter\BaseParameter;
use galaxygames\ovommand\parameter\result\BaseResult;
use galaxygames\ovommand\parameter\result\BrokenSyntaxResult;

trait ParametableTrait{
	/** @var BaseParameter[][] */
	protected array $overloads = [];

	abstract protected function prepare() : void;

//	public function validateParameter() : bool{
//		if (array_is_list($this->overloads)) {
//			foreach ($this->overloads as $overload) {
//				foreach ($overload as $parameter) {
//
//				}
//			}
//			return true;
//		}
//		return false;
//	}

	public function registerParameters(int $overloadId, BaseParameter ...$parameters) : void{
		if ($overloadId < 0) {
			throw new ParameterOrderException(ExceptionMessage::MSG_PARAMETER_NEGATIVE_ORDER->getErrorMessage(["position" => (string) $overloadId]), ParameterOrderException::PARAMETER_NEGATIVE_ORDER_ERROR);
		}
		if ($overloadId > 0 && !isset($this->overloads[$overloadId - 1])) {
			throw new ParameterOrderException(ExceptionMessage::MSG_PARAMETER_DETACHED_ORDER->getErrorMessage(["position" => (string) $overloadId]), ParameterOrderException::PARAMETER_DETACHED_ORDER_ERROR);
		}
		foreach ($parameters as $parameter) {
			//TODO: TextParameter does not allow
			//TODO: WRONG MSG!!!!!!!!!!!!!!!!!!!!!
			if (!$parameter->isOptional()) {
				foreach ($this->overloads[$overloadId] ?? [] as $para) {
					if ($para->isOptional()) {
						throw new ParameterOrderException(ExceptionMessage::MSG_PARAMETER_DESTRUCTED_ORDER->getRawErrorMessage(), ParameterOrderException::PARAMETER_DESTRUCTED_ORDER_ERROR);
					}
				}
			}

			$this->overloads[$overloadId][] = $parameter;
			echo $this->getName() . " with \$overloadId: $overloadId\n{" . $parameter->getName() . ": " . $parameter->getValueName() . "}\n\n";
//			usort($this->overloads, $callback)

			//		usort($this->overloads[$position], static function(BaseParameter $a, BaseParameter $b) : int{
			//			if ($a->getSpanLength() === PHP_INT_MAX) { // if it takes unlimited parameters, pull it down
			//				return 1;
			//			}
			//			return -1;
			//		}); // Sort with their spans
//			usort($this->overloads[$overloadId], static function(BaseParameter $a, BaseParameter $b) : int{
//				return strnatcmp($a->getName() . ": " . $a->getValueName(), $b->getName() . ": " . $b->getValueName());
//			}); // Sort with their alphabet. EDIT: FLAW LOL [not work]
		}
	}

	public function registerParameter(int $overloadId, BaseParameter $parameter) : void{
		$this->registerParameters($overloadId, $parameter);
	}

	public function parseParameters(array $rawParams) : array{
		$paramCount = count($rawParams);
		if ($paramCount !== 0 && !$this->hasOverloads()) {
			return [];
		}
		/** @var BaseResult[][] $resultContainers */
		$resultContainers = [];
		$finalId = 0;
		foreach ($this->overloads as $overloadId => $parameters) {
			$offset = 0;
			$results = [];
			foreach ($parameters as $parameter) {
				$params = array_slice($rawParams, $offset, $span = $parameter->getSpanLength());
				if ($offset === $paramCount - $span + 1 && $parameter->isOptional()) {
					break;
				}
				$offset += $span;
				//TODO: Because the parser might choose the wrong overloads, so adding something to stop it from doing that?
				$result = $parameter->parse($params);
				$results[$parameter->getName()] = $result;
				if ($result instanceof BrokenSyntaxResult && $overloadId + 1 !== count($this->overloads)) {
					continue 2;
				}
			}
			if ($paramCount > ($pCount = count($parameters))) {
				$results["_error"] = BrokenSyntaxResult::create(array_slice($rawParams, $pCount, $pCount + 1)[0]);
			}
			$resultContainers[$finalId = $overloadId] = $results;
		}
		return $resultContainers[$finalId];
	}

	/**
	 11:06:06.708] [Server thread/EMERGENCY]: Crash occurred while handling a packet from session: Arie1906
[11:06:06.709] [Server thread/CRITICAL]: ErrorException: "Undefined array key 0" (EXCEPTION) in "D:/pmmp/Ovommand/src/galaxygames/ovommand/parameter/TargetParameter" at line 33
--- Stack trace ---
  #0 D:/pmmp/Ovommand/src/galaxygames/ovommand/parameter/TargetParameter(33): pocketmine\errorhandler\ErrorToExceptionHandler::handle(int 2, string[21] Undefined array key 0, string[71] D:\pmmp\Ovommand\src\galaxygames\ovommand\parameter\TargetParameter.php, int 33)
  #1 D:/pmmp/Ovommand/src/galaxygames/ovommand/fetus/ParametableTrait(86): galaxygames\ovommand\parameter\TargetParameter->parse(array[0])
  #2 D:/pmmp/Ovommand/src/galaxygames/ovommand/fetus/Ovommand(80): galaxygames\ovommand\BaseSubCommand->parseParameters(array[1])
  #3 D:/pmmp/Ovommand/src/galaxygames/ovommand/fetus/Ovommand(76): galaxygames\ovommand\fetus\Ovommand->execute(object pocketmine\player\Player#167006, string[4] test, array[1], string[25] hello testhellotest false)
  #4 pmsrc/src/command/SimpleCommandMap(212): galaxygames\ovommand\fetus\Ovommand->execute(object pocketmine\player\Player#167006, string[5] hello, array[1])
  #5 pmsrc/src/Server(1416): pocketmine\command\SimpleCommandMap->dispatch(object pocketmine\player\Player#167006, string[17] hello test false )
  #6 pmsrc/src/player/Player(1512): pocketmine\Server->dispatchCommand(object pocketmine\player\Player#167006, string[17] hello test false )
  #7 pmsrc/src/network/mcpe/handler/InGamePacketHandler(826): pocketmine\player\Player->chat(string[18] /hello test false )
  #8 pmsrc/vendor/pocketmine/bedrock-protocol/src/CommandRequestPacket(55): pocketmine\network\mcpe\handler\InGamePacketHandler->handleCommandRequest(object pocketmine\network\mcpe\protocol\CommandRequestPacket#83197)
  #9 pmsrc/src/network/mcpe/NetworkSession(445): pocketmine\network\mcpe\protocol\CommandRequestPacket->handle(object pocketmine\network\mcpe\handler\InGamePacketHandler#83301)
  #10 pmsrc/src/network/mcpe/NetworkSession(383): pocketmine\network\mcpe\NetworkSession->handleDataPacket(object pocketmine\network\mcpe\protocol\CommandRequestPacket#83197, string[40] M./hello test false ..J=_.{.L.Z..F.....H)
  #11 pmsrc/src/network/mcpe/raklib/RakLibInterface(219): pocketmine\network\mcpe\NetworkSession->handleEncoded(string[43] .....H...W(I-.QHK.)NU`X.e......p..Y..C3..<.)
  #12 pmsrc/vendor/pocketmine/raklib-ipc/src/RakLibToUserThreadMessageReceiver(40): pocketmine\network\mcpe\raklib\RakLibInterface->onPacketReceive(int 0, string[52] .x..j.Fn..`....K....!WU.....v......o!...l..q..7GB...)
  #13 pmsrc/src/network/mcpe/raklib/RakLibInterface(111): raklib\server\ipc\RakLibToUserThreadMessageReceiver->handle(object pocketmine\network\mcpe\raklib\RakLibInterface#167696)
  #14 pmsrc/vendor/pocketmine/snooze/src/SleeperHandler(120): pocketmine\network\mcpe\raklib\RakLibInterface->pocketmine\network\mcpe\raklib\{closure}()
  #15 pmsrc/src/TimeTrackingSleeperHandler(58): pocketmine\snooze\SleeperHandler->processNotifications()
  #16 pmsrc/vendor/pocketmine/snooze/src/SleeperHandler(79): pocketmine\TimeTrackingSleeperHandler->processNotifications()
  #17 pmsrc/src/Server(1681): pocketmine\snooze\SleeperHandler->sleepUntil(float 1695960366.7072)
  #18 pmsrc/src/Server(1064): pocketmine\Server->tickProcessor()
  #19 pmsrc/src/PocketMine(334): pocketmine\Server->__construct(object pocketmine\thread\ThreadSafeClassLoader#6, object pocketmine\utils\MainLogger#3, string[39] C:\Users\nttis\Downloads\PocketMine-MP\, string[47] C:\Users\nttis\Downloads\PocketMine-MP\plugins\)
  #20 pmsrc/src/PocketMine(357): pocketmine\server()
  #21 pmsrc(11): require(string[83] phar://C:/Users/nttis/Downloads/PocketMine-MP/PocketMine-MP.phar/src/PocketMine.)
--- End of exception information ---
[11:06:06.711] [Server thread/EMERGENCY]: An unrecoverable error has occurred and the server has crashed. Creating a crash dump
[11:06:06.761] [Server thread/EMERGENCY]: Please upload the "C:/Users/nttis/Downloads/PocketMine-MP/crashdumps/Fri_Sep_29-11.06.06-WIB_2023.log" file to the Crash Archive and submit the link to the Bug Reporting page. Give as much info as you can.
[11:06:06.761] [Server thread/EMERGENCY]: Forcing server shutdown

	 */

	/**
	 * @return BaseParameter[][]
	 */
	public function getOverloads() : array{
		return $this->overloads;
	}

	public function hasOverloads() : bool{
		return !empty($this->overloads);
	}
}
