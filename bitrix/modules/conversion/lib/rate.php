<?php

namespace Bitrix\Conversion;

abstract class Rate
{
	protected $steps = array();

	protected function __construct()
	{
	}

	abstract public function getRate();

	public function addStep($step, $rate)
	{
		$this->steps[$step] = $rate;
	}

	public function getSteps()
	{
		return $this->steps;
	}
}
