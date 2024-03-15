<?php
declare(strict_types = 1);

namespace Spaze\PhpInfo;

use SessionHandlerInterface;
use SessionIdInterface;

class TestSessionHandler implements SessionHandlerInterface, SessionIdInterface
{

	public function __construct(
		private string $sessionId,
		private array $data = [],
	) {
	}


	public function create_sid(): string
	{
		return $this->sessionId;
	}


	public function open(string $path, string $name): bool
	{
		return true;
	}


	public function close(): bool
	{
		return true;
	}


	public function destroy(string $id): bool
	{
		return true;
	}


	public function gc(int $max_lifetime): int|false
	{
		return 0;
	}


	public function read(string $id): string|false
	{
		return $this->data[$id] ?? '';
	}


	public function write(string $id, string $data): bool
	{
		$this->data[$id] = $data;
		return true;
	}

}
