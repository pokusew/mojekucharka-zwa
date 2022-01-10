<?php

declare(strict_types=1);

namespace App\Presenter;

use App\Limits;
use App\Service\UserChangePasswordException;
use App\Service\UsersService;
use Core\Forms\Controls\TextInput;
use Core\Forms\Form;

class SettingsPresenter extends BasePresenter
{

	/** @inject */
	public UsersService $usersService;

	protected Form $signOutForm;

	protected Form $changePasswordForm;

	public function __construct()
	{
		$this->view = 'settings';
	}

	public function action(): void
	{
		$this->ensureUserLoggedIn();

		$this->signOutForm = $this->createSignOutForm();
	}

	public function actionChangePassword(): void
	{
		$this->view = 'settings-changePassword';

		$this->ensureUserLoggedIn();

		$this->changePasswordForm = $this->createChangePasswordForm();

		$this->changePasswordForm->process($this->httpRequest);
	}

	private function createChangePasswordForm(): Form
	{
		$form = new Form('changePassword');

		$form->setAction($this->link('this'));

		$form->addText('currentPassword', 'Stávající heslo')
			->setType(TextInput::TYPE_PASSWORD)
			->setAutocomplete('current-password')
			->setPlaceholder('Stávající heslo')
			->setRequired()
			->setMaxLength(Limits::PASSWORD_MAX_LENGTH);

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
			$this->handleChangePasswordFormSuccess($form);
		};

		return $form;
	}

	private function handleChangePasswordFormSuccess(Form $form): void
	{
		/** @var TextInput $currentPassword */
		$currentPassword = $form['currentPassword'];
		/** @var TextInput $newPassword */
		$newPassword = $form['password'];

		try {
			$this->usersService->changeUserPassword(
				$this->getUser()->getId(),
				$currentPassword->getValue(),
				$newPassword->getValue(),
				$this->httpRequest->remoteAddress,
			);
		} catch (UserChangePasswordException $e) {

			if ($e->getCode() === UserChangePasswordException::INVALID_CURRENT_PASSWORD) {
				$currentPassword->setError('Špatné heslo.');
				return;
			}

			// should never happen, but if it did, the app error handler will handle it
			throw $e;

		}

		$this->redirect('Settings:');
	}

	public function actionEditProfile(): void
	{
		$this->view = 'settings-editProfile';

		$this->ensureUserLoggedIn();
	}

}
