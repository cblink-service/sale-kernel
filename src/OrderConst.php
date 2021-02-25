<?php

namespace Cblink\Service\Sale\Kernel;

/**
 * Class OrderConst
 * @package Cblink\Service\Sale\Kernel
 */
class OrderConst
{

    const DISCOUNT_TYPE_ORDER = 1;
    const DISCOUNT_TYPE_POST = 2;
    const DISCOUNT_TYPE_PACKAGE = 3;
    const DISCOUNT_TYPE_COUPON = 4;
    const DISCOUNT_TYPE_USER = 5;
    const DISCOUNT_TYPE_OTHER = 10;

    const DISCOUNT_TYPE = [
        self::DISCOUNT_TYPE_ORDER => '订单促销优惠',
        self::DISCOUNT_TYPE_POST => '运费优惠',
        self::DISCOUNT_TYPE_PACKAGE => '打包费优惠',
        self::DISCOUNT_TYPE_COUPON => '卡券优惠',
        self::DISCOUNT_TYPE_USER => '会员权益优惠',
        self::DISCOUNT_TYPE_OTHER => '其他优惠',
    ];

}