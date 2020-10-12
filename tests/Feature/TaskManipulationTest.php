<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use App\Task;

class TaskManipulationTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function a_user_can_add_task_to_a_project()
    {
        $this->signIn()->makeTask();

        $response = $this->json('POST', "/projects/{$this->project->id}/tasks", $this->task->toArray())
            ->assertStatus(201);

        $createdTask = \App\Task::find(json_decode($response->content())->id);

        $this->assertEquals($this->project->id, $createdTask->project_id);
        $this->assertEquals($this->task->description, $createdTask->description);
    }

    /**
     * @test 
     */
    public function a_task_cannot_be_added_to_not_existing_project()
    {
        $this->withExceptionHandling()
            ->signIn()
            ->makeTask()
            ->json('POST', "/projects/99/tasks", $this->task->toArray())
            ->assertStatus(404);
    }
    
    /**
     * @test 
     */
    public function a_task_requires_a_description()
    {
        $this->withExceptionHandling()->signIn()
            ->makeTask(['description' => null])
            ->json('POST', "/projects/{$this->project->id}/tasks", $this->task->toArray())
            ->assertStatus(422)
            ->assertJson(['description' => ['The description field is required.']]);
    }
    
    /**
     * @test 
     */
    public function a_user_can_delete_task()
    {
        $this->signIn()->createTask()
            ->call('DELETE', "/projects/{$this->project->id}/tasks/{$this->task->id}")
            ->assertStatus(204);

        $this->assertEmpty(Task::find($this->project->id));
    }
    
    /**
     * @test 
     */
    public function a_user_can_update_a_task()
    {
        $this->signIn()
            ->createTask()
            ->editTask([
                'description' => 'Foo Bar Baz',
                'done' => !$this->task->done
            ])
            ->json('PUT', "/projects/{$this->project->id}/tasks/{$this->task->id}", $this->task->toArray())
            ->assertStatus(200);
        
        $updatedTask = Task::find($this->task->id);

        $this->assertEquals($this->task->description, $updatedTask->description);
        $this->assertEquals($this->task->done, $updatedTask->done);
    }
    
    /**
     * @test 
     */
    public function a_user_cannot_update_a_task_without_description()
    {
        $this->withExceptionHandling()
            ->signIn()
            ->createTask()
            ->editTask([
                'description' => '',
                'done' => !$this->task->done
            ])
            ->json('PUT', "/projects/{$this->project->id}/tasks/{$this->task->id}", $this->task->toArray())
            ->assertStatus(422)
            ->assertJson(['description' => ['The description field is required.']]);
    }

    /**
     * @test 
     */
    public function a_user_can_reorder_tasks_in_the_project()
    {
        $this->signIn()
            ->createTasks([], 3)
            ->shuffleTasks()
            ->json('PUT', "/projects/{$this->project->id}", $this->project->toArray())
            ->assertStatus(200);

        $this->assertEquals($this->project->tasks_order, $this->tasks->first()->project->refresh()->tasks_order);
    }

    protected function makeTask($attributes = [])
    {
        $this->project = create('App\Project', ['user_id' => auth()->id()]);
        $this->task = make('App\Task', array_merge(
            ['project_id' => $this->project->id],
            $attributes
        ));

        return $this;
    }
    
    protected function createTask($attributes = [])
    {
        $this->project = create('App\Project', ['user_id' => auth()->id()]);
        $this->task = create('App\Task', array_merge(
            ['project_id' => $this->project->id],
            $attributes
        ));

        return $this;
    }

    protected function createTasks($attributes = [], $number = 1)
    {
        $this->project = create('App\Project', ['user_id' => auth()->id()]);
        $this->tasks = create('App\Task', array_merge(
            ['project_id' => $this->project->id],
            $attributes
        ), $number);

        return $this;
    }

    protected function shuffleTasks()
    {
        $this->project->refresh();
        $tasksOrder = $this->project->tasks_order;
        shuffle($tasksOrder);
        $this->project->tasks_order = $tasksOrder;

        return $this;
    }

    protected function editTask($attributes = [])
    {
        foreach ($attributes as $attribute => $value) {
            $this->task->$attribute = $value;
        }

        return $this;
    }
}
