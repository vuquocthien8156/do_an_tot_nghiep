<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimal-ui, shrink-to-fit=no"/>
    <meta name="description" content="page description"/>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-barstyle" content="black-translucent">
    <link rel="apple-touch-icon" href="/images/logo.png">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
    <meta name="mobile-web-app-capable" content="yes">

    <style type="text/css" rel="stylesheet" media="all">
        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
    </style>
</head>

<?php

$style = [
	/* Layout ------------------------------ */

	'body' => 'margin: 0; padding: 0; width: 100%; background-color: #F2F4F6;',
	'email-wrapper' => 'width: 100%; margin: 0; padding: 0; background-color: #F2F4F6;',

	/* Masthead ----------------------- */

	'email-masthead' => 'padding:0;text-align:center;',
	'email-masthead_name' => 'margin-top:10px; margin-left:20px',
	'email-masthead_inner' => 'background-color: #adbabe;',
	'email-masthead_image' => 'height:50px;text-align:center;',
	'email-masthead_cell' => 'padding-left:45px;',

	'email-body' => 'width: 100%; margin: 0; padding: 0;',
	'email-body_inner' => 'max-width: 800px; padding: 0; margin-top: 20px; margin-bottom: 30px;',
	'email-body_cell' => 'padding-left:45px;',

	'email-footer' => 'max-width: 800px; padding: 0; padding-left: 45px;  margin-bottom: 80px',
	'email-footer_cell' => 'color: #74787E; padding-left: 20px; padding-top: 0px; border-left: 2px solid #cfcfcf; font-style:italic;',

	/* Body ------------------------------ */

	'body_action' => 'width: 100%; margin: 30px auto; padding: 0; text-align: center;',
	'body_sub' => 'margin-top: 25px; padding-top: 25px; border-top: 1px solid #EDEFF2;',

	/* Type ------------------------------ */

	'anchor' => 'color: #3869D4;',
	'header-1' => 'margin-top: 0; color: #2F3133; font-size: 19px; font-weight: bold; text-align: left;',
	'greeting' => 'display:block; margin-top: 0; color: #5d5d5d; font-size: 16px; line-height: 1.5em; margin-bottom:0;',
	'paragraph' => 'display:block; margin-top: 0; color: #5d5d5d; font-size: 15px; line-height: 1.5em; font-style:italic',
	'paragraph-notice' => 'margin-top: 0; color: #555555; font-size: 15px; line-height: 1.5em; font-style:italic',
	'paragraph-sub' => 'margin-top: 0; margin-bottom: 0; color: #74787E; font-size: 16px; line-height: 1.5em;',
	'paragraph-sub-about' => 'margin-bottom: 0; color: #74787E; font-size: 16px; line-height: 1.5em;',
	'paragraph-sub-mail' => 'margin-top: 0; margin-bottom: 0; color: #FDD224; font-size: 16px; line-height: 1.5em;',
	'paragraph-center' => 'text-align: center;',
	'salutation' => 'line-height: 1.5em; color: #555555; font-size: 16px; margin-top:50px;',
	'salutation_text' => 'line-height: 1.5em; color: #555555; font-size: 16px; margin-bottom:0;font-style:italic;',
	'salutation_phone' => 'font-size: 12px; line-height: 1.5em; color: #555555; float:left',
	'salutation_email' => 'font-size: 12px; line-height: 1.5em; color: #555555; float:right; margin-bottom:30px;',

	'body_line' => 'display:inline-block',
	/* Buttons ------------------------------ */

	'button' => 'display: block; display: inline-block; width: 150px; min-height: 20px; padding: 7px;
                 background-color: #16467d; border-radius: 3px; color: #ffffff; font-size: 15px; line-height: 25px;
                 text-align: center; text-decoration: none; -webkit-text-size-adjust: none;',

	'button--green' => 'background-color: #22BC66;',
	'button--red' => 'background-color: #dc4d2f;',
	'button--blue' => 'background-color: #16467d;',
];
?>

<?php $fontFamily = 'font-family: Arial, \'Helvetica Neue\', Helvetica, sans-serif;'; ?>

@php
    $inProductionEnvironment = config('app.production') === 'production';
@endphp

<body style="{{ $style['body'] }}">
<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td style="{{ $style['email-wrapper'] }}">
            <table width="100%" cellpadding="0" cellspacing="0">
                <!-- Logo -->
                <tr style="{{ $style['email-masthead_inner'] }}">
                    <td style="{{ $style['email-masthead'] }}">

                        <p style="{{ $style['email-masthead_name'] }}"><img
                                    style="{{ $style['email-masthead_image'] }}" src="{{ $inProductionEnvironment ? secure_asset('images/logo-full.png') : asset('images/logo-full.png') }}"/></p>
                    </td>
                </tr>
                <!-- Email Body -->
                <tr>
                    <td style="{{ $style['email-body'] }}" width="100%">
                        <table style="{{ $style['email-body_inner'] }}" align="center" width="800" cellpadding="0"
                               cellspacing="0">
                            <tr>
                                <td style="{{ $fontFamily }} {{ $style['email-body_cell'] }}">
                                    <!-- Greeting -->
                                    <p style="{{ $style['greeting'] }}">
                                        Dear Trader,
                                    </p>
                                    <br/>
                                    <!-- Intro -->
                                    <!-- Intro -->
                                    @foreach ($introLines as $line)
                                        <p style="{{ $style['greeting'] }}">
                                            {{ $line }}
                                        </p>
                                    @endforeach

                                <!-- Action Button -->
                                    @if (isset($actionText))
                                        <table style="{{ $style['body_action'] }}" align="center" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td align="center">
													<?php
													switch ($level) {
														case 'success':
															$actionColor = 'button--green';
															break;
														case 'error':
															$actionColor = 'button--red';
															break;
														default:
															$actionColor = 'button--blue';
													}
													?>

                                                    <a href="{{ $actionUrl }}"
                                                       style="{{ $fontFamily }} {{ $style['button'] }} {{ $style[$actionColor] }}"
                                                       class="button"
                                                       target="_blank">
                                                        {{ $actionText }}
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    @endif

                                <!-- Outro -->
                                    @foreach ($outroLines as $line)
                                        <p style="{{ $style['paragraph'] }}">
                                            {{ $line }}
                                        </p>
                                @endforeach

                                <!-- Salutation -->
                                    <p style="{{ $style['salutation'] }}">
                                        {{ trans('common.salutation') }},<br>{{ config('app.name')}}, Support team
                                    </p>
                                    <p style="{{ $style['salutation_text'] }}">
                                        We are here to answer questions 24 hours a day during the trading week
                                    </p>
                                    <hr>
                                    <div>
                                        <strong><span style="{{ $style['salutation_email'] }}">
                                        support@trade-option.co
                                        </span></strong>
                                    </div>
                                    <!-- body -->
                                    <div style="{{ $style['body_line'] }}">
                                        <p style="{{ $style['paragraph'] }}">
                                            <strong><span
                                                        style="{{ $style['paragraph-notice'] }}">Notice: </span></strong>
                                            The information contained in this message may be privileged, confidential,
                                            and protected from disclosure. If the reader of this message is not the
                                            intended recipient, you are hereby notified that any dissemination,
                                            distribution, or copying of this communication is strictly prohibited. If
                                            you have received this communication in error, please notify us immediately
                                            by replying to this message, and then delete it from your computer. All
                                            e-mails sent to this address will be received by Trade-Option and are
                                            subject to archiving and review by someone other than the recipient. This
                                            communication is for information purposes only. Any comments or statements
                                            made herein do not necessarily reflect those of Trade-Option, its
                                            subsidiaries and affiliates.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
