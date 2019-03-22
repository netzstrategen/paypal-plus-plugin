<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the PayPal PLUS for WooCommerce package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WCPayPalPlus\WC;

use Inpsyde\Lib\PayPal\Exception\PayPalConnectionException;
use Inpsyde\Lib\Psr\Log\LoggerInterface as Logger;
use WCPayPalPlus\Api\ApiContextFactory;
use WCPayPalPlus\Order\OrderFactory;
use WCPayPalPlus\Setting\PlusStorable;
use WCPayPalPlus\Payment\PaymentPatchFactory;
use WCPayPalPlus\Session\Session;
use OutOfBoundsException;

/**
 * Class RedirectablePatcher
 * @package WCPayPalPlus\WC
 */
class RedirectablePatcher
{
    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var PaymentPatchFactory
     */
    private $paymentPatchFactory;

    /**
     * @var PlusStorable
     */
    private $settingRepository;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var CheckoutDropper
     */
    private $checkoutDropper;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * ReceiptPageRenderer constructor.
     * @param OrderFactory $orderFactory
     * @param PaymentPatchFactory $paymentPatchFactory
     * @param PlusStorable $settingRepository
     * @param Session $session
     * @param CheckoutDropper $checkoutDropper
     * @param Logger $logger
     */
    public function __construct(
        OrderFactory $orderFactory,
        PaymentPatchFactory $paymentPatchFactory,
        PlusStorable $settingRepository,
        Session $session,
        CheckoutDropper $checkoutDropper,
        Logger $logger
    ) {

        $this->orderFactory = $orderFactory;
        $this->paymentPatchFactory = $paymentPatchFactory;
        $this->settingRepository = $settingRepository;
        $this->session = $session;
        $this->checkoutDropper = $checkoutDropper;
        $this->logger = $logger;
    }

    /**
     * @param $orderId
     * @throws OutOfBoundsException
     * @throws \WCPayPalPlus\Order\OrderFactoryException
     */
    public function patchOrder($orderId)
    {
        assert(is_int($orderId));

        $this->session->set(Session::ORDER_ID, $orderId);
        $order = $this->orderFactory->createById($orderId);
        $paymentId = $this->session->get(Session::PAYMENT_ID);

        !$paymentId and $this->checkoutDropper->abortSession();

        $paymentPatcher = $this->paymentPatchFactory->create(
            $order,
            $paymentId,
            $this->settingRepository->invoicePrefix(),
            ApiContextFactory::getFromConfiguration()
        );

        try {
            $paymentPatcher->execute();
        } catch (PayPalConnectionException $exc) {
            $this->logger->error($exc->getData());
            $this->checkoutDropper->abortSession();
        }

        wp_enqueue_script('paypalplus-woocommerce-plus-paypal-redirect');
    }
}
