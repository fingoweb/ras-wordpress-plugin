<?php

namespace RasOnet\Hooks;

use RasOnet\Utilities\ProductUtil;
use WP_Query;
use WP_REST_Request;
use WP_REST_Response;

class ProductHook
{

    /**
     * @param WP_REST_Request $request
     * @return void|WP_REST_Response
     */
    public function ras_get_product_html(WP_REST_Request $request): void
    {
        $product_id = $request->get_param('product_id');

        $productUtil = new ProductUtil($product_id);
        if (!$productUtil->is_product_in_stock()) {
            wp_send_json([
                'message' => 'Product not found',
            ], 404);
        }

        wp_send_json([
            'product_html' => $productUtil->get_product_html(),
            'link_url' => $productUtil->get_product_link_html()
        ]);
    }

    public function ras_return_required_html_elements(): void
    {
        $args = [
            'post_type' => 'product',
            'posts_per_page' => 1,
            'orderby' => 'ID',
            'post_status' => 'publish'
        ];

        $query = new WP_Query($args);

        $productId = null;

        if ($query->have_posts()) {
            $query->the_post();

            global $product;

            $productId = $product->get_id();
        }

        wp_reset_postdata();

        if (is_null($productId)) {
            wp_send_json([
                'message' => 'No products found',
            ], 404);
        }

        $productUtil = new ProductUtil($productId);

        $promoTags = $productUtil->get_product_promo_tag();

        wp_send_json([
            'list_container_tag' => $productUtil->get_product_list_container_tag(),
            'product_tag' => $productUtil->get_product_tag(),
            'original_promo_tag' => $promoTags['original'],
            'substitute_promo_tag' => $promoTags['substitute'],
        ]);
    }


}