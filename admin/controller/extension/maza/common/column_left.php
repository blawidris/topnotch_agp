<?php
class ControllerExtensionMazaCommonColumnLeft extends Controller {
	public function index() {
		if (isset($this->request->get['user_token']) && isset($this->session->data['user_token']) && ($this->request->get['user_token'] == $this->session->data['user_token'])) {
			$this->load->language('extension/maza/common/column_left');
                        $this->load->model('tool/image');
                        
                        $url = '';
                
                        if(isset($this->request->get['mz_theme_code'])){
                            $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                        }

                        if(isset($this->request->get['mz_skin_id'])){
                            $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                        }

                        $data['home'] = $this->url->link('extension/maza/skin', 'user_token=' . $this->session->data['user_token'] . $url, true);
			
			// Menu
			$data['menus'] = array();
                        
                        // Skin
			if ($this->user->hasPermission('access', 'extension/maza/skin')) {
				$data['menus'][] = array(
                                        'id'       => 'mz-menu-skin',
                                        'icon'	   => 'fa-desktop',
                                        'name'	   => $this->language->get('text_skin'),
                                        'active'   => ($this->request->get['route'] === 'extension/maza/skin')?TRUE: FALSE,
                                        'href'     => $this->url->link('extension/maza/skin', 'user_token=' . $this->session->data['user_token'] . $url, true),
                                        'children' => array()
                                );
			}
			
                        // style
			if ($this->user->hasPermission('access', 'extension/maza/style')) {
				$data['menus'][] = array(
                                        'id'       => 'mz-menu-style',
                                        'icon'	   => 'fa-paint-brush',
                                        'name'	   => $this->language->get('text_style'),
                                        'active'   => ($this->request->get['route'] === 'extension/maza/style')?TRUE: FALSE,
                                        'href'     => $this->url->link('extension/maza/style', 'user_token=' . $this->session->data['user_token'] . $url, true),
                                        'children' => array()
                                );
			}
                        
                        // catalog
                        if ($this->user->hasPermission('access', 'extension/maza/catalog')) {
                                $data['menus'][] = array(
                                        'id'       => 'mz-menu-catalog',
                                        'icon'     => 'fa-file-text',
                                        'name'     => $this->language->get('text_catalog'),
                                        'active'   => ($this->request->get['route'] === 'extension/maza/catalog')?TRUE: FALSE,
                                        'href'     => $this->url->link('extension/maza/catalog', 'user_token=' . $this->session->data['user_token'] . $url, true),
                                        'children' => array()
                                );
                        }
                        
                        // layout builder
                        if ($this->user->hasPermission('access', 'extension/maza/layout_builder')) {
                                $data['menus'][] = array(
                                        'id'       => 'mz-menu-layout-builder',
                                        'icon'     => 'fa-object-group',
                                        'name'     => $this->language->get('text_layout_builder'),
                                        'active'   => ($this->request->get['route'] === 'extension/maza/layout_builder')?TRUE: FALSE,
                                        'href'     => $this->url->link('extension/maza/layout_builder', 'user_token=' . $this->session->data['user_token'] . $url, true),
                                        'children' => array()
                                );
                        }
                        
                        // content builder
                        if ($this->user->hasPermission('access', 'extension/maza/content_builder')) {
                                $data['menus'][] = array(
                                        'id'       => 'mz-menu-content-builder',
                                        'icon'     => 'fa-table',
                                        'name'     => $this->language->get('text_content_builder'),
                                        'active'   => (strpos($this->request->get['route'], 'extension/maza/content_builder') === 0)?TRUE: FALSE,
                                        'href'     => $this->url->link('extension/maza/content_builder', 'user_token=' . $this->session->data['user_token'] . $url, true),
                                        'children' => array()
                                );
                        }
                        
                        // page builder
                        if ($this->user->hasPermission('access', 'extension/maza/page_builder')) {
                                $data['menus'][] = array(
                                        'id'       => 'mz-menu-page-builder',
                                        'icon'     => 'fa-columns',
                                        'name'     => $this->language->get('text_page_builder'),
                                        'active'   => (strpos($this->request->get['route'], 'extension/maza/page_builder') === 0)?TRUE: FALSE,
                                        'href'     => $this->url->link('extension/maza/page_builder', 'user_token=' . $this->session->data['user_token'] . $url, true),
                                        'children' => array()
                                );
                        }

                        // Module
                        if ($this->user->hasPermission('access', 'extension/maza/module')) {
                                $data['menus'][] = array(
                                        'id'       => 'mz-menu-module',
                                        'icon'     => 'fa-plug',
                                        'name'     => $this->language->get('text_module'),
                                        'active'   => ($this->request->get['route'] === 'extension/maza/module')?TRUE: FALSE,
                                        'href'     => $this->url->link('extension/maza/module', 'user_token=' . $this->session->data['user_token'] . $url, true),
                                        'children' => array()
                                );
                        }
                        
                        // Menu
                        if ($this->user->hasPermission('access', 'extension/maza/menu')) {
                                $data['menus'][] = array(
                                        'id'       => 'mz-menu-menu',
                                        'icon'     => 'fa-bars',
                                        'name'     => $this->language->get('text_menu'),
                                        'active'   => (strpos($this->request->get['route'], 'extension/maza/menu') === 0)?TRUE: FALSE,
                                        'href'     => $this->url->link('extension/maza/menu', 'user_token=' . $this->session->data['user_token'] . $url, true),
                                        'children' => array()
                                );
                        }
                        
                        // Filter
                        if ($this->user->hasPermission('access', 'extension/maza/filter')) {
                                $data['menus'][] = array(
                                        'id'       => 'mz-menu-filter',
                                        'icon'     => 'fa-filter',
                                        'name'     => $this->language->get('text_filter'),
                                        'active'   => (strpos($this->request->get['route'], 'extension/maza/filter') === 0)?TRUE: FALSE,
                                        'href'     => $this->url->link('extension/maza/filter', 'user_token=' . $this->session->data['user_token'] . $url, true),
                                        'children' => array()
                                );
                        }
                        
                        // Newsletter
                        if ($this->user->hasPermission('access', 'extension/maza/newsletter') && $this->model_extension_maza_extension->hasInstalled('module', 'mz_newsletter')) {
                                $data['menus'][] = array(
                                        'id'       => 'mz-menu-newsletter',
                                        'icon'     => 'fa-envelope',
                                        'name'     => $this->language->get('text_newsletter'),
                                        'active'   => (strpos($this->request->get['route'], 'extension/maza/newsletter') === 0)?TRUE: FALSE,
                                        'href'     => $this->url->link('extension/maza/newsletter/mail', 'user_token=' . $this->session->data['user_token'] . $url, true),
                                        'children' => array()
                                );
                        }
                        
                        // Testimonial
                        if ($this->user->hasPermission('access', 'extension/maza/testimonial')) {
                                $data['menus'][] = array(
                                        'id'       => 'mz-menu-testimonial',
                                        'icon'     => 'fa-smile-o',
                                        'name'     => $this->language->get('text_testimonial'),
                                        'active'   => (strpos($this->request->get['route'], 'extension/maza/testimonial') === 0)?TRUE: FALSE,
                                        'href'     => $this->url->link('extension/maza/testimonial', 'user_token=' . $this->session->data['user_token'] . $url, true),
                                        'children' => array()
                                );
                        }

                        // Form
                        if ($this->user->hasPermission('access', 'extension/maza/form')) {
                                $data['menus'][] = array(
                                        'id'       => 'mz-menu-form',
                                        'icon'     => 'fa-wpforms',
                                        'name'     => $this->language->get('text_form'),
                                        'active'   => (strpos($this->request->get['route'], 'extension/maza/form') === 0)?TRUE: FALSE,
                                        'href'     => $this->url->link('extension/maza/form', 'user_token=' . $this->session->data['user_token'] . $url, true),
                                        'children' => array()
                                );
                        }

                        // Gallery
                        if ($this->user->hasPermission('access', 'extension/maza/gallery')) {
                                $data['menus'][] = array(
                                        'id'       => 'mz-menu-gallery',
                                        'icon'     => 'fa-image',
                                        'name'     => $this->language->get('text_gallery'),
                                        'active'   => (strpos($this->request->get['route'], 'extension/maza/gallery') === 0)?TRUE: FALSE,
                                        'href'     => $this->url->link('extension/maza/gallery', 'user_token=' . $this->session->data['user_token'] . $url, true),
                                        'children' => array()
                                );
                        }

                        // Extra
                        if ($this->user->hasPermission('access', 'extension/maza/catalog')) {
                                $data['menus'][] = array(
                                        'id'       => 'mz-menu-extra',
                                        'icon'     => 'fa-tags',
                                        'name'     => $this->language->get('text_extra'),
                                        'active'   => (strpos($this->request->get['route'], 'extension/maza/catalog/') === 0)?TRUE: FALSE,
                                        'href'     => $this->url->link('extension/maza/catalog/product', 'user_token=' . $this->session->data['user_token'] . $url, true),
                                        'children' => array()
                                );
                        }
                        
                        // Blog
                        if ($this->user->hasPermission('access', 'extension/maza/blog')) {
                                $data['menus'][] = array(
                                        'id'       => 'mz-menu-blog',
                                        'icon'     => 'fa-newspaper-o',
                                        'name'     => $this->language->get('text_blog'),
                                        'active'   => (strpos($this->request->get['route'], 'extension/maza/blog') === 0)?TRUE: FALSE,
                                        'href'     => $this->url->link('extension/maza/blog', 'user_token=' . $this->session->data['user_token'] . $url, true),
                                        'children' => array()
                                );
                        }

                        // Code
                        if ($this->user->hasPermission('access', 'extension/maza/code')) {
                                $data['menus'][] = array(
                                        'id'       => 'mz-menu-code',
                                        'icon'     => 'fa-code',
                                        'name'     => $this->language->get('text_code'),
                                        'active'   => ($this->request->get['route'] === 'extension/maza/code')?TRUE: FALSE,
                                        'href'     => $this->url->link('extension/maza/code', 'user_token=' . $this->session->data['user_token'] . $url, true),
                                        'children' => array()
                                );
                        }
                        
                        // System
                        if ($this->user->hasPermission('access', 'extension/maza/system')) {
                                $data['menus'][] = array(
                                        'id'       => 'mz-menu-system',
                                        'icon'     => 'fa-cog',
                                        'name'     => $this->language->get('text_system'),
                                        'active'   => (strpos($this->request->get['route'], 'extension/maza/system') === 0)?TRUE: FALSE,
                                        'href'     => $this->url->link('extension/maza/system', 'user_token=' . $this->session->data['user_token'] . $url, true),
                                        'children' => array()
                                );
                        }
                        
                        $url_data = $this->request->get;
			unset($url_data['_route_']);unset($url_data['route']);
			if ($url_data) {
                            $redirect = $this->url->link($this->request->get['route'], urldecode(http_build_query($url_data, '', '&')), $this->request->server['HTTPS']);
                        } else {
                            $redirect = '';
                        }
			
                        $data['clear'] = $this->url->link('extension/maza/common/clear', 'user_token=' . $this->session->data['user_token'] . $url . '&redirect=' . urlencode($redirect), true);
                        $data['mazatheme_version'] = $this->mz_theme_config->get('version');
			
			return $this->load->view('extension/maza/common/column_left', $data);
		}
	}
        
        public function module($code) {
                if (isset($this->request->get['user_token']) && isset($this->session->data['user_token']) && ($this->request->get['user_token'] == $this->session->data['user_token'])) {
                        $this->load->language('extension/maza/common/column_left');
                        $this->load->model('tool/image');
                        $this->load->model('extension/maza/opencart');
                        
                        $url = '';
                
                        if(isset($this->request->get['mz_theme_code'])){
                            $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                        }

                        if(isset($this->request->get['mz_skin_id'])){
                            $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                        }
                        
                        // logo
                        // $data['maza_logo'] = $this->model_tool_image->resize('catalog/opencart-logo.png', 130, 50);

                        $data['home'] = $this->url->link('extension/maza/skin', 'user_token=' . $this->session->data['user_token'] . $url, true);
			
			// Menu
			$data['menus'] = array();
                        
                        $data['menus'][] = array(
                                'id'       => 'mz-menu-add',
                                'icon'     => 'fa-plus-circle',
                                'name'     => $this->language->get('text_add_module'),
                                'active'   => !isset($this->request->get['module_id'])?TRUE: FALSE,
                                'href'     => $this->url->link('extension/module/' . $code, 'user_token=' . $this->session->data['user_token'] . $url, true),
                                'children' => array()
                        );
                        
                        // module list
                        $modules = $this->model_extension_maza_opencart->getModulesByCode($code);
                        foreach ($modules as $module) {
                            $data['menus'][] = array(
                                    'id'       => 'mz-menu-edit-' . $module['module_id'],
                                    'icon'     => 'fa-pencil',
                                    'name'     => $module['name'],
                                    'active'   => (isset($this->request->get['module_id']) && $this->request->get['module_id'] === $module['module_id'])?TRUE: FALSE,
                                    'href'     => $this->url->link('extension/module/' . $code, 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module['module_id'] . $url, true),
                                    'children' => array()
                            );
                        }
                        
                        $url_data = $this->request->get;
			unset($url_data['_route_']);unset($url_data['route']);
			if ($url_data) {
                            $redirect = $this->url->link($this->request->get['route'], urldecode(http_build_query($url_data, '', '&')), $this->request->server['HTTPS']);
                        } else {
                            $redirect = '';
                        }
			
                        $data['clear'] = $this->url->link('extension/maza/common/clear', 'user_token=' . $this->session->data['user_token'] . $url . '&redirect=' . urlencode($redirect), true);
                        $data['mazatheme_version'] = $this->mz_theme_config->get('version');
                        
                        return $this->load->view('extension/maza/common/column_left', $data);
                }
                
                
        }
}