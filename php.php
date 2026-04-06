<?php

$a = 1;

class A {
	private mixed $wow;


	public function __construct(mixed $wow) {
		$this->wow = $wow;
	}
}

echo 1 . $a;
