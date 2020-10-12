<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TaskTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function a_task_belongs_to_a_project()
    {
        $this->createTask()
            ->assertInstanceOf('App\Project', $this->task->project);
    }
    
    /**
     * @test
     */
    public function when_task_created_it_must_be_added_to_a_project_tasks_order()
    {
        $this->createTask();

        $task = create('App\Task', ['project_id' => $this->project->id]);
        
        $this->assertEquals([$this->task->id, $task->id], $this->project->fresh()->tasks_order);
    }

    /**
     * @test
     */
    public function when_task_deleted_it_must_be_removed_to_a_project_tasks_order()
    {
        $this->createTask();

        $task = create('App\Task', ['project_id' => $this->project->id]);
        
        $this->task->delete();
        
        $this->assertEquals([$task->id], $this->project->fresh()->tasks_order);
    }

    protected function createTask()
    {
        $this->signIn();

        $this->project = create('App\Project', ['user_id' => auth()->id()]);
        $this->task = create('App\Task', ['project_id' => $this->project->id]);

        return $this;
    }
}
