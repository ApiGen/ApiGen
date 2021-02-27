<?php declare(strict_types = 1);

namespace ApiGenX\TaskExecutor;

use Evenement\EventEmitter;
use React\Stream\ReadableStreamInterface;
use React\Stream\WritableStreamInterface;


final class MessageStream extends EventEmitter
{
	private WritableStreamInterface $out;

	private string $buffer = '';


	public function __construct(ReadableStreamInterface $in, WritableStreamInterface $out)
	{
		$this->out = $out;

		$in->on('data', function (string $chunk) {
			$data = $this->buffer . $chunk;

			while (true) {
				if (strlen($data) < 4) {
					break;
				}

				$length = unpack('N', $data)[1];
				if (strlen($data) < $length + 4) {
					break;
				}

				$message = unserialize(substr($data, 4, $length));
				$data = substr($data, $length + 4);

				if ($message === false) {
					throw new \RuntimeException();
				}

				$this->emit('data', [$message]);
			}

			$this->buffer = $data;
		});
	}


	public function write($message): bool
	{
		$bytes = serialize($message);
		return $this->out->write(pack('N', strlen($bytes)) . $bytes);
	}
}
