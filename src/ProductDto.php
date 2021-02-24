<?php

namespace Cblink\Service\Sale\Kernel;

use Cblink\DTO\DTO;

/**
 * Class ProductDto
 * @package Cblink\Service\Sale\Kernel
 * @property-read integer $id
 * @property-read integer $num
 * @property-read array $category
 * @property-read integer $price
 * @property-read numeric $weight
 */
class ProductDto extends DTO
{

    protected $fillable = [
        'id',
        'num',
        'category',
        'price',
        'weight',
        'discount',
    ];

    /**
     * @param $dto
     */
    public function pushDiscount(DiscountDto $dto)
    {
        $discount = $this->getItem('discount', []);

        array_push($discount, $dto);

        $this->setAttribute('discount', $discount);
    }

    /**
     * 获取商品的总金额
     *
     * @return string
     */
    public function getTotalAmount(): string
    {
        return bcmul($this->getItem('num'), $this->getItem('price'));
    }

    /**
     * 通过优惠金额获取折扣率
     *
     * @param $discountFee
     * @return string|null
     */
    public function getDiscountRate($discountFee)
    {
        return bcdiv($this->getItem('price'), bcmod($this->getItem('price'), $discountFee), 2);
    }

    /**
     * 获取优惠的金额
     *
     * @param numeric $rate     折扣率 0~1之间
     * @return int
     */
    public function getDiscountFee($rate) :int
    {
        $rate = ($rate > 1 || $rate < 0) ? 1 : $rate;

        $rate = bcmul(1, $rate, 2);

        return (int) bcmul($rate, $this->getItem('price'));
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $payload = parent::toArray();

        $payload['discount'] = array_map(function(DiscountDto $dto){
            return $dto->toArray();
        }, $payload['discount']);

        return $payload;
    }

    public function rules(): array
    {
        return [];
    }
}