<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaCache extends Controller {
        public function page() {
                $this->response->setOutput($this->mz_cache->getVar('page_cache'));
        }
}
