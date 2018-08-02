<?php
class AdminCtrl extends Controller
{
    public function login($request, $response, $args)
	{
		$post = $request->getParsedBody();
		$get = $request->getQueryParams();

		if ($post) {
			if ($post['remeberme'] == 'on')
				$rememberDuration = (int) (60 * 60 * 24 * 2);
			else
				$rememberDuration = null;

			if (Admin::Login($post['login'], $post['password'], $rememberDuration)) {
				if ($get['n'])
					$this->redirect(base64_decode($get['n']));
				else
					$this->redirect('/');
			}
			else
				Message::add('error', 'Erreur d\'authentification.');
		}

		echo $this->twig->render('admin/login.twig', ['post' => $post, 'get' => $get]);
	}

    public function logout($request, $response, $args)
	{
		Admin::Logout();
		Message::add('success', 'Vous avez bien été déconnecté.');
		$this->redirect('/');
	}
}