<?php

namespace App\Helpers\Calculation;

class BankInterestCalculator {

	const BANK_INTEREST_PER_YEAR = 2; // percent
	const INTEREST_PERIOD = 25; // year

	protected static $numbers = [
		'investment' => 0,
		'saving' => 0,
		'result' => 0,
		'increment' => 0,
		'comparable' => 0,
	];

	public static function getComparableInterest($investment, $saving, $interest = self::BANK_INTEREST_PER_YEAR, $period = self::INTEREST_PERIOD){
		self::$numbers['investment'] = $investment;
		self::$numbers['saving'] = $saving;
		self::$numbers['result'] = self::tw($saving, $interest, $period);
		if (self::$numbers['investment'] <= 0) {
			self::$numbers['increment'] = 0;
			self::$numbers['comparable'] = 0;
		}
		else {
			self::$numbers['increment'] = round( ( self::$numbers['result'] / self::$numbers['investment'] ) * 100 );
			self::$numbers['comparable'] = round(max(0, (pow(self::$numbers['result'] / self::$numbers['investment'], 1/$period) - 1)) * 100, 1);
		}

		return self::$numbers['comparable'];
	}


	/**
	 * PHP implementation of Excel's TW function.
	 * http://www.kb-financieel.nl/vijf-basisfuncties-bet-hw-nper-rente-tw.14645.lynkx
	 *
	 * @param float $amountPerTerm
	 * @param int $interest
	 * @param int $period
	 *
	 * @return int
	 */
	public static function tw($amountPerTerm, $interest = self::BANK_INTEREST_PER_YEAR, $period = self::INTEREST_PERIOD){
		$amount = $amountPerTerm;
		for($year = 1; $year < $period; $year++){
			$amount *= 1 + ($interest / 100);
			$amount += $amountPerTerm;
		}
		return (int) $amount;
	}
}