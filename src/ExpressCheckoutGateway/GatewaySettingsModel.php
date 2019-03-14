<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the PayPal PLUS for WooCommerce package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WCPayPalPlus\ExpressCheckoutGateway;

use WCPayPalPlus\Setting\ExpressCheckoutStorable;
use WCPayPalPlus\Setting\SettingsGatewayModel;
use WCPayPalPlus\Setting\SharedFieldsOptionsTrait;
use WCPayPalPlus\Setting\SharedSettingsModel;
use WC_Payment_Gateway;
use WCPayPalPlus\Setting\Storable;

/**
 * Class GatewaySettingsModel
 *
 * @package WCPayPalPlus\ExpressCheckoutGateway
 */
final class GatewaySettingsModel implements SettingsGatewayModel
{
    use SharedFieldsOptionsTrait;

    /**
     * @var SharedSettingsModel
     */
    private $sharedSettingsModel;

    /**
     * GatewaySettingsModel constructor.
     * @param SharedSettingsModel $sharedSettingsModel
     */
    public function __construct(SharedSettingsModel $sharedSettingsModel)
    {
        $this->sharedSettingsModel = $sharedSettingsModel;
    }

    /**
     * @param WC_Payment_Gateway $gateway
     * @return array
     */
    public function settings(WC_Payment_Gateway $gateway)
    {
        /** @noinspection AdditionOperationOnArraysInspection */
        $settings =
            $this->general()
            + $this->sharedSettingsModel->credentials()
            + $this->sharedSettingsModel->webProfile($gateway)
            + $this->gateway()
            + $this->buttons()
            + $this->sharedSettingsModel->downloadLog();

        return $settings;
    }

    /**
     * @return array
     */
    private function general()
    {
        return [
            'enabled' => [
                'title' => esc_html_x('Enable/Disable', 'gateway-settings', 'woo-paypalplus'),
                'type' => 'checkbox',
                'label' => esc_html_x(
                    'Enable PayPal Express Checkout',
                    'gateway-settings',
                    'woo-paypalplus'
                ),
                'default' => 'no',
            ],
            'title' => [
                'title' => esc_html_x('Title', 'gateway-settings', 'woo-paypalplus'),
                'type' => 'text',
                'description' => esc_html_x(
                    'This controls the name of the payment gateway the user sees during checkout.',
                    'gateway-settings',
                    'woo-paypalplus'
                ),
                'default' => esc_html_x('Paypal Checkout', 'gateway-setting', 'woo-paypalplus'),
            ],
            'description' => [
                'title' => esc_html_x('Description', 'gateway-setting', 'woo-paypalplus'),
                'type' => 'text',
                'description' => esc_html_x(
                    'This controls the payment gateway description the user sees during checkout.',
                    'gateway-setting',
                    'woo-paypalplus'
                ),
            ],
        ];
    }

    /**
     * @return array
     */
    private function gateway()
    {
        $invoicePrefix = $this->sharedSettingsModel->invoicePrefix();
        $options = [
            'cancel_url' => [
                'title' => esc_html_x('Cancel Page', 'gateway-setting', 'woo-paypalplus'),
                'description' => esc_html_x(
                    'Sets the page users will be returned to if they click the Cancel link on the PayPal checkout pages.',
                    'gateway-setting',
                    'woo-paypalplus'
                ),
                'type' => 'select',
                'options' => $this->cancelPageOptions(),
                'default' => wc_get_page_id('checkout'),
            ],
            'cancel_custom_url' => [
                'title' => esc_html_x(
                    'Custom Cancellation URL',
                    'gateway-setting',
                    'woo-paypalplus'
                ),
                'type' => 'text',
                'description' => esc_html_x(
                    'URL to a custom page to be used for cancelation. Please select "custom" above first.',
                    'gateway-setting',
                    'woo-paypalplus'
                ),
            ],
        ];

        return array_merge(
            [
                'settings_section' => [
                    'title' => esc_html_x('Settings', 'gateway-setting', 'woo-paypalplus'),
                    'type' => 'title',
                    'desc' => '',
                ],
            ],
            $invoicePrefix,
            $options
        );
    }

    /**
     * @return array
     */
    private function buttons()
    {
        return [
            ExpressCheckoutStorable::OPTION_SHOW_ON_PRODUCT_PAGE => [
                'title' => esc_html_x(
                    'Show button on single product pages',
                    'gateway-setting',
                    'woo-paypalplus'
                ),
                'type' => 'checkbox',
                'default' => Storable::OPTION_ON,
                'description' => esc_html_x(
                    'Allow you to show or not the Express Checkout button within single product pages.',
                    'gateway-setting',
                    'woo-paypalplus'
                ),
            ],
            ExpressCheckoutStorable::OPTION_SHOW_ON_MINI_CART => [
                'title' => esc_html_x(
                    'Show button on mini cart',
                    'gateway-setting',
                    'woo-paypalplus'
                ),
                'type' => 'checkbox',
                'default' => Storable::OPTION_ON,
                'description' => esc_html_x(
                    'Allow you to show or not the Express Checkout button within the mini cart.',
                    'gateway-setting',
                    'woo-paypalplus'
                ),
            ],
            ExpressCheckoutStorable::OPTION_SHOW_ON_CART => [
                'title' => esc_html_x(
                    'Show button on cart page',
                    'gateway-setting',
                    'woo-paypalplus'
                ),
                'type' => 'checkbox',
                'default' => Storable::OPTION_ON,
                'description' => esc_html_x(
                    'Allow you to show or not the Express Checkout button within the cart page.',
                    'gateway-setting',
                    'woo-paypalplus'
                ),
            ],
        ];
    }
}
