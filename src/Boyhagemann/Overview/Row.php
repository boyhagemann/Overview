<?php

namespace Boyhagemann\Overview;

class Row
{
	protected $columns = array();

	public function __construct(Array $columns)
	{
		$this->columns = $columns;
	}

	public function columns()
	{
		return $this->columns;
	}
}