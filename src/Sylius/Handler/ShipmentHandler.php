<?php

declare(strict_types=1);

namespace BitBag\SyliusVueStorefrontPlugin\Sylius\Handler;

use BitBag\SyliusVueStorefrontPlugin\Sylius\Modifier\AdjustmentModifierInterface;
use BitBag\SyliusVueStorefrontPlugin\Sylius\Modifier\ShipmentModifierInterface;
use BitBag\SyliusVueStorefrontPlugin\Sylius\Provider\AdjustmentProviderInterface;
use BitBag\SyliusVueStorefrontPlugin\Sylius\Provider\ShipmentProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;

final class ShipmentHandler implements ShipmentHandlerInterface
{
    /** @var ShipmentProviderInterface */
    private $shipmentProvider;

    /** @var AdjustmentProviderInterface */
    private $adjustmentProvider;

    /** @var ShipmentModifierInterface */
    private $shipmentModifier;

    /** @var AdjustmentModifierInterface */
    private $adjustmentModifier;

    public function __construct(
        ShipmentProviderInterface $shipmentProvider,
        AdjustmentProviderInterface $adjustmentProvider,
        ShipmentModifierInterface $shipmentModifier,
        AdjustmentModifierInterface $adjustmentModifier
    ) {
        $this->shipmentProvider = $shipmentProvider;
        $this->adjustmentProvider = $adjustmentProvider;
        $this->shipmentModifier = $shipmentModifier;
        $this->adjustmentModifier = $adjustmentModifier;
    }

    public function handle(OrderInterface $cart, ShippingMethodInterface $shippingMethod): void
    {
        if (!$cart->hasShipments()) {
            $shipment = $this->shipmentProvider->provide($shippingMethod);
            $cart->addShipment($shipment);

            $adjustment = $this->adjustmentProvider->provide($shippingMethod);
            $cart->addAdjustment($adjustment);

            return;
        }

        $this->shipmentModifier->modify($cart, $shippingMethod);
        $this->adjustmentModifier->modify($cart, $shippingMethod);
    }
}