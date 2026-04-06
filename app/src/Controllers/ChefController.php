<?php 

namespace App\Controllers;

use App\Framework\Controller;
use Exception;
use App\Services\Interfaces\Yummy\IChefService;

class ChefController extends Controller {
    private IChefService $service;

    public function __construct(IChefService $service) {
        $this->service = $service;
    }

    public function index(): void {
        $this->render('admin/yummy/index', [
            'chefs' => $this->service->getAllChefs()
        ]);
    }

    public function showCreateForm(): void {
        $this->requireAdmin();
        $this->render('admin/yummy/createChef');
    }

    public function store(): void {
        $this->requireAdmin();

        try {
            $this->service->processCreateChef($_POST, $_FILES['image_file'] ?? null);
            $this->redirect('/admin/yummy', 'Chef created successfully!');
        } catch (Exception $e) {
            // serivce validation or upload errors end up here
            $this->redirect('/admin/chefs/create', $e->getMessage(), 'error');
        }
    }

    public function showEditForm(array $vars): void {
        $this->requireAdmin();
        $id = (int)$vars['id'];
        $chef = $this->service->getChefById($id);

        if (!$chef) {
            $this->redirect('/admin/yummy', 'Chef not found.', 'error');
        }

        $this->render('admin/yummy/editChef', [
            'chef' => $chef
        ]);
    }

    public function update(array $vars): void {
        $this->requireAdmin();
        $id = (int)$vars['id'];

        try {
            $this->service->processUpdateChef($id, $_POST, $_FILES['image_file'] ?? null);
            $this->redirect('/admin/yummy', 'Chef updated successfully!');
        } catch (Exception $e) {
            $this->redirect("/admin/yummy/editChef/$id", $e->getMessage(), 'error');
        }
    }

    public function delete(array $vars): void {
        $this->requireAdmin();
        $id = (int)$vars['id'];

        try {
            if ($this->service->deleteChef($id)) {
                $this->redirect('/admin/yummy', 'Chef deleted successfully.');
            }
            throw new Exception("The chef could not be deleted.");
        } catch (Exception $e) {
            $this->redirect('/admin/yummy', $e->getMessage(), 'error');
        }
    }
}