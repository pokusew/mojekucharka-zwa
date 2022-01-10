<?php

declare(strict_types=1);

namespace App\Presenter;

use App\Repository\UsersRepository;
use App\Service\UsersService;
use Core\Forms\Controls\TextInput;
use Core\Forms\Form;

class ResetPasswordPresenter extends BasePresenter
{

	/** @inject */
	public UsersRepository $usersRepository;

	/** @inject */
	public UsersService $usersService;

	protected string $key;

	/**
	 * @var array<string, mixed>|null
	 */
	protected ?array $user = null;

	protected Form $resetPasswordForm;

	public function action(string $key): void
	{
		$this->view = 'resetPassword';

		$this->key = $key;

		// we need the username for the hidden username field (see the template)
		$this->user = $this->usersRepository->findOneByValidPasswordResetKey($key, ['id', 'email', 'username']);

		if ($this->user !== null) {

			$this->resetPasswordForm = $this->createResetPasswordForm();

			$this->resetPasswordForm->process($this->httpRequest);

		}
	}

	private function createResetPasswordForm(): Form
	{
		$form = new Form('resetPassword');

		$form->setAction($this->link('this'));

		$this->addNewPasswordWithConfirmationToForm($form);

		/** @var TextInput $newPassword */
		$newPassword = $form['password'];
		$newPassword->setLabelText('Nové heslo');
		$newPassword->setPlaceholder('Nové heslo');

		/** @var TextInput $newPasswordAgain */
		$newPasswordAgain = $form['passwordAgain'];
		$newPasswordAgain->setLabelText('Nové heslo znovu pro kontrolu');
		$newPasswordAgain->setPlaceholder('Nové heslo znovu pro kontrolu');

		$form->addSubmit('submit', 'Změnit heslo');

		$form->onSuccess[] = function (Form $form) {
			$this->handleResetPasswordFormSuccess($form);
		};

		return $form;
	}

	private function handleResetPasswordFormSuccess(Form $form): void
	{
		/** @var TextInput $newPassword */
		$newPassword = $form['password'];

		if (
			!$this->usersService->resetUserPassword(
				$this->user['email'],
				$this->key,
				$newPassword->getValue(),
				$this->httpRequest->remoteAddress,
			)
		) {
			$form->setGlobalError('Heslo se nepodařilo změnit. Pravděpodobně vypršela platnost odkazu.');
			return;
		}

		$this->redirect('SignIn:');
	}

}
