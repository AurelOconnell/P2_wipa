<?php

namespace App\Controller;

use App\Model\PhotoManager;

class PhotoController extends AbstractController
{
    /**
     * List photos
     */
    public function index(): string
    {
        $photoManager = new PhotoManager();
        $photos = $photoManager->selectAll('photo_title', 'ASC');

        return $this->twig->render('Photo/index.html.twig', ['photos' => $photos]);
    }

    /**
     * Show informations for a specific photo
     */
    public function show(int $id): string
    {
        $photoManager = new PhotoManager();
        $photo = $photoManager->selectOneById($id);

        return $this->twig->render('Photo/show.html.twig', ['photo' => $photo]);
    }

    /**
     * Edit a specific photo
     */
    public function edit(int $id): ?string
    {
        $photoManager = new PhotoManager();
        $photo = $photoManager->selectOneById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $photo = array_map('trim', $_POST);

            // TODO validations (length, format...)

            // if validation is ok, update and redirection
            $photoManager->update($photo);

            header('Location: /photos/show?id=' . $id);

            // we are redirecting so we don't want any content rendered
            return null;
        }

        return $this->twig->render('Photo/edit.html.twig', [
            'photo' => $photo,
        ]);
    }

    /**
     * Add a new photo
     */
    public function add(): ?string
    {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $photo = array_map('trim', $_POST);

            $this->validateURL($photo, $errors);

            if (!isset($photo['prompt']) || empty($photo['prompt'])) {
                $errors[] = 'You must write a prompt';
            }
            if (!isset($photo['description']) || empty($photo['description'])) {
                $errors[] = 'You must write a comment';
            }
            if (empty($errors)) {
                $photoManager = new PhotoManager();
                $id = $photoManager->insert($photo);

                header('Location:/photos/show?id=' . $id);
                return null;
            }
        }

        return $this->twig->render('Photo/add.html.twig', ['errors' => $errors]);
    }

    public function validateURL(array $photo, array &$errors): void
    {
        if (!isset($photo['picture']) || empty($photo['picture'])) {
            $errors[] = 'You must enter an URL';
        }
        if (!filter_var($photo['picture'], FILTER_VALIDATE_URL)) {
            $errors[] = 'Wrong URL format';
        }
    }

    /**
     * Delete a specific photo
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id']);
            $photoManager = new PhotoManager();
            $photoManager->delete((int)$id);

            header('Location:/photos');
        }
    }
}