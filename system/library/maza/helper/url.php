<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
*/
namespace maza;

function getImageURL($image): string {
    if(is_file(DIR_IMAGE . $image)){
        return Registry::config('mz_store_url') . 'image/' . $image;
    }

    return '';
}

function getEmbed(string $url): string {
    // Youtube
    $youtube_id = '';

    if (preg_match('/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))(\w+)/i', $url, $matches)) {
        $youtube_id = $matches[count($matches) - 1];
    }

    if (preg_match('/youtu.be\/([a-zA-Z0-9_]+)\??/i', $url, $matches)) {
        $youtube_id = $matches[count($matches) - 1];
    }

    if ($youtube_id) {
        return 'https://www.youtube.com/embed/' . $youtube_id;
    }

    // Vimeo
    if (preg_match('/https:\/\/vimeo.com\/(\\d+)/', $url, $regs)){
        return 'https://player.vimeo.com/video/' . $regs[1];// . '?title=0&byline=0&portrait=0&badge=0&color=ffffff';
    }

    return $url;
}