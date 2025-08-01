<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Task;

class TaskCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $task;

    // Accept task in constructor
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    // Channels for notification
    public function via($notifiable)
    {
        return ['mail'];
    }

    // Email content
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('New Task Created: ' . $this->task->title)
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('A new task has been created with the following details:')
                    ->line('Title: ' . $this->task->title)
                    ->line('Description: ' . ($this->task->description ?? 'No description'))
                    ->line('Status: ' . $this->task->status)
                    ->line('Priority: ' . $this->task->priority)
                    ->line('Due Date: ' . ($this->task->due_date ? $this->task->due_date->format('Y-m-d') : 'No due date'))
                    ->line('Thank you for using our task management app!');
    }
}
