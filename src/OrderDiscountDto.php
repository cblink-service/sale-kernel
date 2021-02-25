<?php

namespace Cblink\Service\Sale\Kernel;

use Cblink\DTO\DTO;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class OrderDiscountDto
 * @package Cblink\Service\Sale\Kernel
 * @property-read string $name          名称
 * @property-read string $label         标签
 * @property-read int $discount_fee     优惠金额
 */
class OrderDiscountDto extends DTO
{

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'label' => ['required', 'string', 'max:20'],
            'type' => ['required', sprintf("in:%s", implode(",", OrderConst::DISCOUNT_TYPE))],
            'discount_fee' => ['required', 'int', 'min:0', 'max:100000000'],
        ];
    }
}