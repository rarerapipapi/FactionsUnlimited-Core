<?php
namespace FacCore\Commands;

use FacCore\Main;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class WarpCommand extends PluginCommand {
	public function __construct(Main $owner) {
		parent::__construct($owner->getLanguage()->get("warp.name"), $owner);
		$this->setUsage($owner->getLanguage()->get("warp.usage"));
		$this->setPermission("core.command.warp.tp");
		$this->setDescription($owner->getLanguage()->get("warp.desc"));
	}
	public function execute(CommandSender $sender, string $commandLabel, array $args) {
		if(!$this->testPermission($sender) or !$sender instanceof Player) {
			return true;
		}
		if(!$this->getPlugin()->getWarpsConfig()->exists($args[0])) {
			$sender->sendMessage(TextFormat::YELLOW.$this->getPlugin()->getLanguage()->translateString("warp.nonexistant", $args[0]));
			return true;
		}
		$warp = $args[0];
		if(!$sender->teleport(
			new Position(
				$this->getPlugin()->getConfig()->getNested("{$warp}.x", 0),
				$this->getPlugin()->getConfig()->getNested("{$warp}.y", 64),
				$this->getPlugin()->getConfig()->getNested("{$warp}.z", 0),
				$this->getPlugin()->getServer()->getLevelByName(
					$this->getPlugin()->getConfig()->getNested("{$warp}.world", "world")
				)
			)
		)) {
			$sender->sendMessage(TextFormat::YELLOW.$this->getPlugin()->getLanguage()->get("error"));
		}else{
			$sender->sendMessage(TextFormat::GREEN.$this->getPlugin()->getLanguage()->translateString("warp.success", $warp));
		}
		return true;
	}
	/**
	 * @return Main
	 */
	public function getPlugin() : Plugin {
		return parent::getPlugin();
	}
	public function generateCustomCommandData(Player $player) : array {
		$arr = parent::generateCustomCommandData($player);
		$warps = $this->getPlugin()->getWarpsConfig()->getAll(true);
		$arr["overloads"]["default"]["input"]["parameters"] = [
			[
				"name" => "warp",
				"type" => "stringenum",
				"optional" => false,
				"enum_values" => $warps
			]
		];
		return $arr;
	}
}