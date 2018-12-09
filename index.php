<?php

require 'vendor/autoload.php';
// Get config
require 'config.php';

// Go!
$report = new App\Report($config);
echo $report->run();