<?php
class ControllerExtensionMazaStartupEvent extends Controller {
	public function index(): Action {
        if ($this->config->has('mz_action_event')) {
            foreach ($this->config->get('mz_action_event') as $key => $value) {
                foreach ($value as $priority => $action) {
                    $this->event->register($key, new Action($action), $priority);
                }
            }
        }
        
        // Route Controller Event
        // $this->event->register('controller/' . $this->mz_document->getRoute() . '/before', new Action('extension/maza/event/layout_builder'));
        $this->event->register('controller/' . $this->mz_document->getRoute() . '/after', new Action('extension/maza/event/asset'));

        // Language event for opencart 2
        if(version_compare(VERSION, '3.0.0.0') < 0){ // For opencart 2
            $event_language_controller = array(
                'extension/maza/*',
                'extension/mz_content/*',
                'extension/mz_design/*',
                'extension/mz_widget/*',
                'extension/module/mz_*',
                'product/*',
                'information/sitemap',
                'common/language',
                'common/currency',
                'common/menu',
            );
            foreach($event_language_controller as $trigger){
                $this->event->register('controller/' . $trigger . '/before', new Action('extension/maza/event/language/before'));
                $this->event->register('controller/' . $trigger . '/after', new Action('extension/maza/event/language/after'));
                $this->event->register('view/' . $trigger . '/before', new Action('extension/maza/event/language'));
            }
        }
        
        return new Action('extension/maza/startup/hook');
	}
}
