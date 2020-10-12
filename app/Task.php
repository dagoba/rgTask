<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Events\TaskCreated;
use App\Events\TaskDeleted;

class Task extends Model
{
    protected $guarded = [];

    protected $events = [
        'created' => TaskCreated::class,
        'deleted' => TaskDeleted::class,
    ];

    public function getDoneAttribute($value)
    {
        return boolval($value);
    }

    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    public function pushSelfIdToProjectTasksOrder()
    {
        $this->project->fresh()->addTaskToOrder($this->id);
    }

    public function removeSelfIdFromProjectTasksOrder()
    {
        $this->project->fresh()->removeTaskFromOrder($this->id);
    }
}
