<?php

namespace Cblink\Service\Sale\Kernel;

use Cblink\DTO\DTO;

/**
 * Class DiscountDto
 * @package Cblink\Service\Sale\Kernel
 * @property-read string $name                  优惠名称
 * @property-read int $num                      优惠的数量
 * @property-read integer $discount_fee         优惠金额
 * @property-read integer $discount_rate        折扣率，除以100为实际折扣率
 * @property-read string|null $label            标签
 */
class DiscountDto extends DTO
{

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'label' => ['nullable', 'string', 'max:20'],
            'num' => ['required', 'integer', 'min:0'],
            'discount_rate' => ['required', 'integer', 'min:0', 'max:100'],
            'discount_fee' => ['required', 'numeric', 'min:0'],
        ];
    }
}