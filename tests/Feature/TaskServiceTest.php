<?php

namespace Tests\Feature;

use App\Contracts\NotificationContract;
use App\Contracts\ReportContract;
use App\Enums\ReportStatus;
use App\Enums\TaskTypes;
use App\Models\Report;
use App\Models\ReportLog;
use App\Models\Task;
use App\Models\TaskLog;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TaskServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected Report $task;
    protected ReportContract $taskService;

    public function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->taskService = app(ReportContract::class);
        $this->task = $this->taskService->new(TaskTypes::SupplierUnload, ['payload' => 'test'], $user);
    }

    public function test_new_task()
    {
        $this->assertNotNull($this->task);
        $this->assertTrue($this->task->isPending());
        $this->assertDatabaseHas(Task::class, [
            'status' => ReportStatus::pending,
            'type' => TaskTypes::SupplierUnload
        ]);
    }

    public function test_failed_task()
    {
        $status = $this->taskService->failed($this->task);

        $this->assertTrue($this->task->isFailed());
        $this->assertTrue($status);
    }

    public function test_cancelled_task()
    {
        $status = $this->taskService->cancelled($this->task);

        $this->assertTrue($this->task->isCancelled());
        $this->assertTrue($status);
    }

    public function test_finished_task()
    {
        $status = $this->taskService->finished($this->task);

        $this->assertTrue($this->task->isFinished());
        $this->assertTrue($status);
    }

    public function test_running_task()
    {
        $status = $this->taskService->running($this->task);

        $this->assertTrue($this->task->isRunning());
        $this->assertTrue($status);
    }

    public function test_is_not_cancelled_task()
    {
        $this->assertFalse($this->task->isCancelled());
    }

    public function test_change_message_task()
    {
        $status = $this->taskService->changeMessage($this->task, 'Тестовое сообщение');

        $this->assertTrue($status);
        $this->assertDatabaseHas(Task::class, [
            'message' => 'Тестовое сообщение'
        ]);
    }

    public function test_add_log_task()
    {
        $log = $this->taskService->addLog($this->task, 'Тест', [], 'Тестовое сообщение');

        $this->assertTrue($log instanceof ReportLog);
        $this->assertDatabaseHas(TaskLog::class, [
            'message' => 'Тестовое сообщение',
            'status' => 'Тест',
            'task_id' => $this->task->id
        ]);
    }
}
