<?php

namespace Cblink\Service\Sale\Kernel;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

/**
 * Class OrderResponse
 * @package Cblink\Service\Sale\Kernel
 */
class OrderResponse implements Arrayable
{
    /**
     * @var array
     */
    public $data;

    public function __construct(OrderDto $dto)
    {
        $this->data = $dto->toArray();
    }


    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = $this->data;
        // 计算商品信息
        $data['products'] = $this->getProducts($data['products']);
        // 设置订单原价
        $data['original_fee'] = array_sum(Arr::pluck($data['products'], 'original_fee'));
        // 设置商品累计优惠金额
        $data['products_discount_fee'] = array_sum(Arr::pluck($data['products'], 'total_discount_fee'));

        return $data;
    }

    /**
     * @param $discount
     * @return int
     */
    public function getDiscountFee($discount): int
    {
        if (isset($discount['discount']) && is_array($discount['discount'])) {
            return (int) array_sum(Arr::pluck($discount, 'discount_fee'));
        }

        return 0;
    }

    /**
     * @param $products
     * @return array
     */
    public function getProducts($products): array
    {
        return array_map(function($product){
            // 原价小计
            $product['original_fee'] = bcmul($product['price'], $product['num']);
            // 折扣小计
            $product['total_discount_fee'] = 0;

            // 如果有优惠的话，则进行计算
            if (isset($product['discount']) && count($product['discount']) >= 1) {
                foreach ($product['discount'] as $discount) {
                    // 累加优惠金额
                    $product['total_discount_fee'] += (int) bcmul($discount['num'], $discount['discount_fee']);
                }
            }

            // 订单小计
            $product['total_fee'] = bcsub($product['original_fee'], $product['total_discount_fee']);

            return $product;
        }, $products);
    }
}