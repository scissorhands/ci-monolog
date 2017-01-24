# ci-monolog
A simple integration of Codeigniter and Monolog

## Installation
Install using from composer
´composer install scissorhands/ci-logs´ 

## Test
Edit autoload on composer.json file
```
"autoload": {
    "psr-4": {
        "ciutil\\": "vendor/scissorhands/ci-utilities/",
        "cilogs\\": "vendor/scissorhands/ci-logs/"
    }
}
```

Add new migration file extending from library migration
```
<?php
use cilogs\application\migrations\Create_logs_table as Logs_migration;
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_logs_table extends Logs_migration {

	public function __construct()
	{
		parent::__construct();
	}
}

```
Run migration

Create Logger model and extend it from library model
```
<?php
use cilogs\application\models\Logger_model as CILogger;
defined('BASEPATH') OR exit('No direct script access allowed');

class Logger_model extends CILogger {
	public function __construct()
	{
		parent::__construct();
	}
}
```

Create example controller
```
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
```

Run example
`php index.php test log2db`

## Usage
Load and init model
```
$this->load->model('logger_model', 'logger');
$this->logger->init('channel_name', [
	'log_table' => 'table_name',
	/* Extra fields in case you need them */
	//'extra_fields' => ['extra_field']
]);
```

Log to database and files
```
$this->logger->log('message_to_log', [/*extra_fields_associative_array*/], 'log_level');
```
