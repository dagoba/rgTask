<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $guarded = [];
    
    public function scopeOwned($query)
    {
        return $query->whereUserId(auth()->id());
    }

    public function setTasksOrderAttribute($value)
    {
        $this->attributes['tasks_order'] = json_encode($value);
    }

    public function getTasksOrderAttribute($value)
    {
        return json_decode($value);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function addTaskToOrder($taskId)
    {
        $this->tasks_order = array_merge(($this->tasks_order ?: []), [$taskId]);
        $this->save();
    }
    
    public function removeTaskFromOrder($taskId)
    {
        $this->tasks_order = array_reduce(
            $this->tasks_order,
            function ($carry, $id) use ($taskId) {
                if ($id !== $taskId) {
                    $carry[] = $id;
                }

                return $carry;
            },
            []
        );

        $this->save();
    }

}
