<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('logger_model', 'logger');
		$this->logger->init('testing', [
			'log_table' => 'app_logs',
			'extra_fields' => ['client_id']
		]);
	}

	public function log2db()
	{
		$this->logger->log('Hello world', [
			'client_id' => 123456,
		], 'info');
	}

}

/* End of file Test.php */
/* Location: ./application/controllers/Test.php */