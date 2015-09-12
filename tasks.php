<?php

require 'asana.class.php';

$project_id = $argv[1];
$access_token = getenv('ASANA_ACCESS_TOKEN');

if (empty($access_token)) {
  die("ASANA_ACCESS_TOKEN not in environment!\r\n");
}

if (empty($project_id)) {
  die("Usage: tasks.php [project_id]\r\n");
}

$a = new Asana(array('access_token' => $access_token));
$tasks = $a->get_tasks($project_id);

print_r ($tasks);
