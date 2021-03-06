<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\OrderProcessing;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\OrderProcessing\PricesRecalculator;
use Sylius\Component\Core\OrderProcessing\PricesRecalculatorInterface;
use Sylius\Component\Pricing\Calculator\DelegatingCalculatorInterface;
use Sylius\Component\Pricing\Model\PriceableInterface;
use Sylius\Component\User\Model\GroupableInterface;

/**
 * @mixin PricesRecalculator
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class PricesRecalculatorSpec extends ObjectBehavior
{
    function let(DelegatingCalculatorInterface $priceCalculator)
    {
        $this->beConstructedWith($priceCalculator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\OrderProcessing\PricesRecalculator');
    }

    function it_implements_prices_recalculator_interface()
    {
        $this->shouldImplement(PricesRecalculatorInterface::class);
    }

    function it_recalculates_prices_adding_customer_to_the_context(
        ArrayCollection $groups,
        DelegatingCalculatorInterface $priceCalculator,
        GroupableInterface $customer,
        OrderInterface $order,
        OrderItemInterface $item,
        PriceableInterface $variant
    ) {
        $order->getCustomer()->willReturn($customer);
        $order->getChannel()->willReturn(null);
        $order->getItems()->willReturn([$item]);

        $customer->getGroups()->willReturn($groups);
        $groups->toArray()->willReturn(['group1', 'group2']);

        $item->isImmutable()->willReturn(false);
        $item->getQuantity()->willReturn(5);
        $item->getVariant()->willReturn($variant);

        $priceCalculator
            ->calculate($variant, ['customer' => $customer, 'groups' => ['group1', 'group2'], 'quantity' => 5])
            ->willReturn(10)
        ;
        $item->setUnitPrice(10)->shouldBeCalled();

        $this->recalculate($order);
    }

    function it_recalculates_prices_adding_channel_to_the_context(
        ChannelInterface $channel,
        DelegatingCalculatorInterface $priceCalculator,
        OrderInterface $order,
        OrderItemInterface $item,
        PriceableInterface $variant
    ) {
        $order->getCustomer()->willReturn(null);
        $order->getChannel()->willReturn($channel);
        $order->getItems()->willReturn([$item]);

        $item->isImmutable()->willReturn(false);
        $item->getQuantity()->willReturn(5);
        $item->getVariant()->willReturn($variant);

        $priceCalculator
            ->calculate($variant, ['channel' => [$channel], 'quantity' => 5])
            ->willReturn(10)
        ;
        $item->setUnitPrice(10)->shouldBeCalled();

        $this->recalculate($order);
    }

    function it_recalculates_prices_adding_channel_and_customer_to_the_context(
        ArrayCollection $groups,
        ChannelInterface $channel,
        DelegatingCalculatorInterface $priceCalculator,
        GroupableInterface $customer,
        OrderInterface $order,
        OrderItemInterface $item,
        PriceableInterface $variant
    ) {
        $order->getCustomer()->willReturn($customer);
        $order->getChannel()->willReturn($channel);
        $order->getItems()->willReturn([$item]);

        $customer->getGroups()->willReturn($groups);
        $groups->toArray()->willReturn(['group1', 'group2']);

        $item->isImmutable()->willReturn(false);
        $item->getQuantity()->willReturn(5);
        $item->getVariant()->willReturn($variant);

        $priceCalculator
            ->calculate(
                $variant,
                [
                    'customer' => $customer,
                    'groups' => ['group1', 'group2'],
                    'channel' => [$channel],
                    'quantity' => 5
                ]
            )
            ->willReturn(10)
        ;
        $item->setUnitPrice(10)->shouldBeCalled();

        $this->recalculate($order);
    }

    function it_recalculates_prices_without_adding_anything_to_the_context_if_its_not_needed(
        DelegatingCalculatorInterface $priceCalculator,
        OrderInterface $order,
        OrderItemInterface $item,
        PriceableInterface $variant
    ) {
        $order->getCustomer()->willReturn(null);
        $order->getChannel()->willReturn(null);
        $order->getItems()->willReturn([$item]);

        $item->isImmutable()->willReturn(false);
        $item->getQuantity()->willReturn(5);
        $item->getVariant()->willReturn($variant);

        $priceCalculator->calculate($variant, ['quantity' => 5])->willReturn(10);
        $item->setUnitPrice(10)->shouldBeCalled();

        $this->recalculate($order);
    }
}
