<?php
class ControllerExtensionMazaEventCheckoutCheckout extends Controller {
	public function controllerBefore(&$route): void {
		if ($this->mz_skin_config->get('catalog_checkout_status')) {
			$route = 'extension/maza/checkout';

			// Fixed to reach asset event
			$this->event->register('controller/extension/maza/checkout/after', new Action('extension/maza/event/asset'));
		}
	}
}
