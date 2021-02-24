<?php

namespace Cblink\Service\Sale\Kernel;

use Cblink\DTO\DTO;
use Illuminate\Support\Arr;

/**
 * Class OrderDto
 * @package App\Modules\Api\Dto
 * @property-read integer $member_id            访问者ID
 * @property-read string $buyer_id              购买方ID
 * @property-read string $shop_id               店铺ID
 * @property-read string $trade_no              订单编号
 * @property-read numeric $post_fee             运费
 * @property-read numeric $total_fee            总金额
 * @property-read numeric $package_fee          打包费
 * @property-read array $products               商品信息
 *
 */
class OrderDto extends DTO
{

    protected $fillable = ['*'];

    public function rules(): array
    {
        return [
            'member_id' => ['required', 'integer'],
            'buyer_id' => ['required', 'string', 'max:64'],
            'shop_id' => ['required', 'string', 'max:64'],
            'trade_no' => ['required', 'string', 'max:32'],
            'post_fee' => ['required', 'numeric', 'min:0'],
            'package_fee' => ['required', 'numeric', 'min:0'],
            'products' => ['required', 'array', 'min:1', 'max:500'],
            // 商品ID
            'products.*.id' => ['required', 'string', 'max:32'],
            // 商品数量
            'products.*.num' => ['required', 'integer', 'min:1'],
            // 商品分类
            'products.*.category' => ['array'],
            // 商品价格
            'products.*.price' => ['required', 'numeric', 'min:0'],
            // 商品重量
            'products.*.weight' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * @return array
     */
    public function getProductIds(): array
    {
        return Arr::pluck($this->getItem('products'), 'id');
    }
}
