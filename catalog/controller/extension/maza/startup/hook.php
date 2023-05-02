<?php
class ControllerExtensionMazaStartupHook extends Controller {
	public function index(): Action {
        if ($this->config->has('mz_action_hook')) {
            foreach ($this->config->get('mz_action_hook') as $trigger => $value) {
                foreach ($value as $action) {
                    $this->mz_hook->register($trigger, new Action($action));
                }
            }
        }

        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mz_hook` WHERE status = 1");
        
        foreach($query->rows as $hook){
            $this->mz_hook->register($hook['trigger'], new Action($hook['action']));
        }
        
        return new Action('extension/maza/startup/asset');
	}
}
