<?php

/**
 * @name Double
 * @main sipsam\Double
 * @author sipsam1
 * @version 1
 * @api 3.0.0-ALPHA10
 */
namespace sipsam;

use pocketmine\math\Vector3;
class Double extends \pocketmine\plugin\PluginBase implements \pocketmine\event\Listener{
	public $intensity = [];
	public $mod = [];
	
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$cmd1 = new \pocketmine\command\PluginCommand("더블", $this);
		$cmd1->setPermission("true");
		$cmd1->setDescription("더블 대쉬 및 점프의 강도와 모드를 설정합니다");
		$this->getServer()->getCommandMap()->register("더블", $cmd1);
	}
	public function onCommand(\pocketmine\command\CommandSender $sender, \pocketmine\command\Command $command, string $label, array $args): bool{
		switch($command->getName()){
			case "더블":
				if(!$sender instanceof \pocketmine\Player){
					$sender->sendMessage(\pocketmine\utils\TextFormat::RED."인게임에서 사용해주세요");
					return false;
				}
				if(!isset($args[0])){
					$sender->sendMessage(\pocketmine\utils\TextFormat::AQUA."사용법: /더블 (강도)");
					$sender->sendMessage(\pocketmine\utils\TextFormat::AQUA."사용법: /더블 모드 (점프/대쉬)");
					return false;
				}
				if(!isset($args[1])){
					if(!is_numeric($args[0])){
						$sender->sendMessage(\pocketmine\utils\TextFormat::RED."(강도)는 숫자만 가능합니다");
						return false;
					}
					$this->intensity[$sender->getName()] = $args[0];
					$sender->sendMessage(\pocketmine\utils\TextFormat::AQUA."(강도)가 ".$this->intensity[$sender->getName()]."로 설정되었습니다");
					return true;
				}
				if($args[0] == "모드"){
					if(!isset($args[1])){
						$sender->sendMessage(\pocketmine\utils\TextFormat::AQUA."사용법: /더블 모드 (점프/대쉬)");
						return false;
					}
					if($args[1] == "점프"){
						$this->mod[$sender->getName()] = $args[1];
						$sender->sendMessage(\pocketmine\utils\TextFormat::AQUA."모드가 ".$this->mod[$sender->getName()]."로 설정되었습니다");
						return true;
					}elseif($args[1] == "대쉬"){
						$this->mod[$sender->getName()] = $args[1];
						$sender->sendMessage(\pocketmine\utils\TextFormat::AQUA."모드가 ".$this->mod[$sender->getName()]."로 설정되었습니다");
						return true;
					}
					return false;
				}
				break;
		}
		return false;
	}
	public function onJoin(\pocketmine\event\player\PlayerJoinEvent $ev){
		$this->mod[$ev->getPlayer()->getName()] = "대쉬";
		$ev->getPlayer()->setAllowFlight(true);
	}
	public function onMove(\pocketmine\event\player\PlayerMoveEvent $ev){
		if($ev->getPlayer()->isFlying()) $ev->getPlayer()->setFlying(false);
	}
	public function onDamage(\pocketmine\event\entity\EntityDamageEvent $ev){
		$entity = $ev->getEntity();
		if(!$entity instanceof \pocketmine\Player) return false;
		if($ev->getCause() == \pocketmine\event\entity\EntityDamageEvent::CAUSE_FALL) $ev->setCancelled(true); return true;
	}
	public function onFlight(\pocketmine\event\player\PlayerToggleFlightEvent $ev){
		$p = $ev->getPlayer();
		if($p->getGamemode() !== 0) return false;
		if(!$p->isOnGround()){
			if($this->mod[$p->getName()] == "대쉬"){
				$p->setFlying(false);
				$p->setMotion($p->getDirectionVector()->multiply($this->intensity[$p->getName()] ?? 1));
				return;
			}elseif($this->mod[$p->getName()] == "점프"){
				$p->setFlying(false);
				$p->setMotion(new Vector3(0, $this->intensity[$p->getName()], 0));
				return;
			}
		}
	}
}