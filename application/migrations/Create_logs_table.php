<?php
namespace cilogs\application\migrations;
defined('BASEPATH') OR exit('No direct script access allowed');

class Create_logs_table extends \CI_Migration {

	public function __construct()
	{
		$this->load->dbforge();
		$this->load->database();
	}

	public function up() {
		$this->dbforge->add_field([
			"time" => [
				"type" => "INT",
				"constraint" => 11,
				"unsigned" 	=> true
			],
			'channel' => [
				"type" => "VARCHAR",
				"constraint" => 64
			],
			"level" => [
				"type" => "INT",
				"constraint" => 11,
				"unsigned" 	=> true
			],
			"message" => [
				"type" => "VARCHAR",
				"constraint" => 255
			],
			'url' => [
				"type" => "VARCHAR",
				"constraint" => 255
			],
			'ip' => [
				"type" => "VARCHAR",
				"constraint" => 32,
				'null' => true,
				'default' => null
			],
			'http_method' => [
				"type" => "VARCHAR",
				"constraint" => 16,
				'null' => true,
				'default' => null
			],
			'referrer' => [
				"type" => "VARCHAR",
				"constraint" => 255,
				'null' => true,
				'default' => null
			],
			'platform' => [
				"type" => "VARCHAR",
				"constraint" => 64,
				'null' => true,
				'default' => null
			],
			'mobile' => [
				"type" => "VARCHAR",
				"constraint" => 64,
				'null' => true,
				'default' => null
			],
			'post_fields' => [
				"type" => "VARCHAR",
				"constraint" => 255,
				'null' => true,
				'default' => null
			],
		])
		->add_key(['time','channel','level','message'], true)
		->add_key('http_method')
		->add_key('platform')
		->add_key('mobile')
		->create_table( "app_logs", true );
	}

	public function down() {
		$this->dbforge->drop_table('app_logs', true);
	}

}