<?php
class ControllerAffiliateHome extends Controller
{
    private $error = array();

    public function index()
    {

        $this->load->language('affiliate/home');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('account/customer');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && isset($this->request->post['email']) && isset($this->request->post['password']) && $this->validate()) {
            // Added strpos check to pass McAfee PCI compliance test (http://forum.opencart.com/viewtopic.php?f=10&t=12043&p=151494#p151295)
            if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], $this->config->get('config_url')) !== false || strpos($this->request->post['redirect'], $this->config->get('config_ssl')) !== false)) {
                $this->response->redirect(str_replace('&amp;', '&', $this->request->post['redirect']));
            } else {
                $this->response->redirect($this->url->link('account/account', '', true));
            }
        }


        $data['text_description'] = sprintf($this->language->get('text_description'), $this->config->get('config_name'), $this->config->get('config_name'), $this->config->get('config_affiliate_commission') . '%');


        $data['login'] = $this->url->link('affiliate/login', '', true);
        $data['register'] = $this->url->link('affiliate/register', '', true);
        $data['faq'] = $this->url->link('affiliate/faqs', '', true);

        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('affiliate/home', $data));
    }

    protected function validate()
    {
        // Check how many login attempts have been made.
        $login_info = $this->model_account_customer->getLoginAttempts($this->request->post['email']);

        if ($login_info && ($login_info['total'] >= $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {
            $this->error['warning'] = $this->language->get('error_attempts');
        }

        // Check if customer has been approved.
        $customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);

        if ($customer_info && !$customer_info['status']) {
            $this->error['warning'] = $this->language->get('error_approved');
        }

        if (!$this->error) {
            if (!$this->customer->login($this->request->post['email'], $this->request->post['password'])) {
                $this->error['warning'] = $this->language->get('error_login');

                $this->model_account_customer->addLoginAttempt($this->request->post['email']);
            } else {
                $this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
            }
        }

        return !$this->error;
    }
}
