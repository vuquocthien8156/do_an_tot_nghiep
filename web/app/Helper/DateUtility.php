<?php

namespace App\Helper;


use App\Enums\EDateFormat;
use Carbon\Carbon;

class DateUtility {
	public static function tryParsedDateFromFormat($date, $tz = null, $from_format = null) {
		if (empty($date)) {
			return null;
		}

		if (empty($from_format)) {
			$from_formats = [EDateFormat::MODEL_DATE_FORMAT, EDateFormat::DATE_FORMAT_WITHOUT_MICROSECOND];
		} else {
			$from_formats = [$from_format];
		}

		$type = gettype($date);
		$tmp = $date;
		if ($type === 'string') {
			foreach ($from_formats as $format) {
				try {
					return Carbon::createFromFormat($format, $date)->setTimezone($tz);
				} catch (\Exception $e) {
					// logger('date format error', compact('date', 'e'));
				}
			}
			return null;
		}
		if ($type === 'object' && !$date instanceof Carbon && !$date instanceof \Illuminate\Support\Carbon) {
			// logger('not a date object', compact('date', 'from_format', 'to_formal'));
			return null;
		}
		if ($type === 'integer') {
			return Carbon::createFromTimestampUTC($date)->setTimezone($tz);
		}

		if (empty($tmp)) {
			return '';
		}
		return $tmp->setTimezone($tz);
	}

	public static function hasTimePart(String $date) {
        if (Carbon::hasFormat($date, 'Y-m-d')) {
            return false;
        } else {
            return true;
        }
    }
}