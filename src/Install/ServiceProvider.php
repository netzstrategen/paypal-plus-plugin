<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the PayPal PLUS for WooCommerce package.
 */

namespace WCPayPalPlus\Install;

use WCPayPalPlus\ExpressCheckoutGateway\Gateway as ExpressCheckoutGateway;
use WCPayPalPlus\Service\BootstrappableServiceProvider;
use WCPayPalPlus\Service\Container;
use WCPayPalPlus\Setting\SharedPersistor;

/**
 * Class ServiceProvider
 * @package WCPayPalPlus\Installation
 */
class ServiceProvider implements BootstrappableServiceProvider
{
    /**
     * @inheritdoc
     */
    public function register(Container $container)
    {
        $container[Installer::class] = function (Container $container) {
            return new Installer(
                $container[SharedPersistor::class],
                $container[ExpressCheckoutGateway::class]
            );
        };
    }

    /**
     * @inheritdoc
     */
    public function bootstrap(Container $container)
    {
        $installer = $container[Installer::class];

        add_action('upgrader_process_complete', [$installer, 'afterInstall']);
        add_action('wp_loaded', function () use ($installer) {
            get_option(SharedPersistor::OPTION_NAME, null) or $installer->afterInstall();
        });
    }
}
