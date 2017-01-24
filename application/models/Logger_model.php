<?php
namespace Scissorhands\CIMonolog\Models;
defined('BASEPATH') OR exit('No direct script access allowed');
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use MySQLHandler\MySQLHandler;

class Logger_model extends \CI_Model {
	private $_logger = null;
	private $mySQLHandler;
	private $default_fields = ['url', 'ip', 'http_method', 'referrer', 'platform', 'mobile', 'post_fields'];
	private $pdo;

	public function __construct()
	{
		parent::__construct();
		$this->load->library('user_agent');
		$this->pdo = new PDO(
			'mysql:host='.$this->db->hostname.
			';dbname='.$this->db->database, 
			$this->db->username, 
			$this->db->password
		);
	}

	/**
	 * Model Initializator
	 *
	 * @return void
	 * @param String $channel: name of the logs channel
	 * @param (Optional) Array $config: fields ['log_table'(String), 'extra_fields'(String array)]
	 **/
	public function init( $channel = 'general', $config = [] )
	{
		$log_table = isset($config['log_table']) ? $config['log_table'] : 'logs';
		$extra_fields = isset($config['extra_fields']) ? array_merge( $this->default_fields, $config['extra_fields']) : $this->default_fields;
		$this->mySQLHandler = new MySQLHandler($this->pdo, $log_table, $extra_fields, Logger::DEBUG);

		$this->_logger = new Logger($channel);
		$this->_logger->pushHandler(new StreamHandler('logs/app_logs.log', Logger::INFO) );
		$this->_logger->pushHandler($this->mySQLHandler);
	}

	public function log( $message = '', $payload = [], $level = 'info')
	{
		if(!$this->_logger){ throw new Exception("Logger must be initialized", 1); }
		$tracking_info = $this->get_tracking_info();
		$payload = array_merge( $payload, $tracking_info );
		$this->_logger->{$level}( $message, $payload );
	}

	private function get_tracking_info()
	{
		return [
			'url' => $this->uri->uri_string(),
			'ip' => $this->input->ip_address(),
			'http_method' => strtoupper($this->input->method()),
			'referrer' => $this->agent->referrer(),
			'platform' => $this->agent->platform(),
			'mobile' => $this->agent->mobile(),
			'post_fields' => $this->input->post()? json_encode($this->input->post()) : null
		];
	}

}

/* End of file Logger_model.php */
/* Location: ./application/models/Logger_model.php */