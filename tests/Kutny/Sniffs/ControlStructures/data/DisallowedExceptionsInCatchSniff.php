<?php

try {

} catch (\Kutny\SomeSpecificException $e) {

}

try {

} catch (\Exception $e) {

}

try {

} catch (\InvalidArgumentException $e) {

}

try {

} catch (\Kutny\Exception\InvalidStateException $e) {

}

class TestFoo
{

	public function test()
	{
		try {

		} catch (\Kutny\Exception\InvalidStateException $e) {

		}
	}

}

function test2()
{
	try {

	} catch (\Exception $ex) {
		throw $ex;
	}
}

function test3()
{
	try {

	} catch (\Kutny\Exception\InvalidStateException $e) {
		if (5 === 3) {
			return 5;
		}
		throw $e;
	}
}

/**
 * @SuppressWarnings(CS.DisallowedExceptionsInCatch)
 */
function test()
{
	try {

	} catch (\Exception $e) {

	}
}

/**
 * @SuppressWarnings(CS)
 */
class Test
{

	public function test()
	{
		try {

		} catch (\InvalidArgumentException $e) {

		}
	}

}
