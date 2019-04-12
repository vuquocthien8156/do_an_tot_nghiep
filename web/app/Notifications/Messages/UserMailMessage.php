<?php

namespace App\Notifications\Messages;

use Illuminate\Notifications\Messages\MailMessage;

class UserMailMessage extends MailMessage {
	/**
	 * The view for the message.
	 *
	 * @var string
	 */
	public $view = [
		'notifications::user-email',
		'notifications::user-email-plain',
	];

	public $name;

	public function name($name) {
		$this->name = $name;
		return $this;
	}

	public $title;

	public function title($title) {
		$this->title = $title;
		return $this;
	}

	/**
	 * Get an array representation of the message.
	 *
	 * @return array
	 */
	public function toArray() {
		return array_merge(parent::toArray(), [
			'title' => $this->title,
			'name' => $this->name,
		]);
	}
}
