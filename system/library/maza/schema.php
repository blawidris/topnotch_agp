<?php
/**
 * @package		Maza
 * @author		Jay Padaliya
 * @copyright   Copyright (c) 2021, Templatemaza.com
 * @license		per domain
 * @link		https://www.templatemaza.com
*/
namespace maza;

class Schema {
	static public function breadcrumb(array $breadcrumbs): array {
        $itemListElement = array();

        foreach (array_values($breadcrumbs) as $key => $value) {
            if($key === 0 && $value['text'][0] === '<'){
                $name = Registry::get('language')->get('text_home_text');
            } else {
                $name = $value['text'];
            }

            $listItem = array(
                '@type' => 'ListItem',
                'position' => $key + 1,
                'name' => $name,
            );

            if($key !== (count($breadcrumbs) - 1)){
                $listItem['item'] = $value['href'];
            }

            $itemListElement[] = $listItem;
        }

        return array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemListElement
        );
    }

    static public function organization(): array {
        return array(
            '@context' => 'http://schema.org',
            '@type' => 'Organization',
            'url' => Registry::config('mz_store_url'),
            'name' => Registry::config('config_name'),
            'logo' => getImageUrl(Registry::config('config_logo')),
            'contactPoint' => [
                [
                    '@type' => 'ContactPoint',
                    'telephone' => Registry::config('config_telephone'),
                    'email' => Registry::config('config_email'),
                    'contactType' => Registry::get('language')->get('text_customer_service'),
                ]
            ]
        );
    }

    static public function website(): array {
        return array(
            '@context' => 'http://schema.org',
            '@type' => 'WebSite',
            'url' => Registry::config('mz_store_url'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => urldecode(Registry::get('url')->link('product/search', 'search={search_term_string}')),
                'query' => 'required',
                'query-input' => 'required name=search_term_string',
            ]
        );
    }
}
