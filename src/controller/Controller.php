<?php
class Controller
{
	protected $ci;

	public function __construct(\Slim\Container $ci)
	{
		global $twig;
		
		$this->twig = $twig;
		$this->slim = $ci;
		$this->router = $this->slim->get('router');
	}

    public function require_login($message = true)
    {
        global $auth;

		$n = base64_encode($this->slim->get('request')->getUri()->getPath());

        if (!$auth->isLoggedIn()) {
			if ($message)
				Message::add('info', 'Merci de vous connecter pour acceder à cette page.');
            $this->redirect($this->router->pathFor('login').'?n='.trim($n, '='));
        }
    }

    public function redirect($url)
    {
		global $config;
        header('Location: '.rtrim($config['url'], '/').$url);
		header('Status: 301 Moved Permanently', false, 301);
		die;
    }
}