<?php

/*
 *
 *  ____                           _   _  ___
 * |  _ \ _ __ ___  ___  ___ _ __ | |_| |/ (_)_ __ ___
 * | |_) | '__/ _ \/ __|/ _ \ '_ \| __| ' /| | '_ ` _ \
 * |  __/| | |  __/\__ \  __/ | | | |_| . \| | | | | | |
 * |_|   |_|  \___||___/\___|_| |_|\__|_|\_\_|_| |_| |_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the MIT License. see <https://opensource.org/licenses/MIT>.
 *
 * @author  PresentKim (debe3721@gmail.com)
 * @link    https://github.com/PresentKim
 * @license https://opensource.org/licenses/MIT MIT License
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 */

declare(strict_types=1);

namespace kim\present\writecheck\listener;

use kim\present\writecheck\util\CheckManager;
use kim\present\writecheck\WriteCheck;
use onebone\economyapi\EconomyAPI;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;

class PlayerEventListener implements Listener{
	/** @var WriteCheck */
	private $plugin;

	/** @var int[] */
	private $touched = [];

	public function __construct(WriteCheck $plugin){
		$this->plugin = $plugin;
	}

	/**
	 * @priority LOWEST
	 *
	 * @param PlayerInteractEvent $event
	 */
	public function onPlayerInteractEvent(PlayerInteractEvent $event) : void{
		if(!$event->isCancelled() && ($event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK || $event->getAction() === PlayerInteractEvent::RIGHT_CLICK_AIR)){
			$player = $event->getPlayer();
			$inventory = $player->getInventory();
			$item = $inventory->getItemInHand();
			$amount = CheckManager::getCheckAmount($item);
			if($amount !== null){
				if($event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK){
					if(!isset($this->touched[$playerName = $player->getLowerCaseName()]) || $this->touched[$playerName] < time()){
						$this->touched[$playerName] = time() + 3;
						$player->sendMessage($this->plugin->getLanguage()->translate("check.help", [(string) $amount]));
					}
				}else{
					$economyApi = EconomyAPI::getInstance();
					$return = $economyApi->addMoney($player, $amount, false, $this->plugin->getName());
					if($return === EconomyAPI::RET_SUCCESS){
						--$item->count;
						$inventory->setItemInHand($item);
						$player->sendMessage($this->plugin->getLanguage()->translate("check.use", [(string) $amount, (string) $economyApi->myMoney($player)]));
					}else{
						$player->sendMessage($this->plugin->getLanguage()->translate("economyFailure", [(string) $return]));
					}
				}
				$event->setCancelled(true);
			}
		}
	}
}