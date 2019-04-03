<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the PayPal PLUS for WooCommerce package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WCPayPalPlus\Payment;

use Inpsyde\Lib\PayPal\Rest\ApiContext;
use WC_Order;
use WCPayPalPlus\Gateway\CurrentPaymentMethod;

/**
 * Class PaymentPatchFactory
 * @package WCPayPalPlus\Payment
 */
class PaymentPatchFactory
{
    /**
     * @var CurrentPaymentMethod
     */
    private $currentPaymentMethod;

    /**
     * PaymentPatchFactory constructor.
     * @param CurrentPaymentMethod $currentPaymentMethod
     */
    public function __construct(CurrentPaymentMethod $currentPaymentMethod)
    {
        $this->currentPaymentMethod = $currentPaymentMethod;
    }

    /**
     * @param WC_Order $order
     * @param string $paymentId
     * @param string $invoicePrefix
     * @param ApiContext $context
     *
     * @return PaymentPatcher
     */
    public function create(WC_Order $order, $paymentId, $invoicePrefix, ApiContext $context)
    {
        assert(is_string($paymentId));
        assert(is_string($invoicePrefix));

        $orderData = new OrderData($order);
        $patchProvider = new PatchProvider($order, $orderData);
        $patchData = new PaymentPatchData(
            $order,
            $paymentId,
            $invoicePrefix,
            $context,
            $patchProvider,
            $this->currentPaymentMethod
        );

        return new PaymentPatcher($patchData);
    }
}
