<?php

namespace Cblink\Service\Sale\Kernel;

/**
 * Interface BaseActivity
 * @package App\Modules\Activitiy
 */
interface BaseActivity
{
    public function handle(OrderDto $dto): OrderDto;
}
