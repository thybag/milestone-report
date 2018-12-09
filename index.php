<?php

require 'vendor/autoload.php';
// Get config
require 'config.php';

// Go!
$report = new App\Report(isset($config) ? $config : null);
echo $report->run();