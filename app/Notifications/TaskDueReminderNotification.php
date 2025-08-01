<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Task;

class TaskDueReminderNotification extends Notification
{
    protected $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function via($notifiable)
    {
        return ['mail'];  // Send immediately via mail channel
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Task Due Today: ' . $this->task->title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('This is a reminder that the following task is due today:')
            ->line('Title: ' . $this->task->title)
            ->line('Description: ' . ($this->task->description ?? 'No description'))
            ->line('Priority: ' . ucfirst($this->task->priority))
            ->line('Status: ' . ucfirst($this->task->status))
            ->action('View Task', url('/tasks/' . $this->task->id))
            ->line('Please make sure to complete it on time.');
    }
}
