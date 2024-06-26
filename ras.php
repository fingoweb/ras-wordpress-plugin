<?php

namespace RasOnet;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use RasOnet\Hooks\CheckVersionHook;
use RasOnet\Hooks\ProductHook;
use WP_REST_Server;

include 'autoload.php';

/**
 * @package Ras
 * @version 1.0
 * @license Apache License 2.0
 *
 * Plugin Name: Retail Media Network - Onet Ads
 * Plugin URI: https://panel.onetads.pl
 * Description: Błyskawiczna integracja Woocommerce z Retail Media Network w Onet Ads. Wykorzystaj ten plugin, żeby szybko i intuicyjnie zaimplementować nasze formaty reklamowe w swoim sklepie.
 * Author: Ringier Axel Springer Polska sp. z o.o.
 * Text Domain: onet-ads-rmn
 * Author URI: https://ringieraxelspringer.pl/
 * Version: 1.0
 * Requires Plugins: woocommerce
 */
class Ras
{

    public function ras_register_routes()
    {
        register_rest_route('ras', '/get-product-html', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'ras_get_product_html'],
            'args' => [
                'product_id' => [
                    'required' => true,
                    'validate_callback' => function ($param) {
                        return is_numeric($param);
                    }
                ],
            ],
            'permission_callback' => '__return_true'
        ]);

        register_rest_route('ras', '/get-html-templates', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'ras_get_product_list_html'],
            'permission_callback' => '__return_true'
        ]);
    }

    public function ras_get_product_html($request): void
    {
        $productHook = new ProductHook();

        $productHook->ras_get_product_html($request);
    }

    public function ras_get_product_list_html(): void
    {
        $productHook = new ProductHook();

        $productHook->ras_return_required_html_elements();
    }

    /**
     * @return void
     */
    public function ras_check_versions(): void
    {
        new CheckVersionHook(plugin_basename( __FILE__ ));
    }
}

add_action('rest_api_init', [new Ras(), 'ras_register_routes']);
add_action('plugins_loaded', [new Ras(), 'ras_check_versions']);