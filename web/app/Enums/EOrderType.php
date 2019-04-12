<?php
namespace App\Enums;


abstract class EOrderType {
    const ARBITRARY_SERVICE_ORDER = 1;
    const BUY_PRODUCT_ORDER = 2;
    const REPLACEABLE_ITEM = 3;
}