<?php
declare(strict_types = 1);

namespace Spaze\PhpInfo;

use ReturnTypeWillChange;
use SessionHandlerInterface;
use SessionIdInterface;

class TestSessionHandler implements SessionHandlerInterface, SessionIdInterface
{

	private string $sessionId;

	/** @var array<string, string> */
	private array $data = [];


	public function __construct(string $sessionId)
	{
		$this->sessionId = $sessionId;
	}


	public function create_sid(): string
	{
		return $this->sessionId;
	}


	/**
	 * @param string $path
	 * @param string $name
	 * @return true
	 */
	#[ReturnTypeWillChange]
	public function open($path, $name)
	{
		return true;
	}


	public function close(): bool
	{
		return true;
	}


	/**
	 * @param string $id
	 * @return bool
	 */
	#[ReturnTypeWillChange]
	public function destroy($id)
	{
		return true;
	}


	/**
	 * @param int $max_lifetime
	 * @return int
	 */
	#[ReturnTypeWillChange]
	public function gc($max_lifetime)
	{
		return 0;
	}


	/**
	 * @param string $id
	 * @return string
	 */
	#[ReturnTypeWillChange]
	public function read($id)
	{
		return $this->data[$id] ?? '';
	}


	/**
	 * @param string $id
	 * @param string $data
	 * @return bool
	 */
	#[ReturnTypeWillChange]
	public function write($id, $data)
	{
		$this->data[$id] = $data;
		return true;
	}

}
