<?php
namespace App\Enums;


abstract class EDateFormat {
    const MODEL_DATE_FORMAT_DEFAULT = 'Y-m-d H:i';
    const MODEL_DATE_FORMAT_NORMAL = 'Y-m-d';
    const MODEL_DATE_FORMAT_DETAIL = 'Y-m-d H:i:s';

	const MODEL_DATE_FORMAT = 'Y-m-d H:i:s.u O';
	const DATE_FORMAT_WITHOUT_MICROSECOND = 'Y-m-d H:i:s O';
}