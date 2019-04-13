<?php

namespace App\Notifications;

use App\Notifications\Messages\UserMailMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

abstract class UserMailNotification extends Notification implements ShouldQueue {
	use Queueable;

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param  mixed $notifiable
	 * @return array
	 */
	public function via($notifiable) {
		return ['mail'];
	}

	/**
	 * Get the mail representation of the notification.
	 *
	 * @param  mixed $notifiable
	 * @return \App\Notifications\Messages\UserMailMessage
	 */
	public function toMail($notifiable) {
		return $this->getMailInstance(new UserMailMessage(), $notifiable);
	}

	/**
	 * Must override in delivered class. Return instance of UserMailMessage
	 * @param $mail \App\Notifications\Messages\UserMailMessage
	 * @param $notifiable
	 * @return \App\Notifications\Messages\UserMailMessage
	 */
	protected abstract function getMailInstance($mail, $notifiable);
}
