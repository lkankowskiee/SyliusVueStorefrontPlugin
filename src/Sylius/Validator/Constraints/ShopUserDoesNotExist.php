<?php

declare(strict_types=1);

namespace BitBag\SyliusVueStorefrontPlugin\Sylius\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class ShopUserDoesNotExist extends Constraint
{
    /** @var string */
    public $message = 'bitbag.vue_storefront_api.sylius.user.email.unique';

    public function validatedBy(): string
    {
        return 'sylius_shop_user_does_not_exist_validator';
    }
}
