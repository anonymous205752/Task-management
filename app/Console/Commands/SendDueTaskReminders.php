<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TaskDueReminderNotification;
use Carbon\Carbon;

class SendDueTaskReminders extends Command
{
    protected $signature = 'tasks:send-due-reminders';
    protected $description = 'Send email reminders for tasks due today';

    public function handle()
    {
        $today = Carbon::today();

        // Get tasks due today and not completed
        $tasksDueToday = Task::whereDate('due_date', $today)
                             ->where('status', '!=', 'completed')
                             ->with('user') // Make sure your Task model has user relationship defined
                             ->get();

        foreach ($tasksDueToday as $task) {
            $task->user->notify(new TaskDueReminderNotification($task));
        }

        $this->info('Due task reminders sent successfully.');
    }
}
