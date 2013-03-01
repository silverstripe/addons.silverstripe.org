<?php
/**
 * A service for queueing up background jobs using resque.
 */
class ResqueService {

	public function __construct($backend = null) {
		Resque::setBackend($backend);
	}

	public function queue($queue, $class, $args = null) {
		Resque::enqueue($queue, $class, $args);
	}

}
