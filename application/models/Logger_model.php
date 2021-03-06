<?php
namespace cilogs\application\models;
defined('BASEPATH') OR exit('No direct script access allowed');
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use MySQLHandler\MySQLHandler;

class Logger_model extends \CI_Model {
	private $_logger = null;
	private $mySQLHandler;
	private $default_fields = ['url', 'ip', 'http_method', 'referrer', 'platform', 'mobile', 'post_fields'];
	private $pdo;
	private $exclude_post_fields = [];

	public function __construct()
	{
		parent::__construct();
		$this->load->library('user_agent');
		$this->pdo = new \PDO(
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
	public function init( $channel = 'general', $config = [], $file_handler = false )
	{
		$log_table = isset($config['log_table']) ? $config['log_table'] : 'logs';
		$extra_fields = isset($config['extra_fields']) ? array_merge( $this->default_fields, $config['extra_fields']) : $this->default_fields;
		$this->exclude_post_fields = isset( $config['exclude_post_fields'] ) ? $config['exclude_post_fields'] : [];
		$this->mySQLHandler = new MySQLHandler($this->pdo, $log_table, $extra_fields, Logger::DEBUG);

		$this->_logger = new Logger($channel);
		if($file_handler){
			$this->_logger->pushHandler(new StreamHandler('logs/app_logs.log', Logger::INFO) );
		}
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
			'ip' => $this->input->ip_address()!=''? $this->input->ip_address() : null,
			'http_method' => strtoupper($this->input->method()),
			'referrer' => $this->agent->referrer()!=''? $this->agent->referrer(): null,
			'platform' => $this->agent->platform(),
			'mobile' => $this->agent->mobile()!=''? $this->agent->mobile(): null,
			'post_fields' => $this->get_post_fields()
		];
	}

	private function get_post_fields()
	{
		$post = $this->input->post();
		$data = [];
		foreach ($post as $key => $value) {
			if( !in_array($key, $this->exclude_post_fields) ){
				$data[$key] = $value;
			}
		}
		return $data? json_encode($data) : null;;
	}

}

/* End of file Logger_model.php */
/* Location: ./application/models/Logger_model.php */