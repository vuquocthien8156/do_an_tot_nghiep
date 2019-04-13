<?php
namespace App\Enums;


abstract class ErrorCode {
    const NO_ERROR = 0;
    const SYSTEM_ERROR = 1;
    const UNAUTHORIZED = 401;
}