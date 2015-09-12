<?php

require ('vendor/autoload.php');

use Goutte\Client;

class Asana {
  public function __construct($options) {
    $this->api_key = trim($options['access_token']);
    $this->client = new Client();
  }

  public function get_tasks($project_id) {
    $this->client->request('GET', 
      "https://app.asana.com/api/1.0/projects/{$project_id}/tasks?completed_since=now&opt_fields=completed,name,notes,stories,created_at,completed_at,modified_at", 
      array(), 
      array(),
      // HTTP_ prefix is required for a custom header to go through?
      array('HTTP_Authorization' => 'Bearer ' . $this->api_key));
    $json = $this->client->getResponse()->getContent();
    $items = json_decode($json);
    $tasks = array();
    foreach ($items->data as $item) {
      if (!empty($item->stories))
        $item->stories = $this->get_stories($item->id);
      $item->subtasks = $this->get_subtasks($item->id);
      $tasks[$item->id] = $item;
    }
    return $tasks;
  }

  public function get_subtasks($task_id) {
    $this->client->request('GET', 
      "https://app.asana.com/api/1.0/tasks/{$task_id}/subtasks?completed_since=now&opt_fields=name,completed,notes,stories,text", 
      array(),
      array(),
      // HTTP_ prefix is required for a custom header to go through?
      array('HTTP_Authorization' => 'Bearer ' . $this->api_key));
    $json = $this->client->getResponse()->getContent();
    $items = json_decode($json);
    $task = array();
    foreach ($items->data as $item) {
      if (!empty($item->stories))
        $item->stories = $this->get_stories($item->id);
      $task[$item->id] = $item;
    }
    return $task;
  }

  public function get_stories($task_id) {
    $this->client->request('GET', 
      "https://app.asana.com/api/1.0/tasks/{$task_id}/stories?opt_fields=name,completed,notes,text", 
      array(),
      array(),
      // HTTP_ prefix is required for a custom header to go through?
      array('HTTP_Authorization' => 'Bearer ' . $this->api_key));
    $json = $this->client->getResponse()->getContent();
    $items = json_decode($json);
    $story = array();
    foreach ($items->data as $item) {
      $story[$item->id] = $item;
    }
    return $story;
  }
}
