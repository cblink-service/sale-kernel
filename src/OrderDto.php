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
 * @property-read int $post_fee                 运费
 * @property-read int $package_fee              打包费
 * @property-read array $products               商品信息
 * @property-read array $discount               订单优惠内容
 */
class OrderDto extends DTO
{
    /**
     * @var string[]
     */
    protected $fillable = ['*'];

    /**
     * @var array
     */
    protected $productIds = [];

    public function rules(): array
    {
        return [
            'member_id' => ['required', 'integer'],
            'buyer_id' => ['required', 'string', 'max:64'],
            'shop_id' => ['required', 'string', 'max:64'],
            'trade_no' => ['required', 'string', 'max:32'],
            'post_fee' => ['required', 'integer', 'min:0'],
            'package_fee' => ['required', 'integer', 'min:0'],
            'products' => ['required', 'array', 'min:1', 'max:500'],
            // 优惠的内容
            'discount' => ['array'],

            // 商品ID
            'products.*.id' => ['required', 'string', 'max:32'],
            // 商品数量
            'products.*.num' => ['required', 'integer', 'min:1'],
            // 商品分类
            'products.*.category' => ['array'],
            // 商品价格
            'products.*.price' => ['required', 'integer', 'min:0'],
            // 商品重量
            'products.*.weight' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * @return OrderResponse
     */
    public function toResponse(): OrderResponse
    {
        return new OrderResponse($this);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $payload = parent::toArray();

        $payload['products'] = array_map(function($dto){
            return $dto instanceof ProductDto ? $dto->toArray() : $dto;
        }, $payload['products']);

        $payload['discount'] = array_map(function($dto){
            return $dto instanceof OrderDiscountDto ? $dto->toArray() : $dto;
        }, $payload['discount'] ?? []);

        return $payload;
    }

    /**
     * 增加优惠
     *
     * @param $dto
     */
    public function pushDiscount(OrderDiscountDto $dto)
    {
        $discount = $this->getItem('discount', []);

        array_push($discount, $dto);

        $this->setAttribute('discount', $discount);
    }

    /**
     * @return ProductDto[]
     * @throws \Throwable
     */
    public function getProducts(): array
    {
        return array_map(function($item){
            return new ProductDto($item);
        }, $this->getItem('products'));
    }

    /**
     * @param $items
     * @return $this
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Throwable
     */
    public function reload($items)
    {
        $this->baseValidate($items);
        $this->setPayload($items);
        return $this;
    }

    /**
     * @return array
     */
    public function getProductIds(): array
    {
        if (!$this->productIds) {
            $this->productIds = Arr::pluck($this->getItem('products'), 'id');
        }

        return $this->productIds;
    }
}
