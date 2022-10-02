<?php
namespace common\daemons;

class WebsocketClient {

	private $_Socket = null;

	/**
	 * Creates the instance of the websocket client and
	 * connects it to the websocket server
	 * if you want to disconnect to the server don't forget to unset your
	 * instance of WebsocketClient in order to disconnect it from server
	 *
	 * @param string $host
	 * @param int $port
	 */
	public function __construct($host, $port) {
		$this->_connect($host, $port);
	}

	public function __destruct() {
		$this->_disconnect();
	}

	/**
	 * Sends data to the socket server that we are connected and returns the socket response
	 *
	 * @param string $data
	 * @return string
	 */
	public function sendData($data) {
		// send actual data:
		return $this->_Socket->write($data);
	}

	private function _connect($host, $port) {
		$this->_Socket = new \Paragi\PhpWebsocket\Client($host, $port);
		return true;
	}

	private function _disconnect() {
		unset($this->_Socket);
	}
}