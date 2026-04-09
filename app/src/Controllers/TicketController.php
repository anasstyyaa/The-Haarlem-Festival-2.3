<?php

namespace App\Controllers;

use App\Models\PersonalProgram;
use App\Repositories\TicketRepository;
use App\Services\Interfaces\ITicketService;
use App\ViewModels\TicketViewModel;
use App\Exceptions\CapacityException;
use App\Framework\Controller;

class TicketController extends Controller
{
    private TicketRepository $ticketRepository;
    private ITicketService $ticketService;

    public function __construct(
        TicketRepository $ticketRepository,
        ITicketService $ticketService
    ) {
        $this->ticketRepository = $ticketRepository;
        $this->ticketService = $ticketService;
    }

    public function index(): void
    {
        try {
            $program = $_SESSION['program'] ?? new PersonalProgram();
            $rawTickets = $this->ticketService->hydrateTickets($program->getTickets());

            $viewTickets = [];
            $grandTotal = 0.0;

            foreach ($rawTickets as $ticket) {
                $vm = new TicketViewModel($ticket);
                $viewTickets[] = $vm;
                $grandTotal += $vm->totalPrice;
            }

            $this->render('personalProgram/personalProgram', [
                'viewTickets' => $viewTickets,
                'grandTotal'  => $grandTotal
            ]);

        } catch (\Throwable $e) {
            error_log($e->getMessage());
            http_response_code(500);
            echo "Error loading personal program.";
        }
    }

    public function addTicket(): void
    {
        try {
            $this->ticketService->addToProgram($_POST, $_SESSION['user']['id'] ?? null);
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/', "Ticket added to your program!");

        } catch (CapacityException $e) {
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/', $e->getMessage(), 'error');

        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/', "Something went wrong.", 'error');
        }
    }

    public function removeTicket(): void
    {
        try {
            $ticketId = (int)($_POST['ticket_id'] ?? 0);

            if ($ticketId <= 0) {
                throw new \Exception("Invalid ticket ID");
            }

            $program = $_SESSION['program'] ?? new PersonalProgram();
            $program->removeTicket($ticketId);

            $_SESSION['program'] = $program;

            $this->redirect('/personalProgram', "Item removed from program.");

        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->redirect('/personalProgram', "Error removing item.", 'error');
        }
    }

    public function updateQuantity()
    {
        try{
        $itemId = $_POST['program_item_id'] ?? null;
        $action = $_POST['action'] ?? null;

            $this->ticketService->updateProgramQuantity($itemId, $action);

            $this->redirect('/personalProgram');

        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->redirect('/personalProgram', "Error updating quantity.", 'error');
        }
}

    public function scan(): void
    {

        try {
            $this->requireEmployee();

            $token = $_GET['token'] ?? '';
            error_log('SCAN TOKEN: [' . $token . ']');
            $result = $this->ticketService->scanTicket($token);

            $this->view('employee/scanResult', $result);
        } catch (\InvalidArgumentException $e) {
            $this->view('employee/scanResult', [
                'status' => 'error',
                'message' => $e->getMessage(),
                'ticket' => null
            ]);
        } catch (\Exception $e) {
            error_log('Scan error: ' . $e->getMessage());

            $this->view('employee/scanResult', [
                'status' => 'error',
                'message' => 'Something went wrong.',
                'ticket' => null
            ]);
        }
    }


    public function scanPage(): void
    {
        try {
            $this->requireEmployee();
            $this->view('employee/scan');
        } catch (\Exception $e) {
            error_log('Scan page error: ' . $e->getMessage());
            echo $e->getMessage();
        }
    }

  public function adminIndex(): void
    {
         $this->requireAdmin();
        try {
            $this->requireAdmin();

            $page = (int)($_GET['page'] ?? 1);
            $data = $this->ticketService->getPaginatedTickets($page);

            $this->render('admin/dashboard', [
                'tickets' => $data['tickets'],
                'totalPages' => $data['total_pages'],
                'currentPage' => $data['current_page'],
                'totalResults' => $data['total_results']
            ]);

        } catch (\Throwable $e) {
            error_log($e->getMessage());
            http_response_code(500);
            echo "Error loading admin dashboard.";
        }
    }

   public function exportCsv(): void
{
    try {
        $data = $this->ticketService->getExportData($_POST['columns'] ?? []);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="tickets.csv"');

        $output = fopen('php://output', 'w');

        fputcsv($output, $data['columns']);

        foreach ($data['rows'] as $row) {
            fputcsv(
                $output,
                array_map(fn($col) => $row[$col] ?? '', $data['columns'])
            );
        }

        fclose($output);
        exit;

    } catch (\Throwable $e) {
        error_log($e->getMessage());
        header("Location: /admin");
        exit;
    }
}
   public function exportExcel(): void
{
    try {
        $data = $this->ticketService->getExportData($_POST['columns'] ?? []);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="tickets.xls"');

        $output = fopen('php://output', 'w');

        fputcsv($output, $data['columns']);

        foreach ($data['rows'] as $row) {
            fputcsv(
                $output,
                array_map(fn($col) => $row[$col] ?? '', $data['columns'])
            );
        }

        fclose($output);
        exit;

    } catch (\Throwable $e) {
        error_log($e->getMessage());
        header("Location: /admin");
        exit;
    }
}
} 