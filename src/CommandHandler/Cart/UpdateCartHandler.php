<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusVueStorefrontPlugin\CommandHandler\Cart;

use BitBag\SyliusVueStorefrontPlugin\Command\Cart\UpdateCart;
use BitBag\SyliusVueStorefrontPlugin\Sylius\Modifier\OrderModifierInterface;
use BitBag\SyliusVueStorefrontPlugin\Sylius\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface as BaseProductVariantRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class UpdateCartHandler implements MessageHandlerInterface
{
    /** @var OrderRepositoryInterface */
    private $cartRepository;

    /** @var BaseProductVariantRepositoryInterface */
    private $baseProductVariantRepository;

    /** @var ProductVariantRepositoryInterface */
    private $productVariantRepository;

    /** @var OrderModifierInterface */
    private $orderModifier;

    public function __construct(
        OrderRepositoryInterface $cartRepository,
        BaseProductVariantRepositoryInterface $baseProductVariantRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        OrderModifierInterface $orderModifier
    ) {
        $this->cartRepository = $cartRepository;
        $this->baseProductVariantRepository = $baseProductVariantRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->orderModifier = $orderModifier;
    }

    public function __invoke(UpdateCart $updateCart): void
    {
        /** @var OrderInterface $cart */
        $cart = $this->cartRepository->findOneBy(['tokenValue' => $updateCart->cartId()]);

        Assert::notNull($cart, 'Cart has not been found.');

        /** @var BaseProductVariantRepositoryInterface $productVariant */
        $productVariant = $this->baseProductVariantRepository->findOneByCode($updateCart->cartItem()->getSku());

        if ($productVariant === null) {
            /** @var ProductVariantInterface $productVariant */
            $productVariant = $this->productVariantRepository->getVariantForOptionValuesBySku(
                $updateCart->cartItem()->getSku(),
                $updateCart->cartItem()->product_option->extension_attributes->configurable_item_options
            );
        }

        Assert::notNull($productVariant, 'Product variant has not been found.');

        $product = $productVariant->getProduct();

        Assert::notNull($product);

        Assert::true(
            in_array($cart->getChannel(), $product->getChannels()->toArray(), true),
            'Product is not in same channel as cart.'
        );

        $this->orderModifier->modify(
            $cart, $productVariant, $updateCart->cartItem()->getQuantity(), $updateCart->getOrderItemUuid()
        );
    }
}
