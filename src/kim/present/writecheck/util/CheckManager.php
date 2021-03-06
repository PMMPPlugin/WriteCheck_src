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

namespace kim\present\writecheck\util;

use kim\present\writecheck\WriteCheck;
use pocketmine\item\Item;
use pocketmine\nbt\tag\IntTag;

class CheckManager{
	public const CHECK_AMOUNT_TAG = "whitecheck-amount";

	/**
	 * @param int $amount
	 * @param int $count
	 *
	 * @return Item the check item
	 */
	public static function makeCheck(int $amount, int $count = 1) : Item{
		$paper = Item::get(Item::PAPER, 0xff, $count);
		$paper->setNamedTagEntry(new IntTag(self::CHECK_AMOUNT_TAG, $amount));
		$language = WriteCheck::getInstance()->getLanguage();
		$paper->setCustomName($language->translate("check-name", [(string) $amount]));
		$lore = [];
		foreach($language->getArray("check.lore") as $key => $line){
			$lore[] = strtr($line, Utils::listToPairs([(string) $amount]));
		}
		$paper->setLore($lore);
		return $paper;
	}

	/**
	 * @param Item $item
	 *
	 * @return int|null
	 */
	public static function getCheckAmount(Item $item) : ?int{
		if($item->getId() === Item::PAPER && $item->getDamage() === 0xff){
			$amount = $item->getNamedTag()->getTagValue(self::CHECK_AMOUNT_TAG, IntTag::class, -1);
			if($amount !== -1){
				return $amount;
			}
		}
		return null;
	}

	/**
	 * @param Item $item
	 *
	 * @return bool
	 */
	public static function isCheck(Item $item) : bool{
		return self::getCheckAmount($item) !== null;
	}
}