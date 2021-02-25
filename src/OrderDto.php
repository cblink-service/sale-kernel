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
 * @property-read int $discount_fee             优惠金额
 * @property-read int $total_fee                订单总金额
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

            // 订单原价
            'original_fee' => ['integer'],
            // 订单总金额
            'total_fee' => ['integer'],
            // 商品优惠金额
            'products_discount_fee' => ['integer'],
            // 订单优惠金额
            'discount_fee' => ['integer'],

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
     * @return array
     */
    public function toResult()
    {
        $result = $this->toArray();

        // 订单原价
        $originalAmount = 0;
        // 商品合计优惠金额
        $totalProductsDiscount = 0;

        foreach ($result['products'] as $key => $product) {

            // 商品小计
            $productTotalFee = 0;
            // 参与优惠的商品数量
            $discountNum = 0;

            // 如果有优惠的话，则进行计算
            if (isset($product['discount']) && count($product['discount']) >= 1) {
                foreach ($product['discount'] as $discount) {
                    $totalProductsDiscount += (int) bcmul($discount['num'], $discount['discount_fee']);
                    $discountNum += (int) $discount['num'];
                    $productTotalFee += (int) bcmul(bcmod($product['price'], $discount['discount_fee']), $discount['num']);
                }
            }

            // 没有优惠的商品 商品数量 * 单价
            $productTotalFee += (int) bcmul(bcmod($product['num'], $discountNum), $product['price']);

            // 累加订单原价
            $originalAmount += (int) bcmul($product['num'], $product['price']);

            // 增加商品小计字段
            $result['products'][$key]['total_fee'] = $productTotalFee;
        }

        // 订单原价
        $result['original_fee'] = $originalAmount;
        // 订单的优惠金额
        $result['discount_fee'] = array_sum(Arr::pluck($result, 'discount.discount_fee'));
        // 商品总优惠
        $result['products_discount_fee'] = $totalProductsDiscount;
        // 订单总金额
        $result['total_fee'] = bcmod(bcmod($originalAmount, $totalProductsDiscount), $result['discount_fee']);

        return $result;
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
        }, $payload['discount']);

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
