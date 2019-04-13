<?php

namespace App\Notifications;

class ConfirmEmail extends UserMailNotification {
	/**
	 * The password reset token.
	 *
	 * @var string
	 */
	public $confirmation_code;

	/**
	 * Create a notification instance.
	 *
	 * @param  string $confirmation_code
	 * @return void
	 */
	public function __construct($confirmation_code) {
		$this->confirmation_code = $confirmation_code;
	}

	/**
	 * Must override in delivered class. Return instance of UserMailMessage
	 * @param $mail \App\Notifications\Messages\UserMailMessage
	 * @param $notifiable
	 * @return \App\Notifications\Messages\UserMailMessage
	 */
	protected function getMailInstance($mail, $notifiable) {
		return $mail->title('Email verification')
			->subject(trans('verify.email-title'))
			->line(trans('verify.email-intro'))
			->action(trans('verify.email-button'), route('email.confirm', ['token' => $this->confirmation_code]));
	}
}
