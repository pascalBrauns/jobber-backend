<?php
namespace Jobber\Reactor\Storage;
use Jobber\Reactor;
use Jobber\Reactor\Storage\Table;
use Jobber\Reactor\Type;

class Job {
  private Table\Id $id;
  private Table\Type $type;
  private Table\Pid $pid;
  private Table\Status $status;
  private Table\Progress $progress;
  private Table\Lifetime $lifetime;
  private Table\Log $log;

  public function __construct() {
    $this->id = new Table\Id;
    $this->type = new Table\Type;
    $this->pid = new Table\Pid;
    $this->status = new Table\Status;
    $this->progress = new Table\Progress;
    $this->lifetime = new Table\Lifetime;
    $this->log = new Table\Log;
  }

  /**
   * @return Type\Job[]
   */
  public function all() {
    $jobs = [];
    foreach ($this->id->all() as $id) {
      $jobs[] = $this->get($id);
    }
    return $jobs;
  }

  public function set(Type\Job $view) {
    $this->id->set($view);
    $this->type->set($view);
    $this->pid->set($view);
    $this->status->set($view);
    $this->progress->set($view);
    $this->lifetime->set($view);
    $this->log->set($view);
  }

  public function get(string $id) {
    $view = new Type\Job;
    if (!$this->id->exists($id)) {
      return null;
    }
    else {
      $view->id = $id;
      $view->type = $this->type->get($id);
      $view->pid = $this->pid->get($id);
      $view->status = $this->status->get($id);
      $view->progress = $this->progress->get($id);
      $view->lifetime = $this->lifetime->get($id);
      $view->logs = $this->log->get($id);
      return $view;
    }
  }

  function remove(string $id) {
    $this->id->remove($id);
    $this->type->remove($id);
    $this->pid->remove($id);
    $this->status->remove($id);
    $this->progress->remove($id);
    $this->lifetime->remove($id);
    $this->log->remove($id);
  }

  function blueprint(string $id) {
    $bid = Reactor\Storage::$job_blueprint->get($id, 'blueprint');
    return Reactor\Storage::$blueprint->get($bid)->get($id);
  }
}