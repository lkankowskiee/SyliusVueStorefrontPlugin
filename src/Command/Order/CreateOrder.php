<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusVueStorefrontPlugin\Command\Order;

use BitBag\SyliusVueStorefrontPlugin\Command\CommandInterface;
use BitBag\SyliusVueStorefrontPlugin\Model\Request\Cart\AddressInformation;
use BitBag\SyliusVueStorefrontPlugin\Model\Request\Order\Product;

final class CreateOrder implements CommandInterface
{
    /** @var string|null */
    private $cartId;

    /** @var Product[] */
    private $products;

    /** @var AddressInformation */
    private $addressInformation;

    public function __construct(
        ?string $cartId,
        AddressInformation $addressInformation,
        Product ...$products
    ) {
        $this->cartId = $cartId;
        $this->products = $products;
        $this->addressInformation = $addressInformation;
    }

    public function cartId(): ?string
    {
        return $this->cartId;
    }

    public function products(): array
    {
        return $this->products;
    }

    public function addressInformation(): AddressInformation
    {
        return $this->addressInformation;
    }
}