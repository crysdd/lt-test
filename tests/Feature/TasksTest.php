<?php

namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TasksTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testTasks()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['tasks']
        );

        $response = $this->get('/api/v1/tasks');

        $response->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure(
            [
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'user_id',
                        'day',
                        'done',
                        'category_id',
                        'name',
                        'text',
                        'category_name'
                    ]
                ]
            ]
        );
    }

    public function testReReleaseTask()
    {
        $user = Sanctum::actingAs(
            User::factory()->create(),
            ['tasks']
        );
        $exercise = Exercise::factory()->create([
            'category_id' => 1,
            'name' => 'Exercise Name',
            'text' => 'Exercise Text',
        ]);
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'exercise_id' => $exercise->id,
            'day' => now()->format('Y-m-d'),
        ]);
        $payload = [
            'id' => $task->id,
        ];
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);

        $this->json('put', 'api/v1/tasks/change-task', $payload)
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure(
            [
                'data' => [
                    'id',
                    'user_id',
                    'day',
                    'done',
                    'category_id',
                    'name',
                    'text',
                    'category_name'
                ]
            ]
        );

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function testDone()
    {
        $user = Sanctum::actingAs(
            User::factory()->create(),
            ['tasks']
        );
        $exercise = Exercise::factory()->create([
            'category_id' => 1,
            'name' => 'Exercise Name',
            'text' => 'Exercise Text',
        ]);
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'exercise_id' => $exercise->id,
            'day' => now()->format('Y-m-d'),
        ]);
        $payload = [
            'id' => $task->id,
        ];

        $this->json('put', 'api/v1/tasks/done', $payload)
        ->assertStatus(Response::HTTP_OK)
        ->assertJson(
            [
                'data' => [
                    'success' => 'true',
                ]
            ]
        );

        $changed_task = Task::find($task->id);
        $this->assertEquals($changed_task->done, true);

    }
}
