<?php

namespace App\Controller;

use App\Model\RegisterManager;

class RegisterController extends AbstractController
{
    public function register(): string
    {
        if ($this->user) {
            header('Location: /user');
            exit();
        }

        $errors = [];
        $success = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Clean $_POST
            $data = array_map('trim', $_POST);

            $password1 = $data['password1'];
            $password2 = $data['password2'];

            $this->validatePassword($password1, $password2, $errors);


            // Check if form is filled
            if (!isset($data['username']) || empty($data['username'])) {
                $errors[] = 'Please fill in username field.';
            }

            if (empty($errors)) {
                $username = $data['username'];
                $password = $data['password1'];

                $addUser = new RegisterManager();
                $addUser->insert($username, $password);
                $success[] = 'Registration successfull. Please login.';
            }
            return $this->twig->render('Register/register.html.twig', ['errors' => $errors, 'success' => $success]);
        }
        return $this->twig->render('Register/register.html.twig');
    }

    public function validatePassword(string $password1 = null, string $password2 = null, array &$errors): void
    {
        // is there a photo ?
        if (!isset($password1) || empty($password1)) {
            $errors[] = 'Please fill in password field.';
        }
        if (!isset($password2) || empty($password2)) {
            $errors[] = 'Please retype your password field.';
        }
        if ($password1 !== $password2) {
            $errors[] = 'Passwords are not the same.';
        }
    }
}