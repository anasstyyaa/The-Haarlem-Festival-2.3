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


    public function __construct(TicketRepository $ticketRepository,  ITicketService $ticketService)
    {
        $this->ticketRepository = $ticketRepository;
        $this->ticketService = $ticketService;
    }

    public function index(): void
    {
        $program = $_SESSION['program'] ?? new PersonalProgram();
        $rawTickets = $this->ticketService->hydrateTickets($program->getTickets());

        // transforming raw models into viewModels
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
    }

    public function addTicket(): void
    {
        try {
            $this->ticketService->addToProgram($_POST, $_SESSION['user']['id'] ?? null);
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/', "Ticket added to your program!");
        } catch (CapacityException $e) {
            $this->redirect($_SERVER['HTTP_REFERER'], $e->getMessage(), 'error');
        } catch (\Exception $e) {
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/', "Something went wrong.", 'error');
        }
    }


    public function removeTicket(): void
    {
        $ticketId = (int)($_POST['ticket_id'] ?? 0);

        if ($ticketId <= 0) {
            $this->redirect('/personalProgram', "Invalid item selected.", 'error');
        }

        $program = $_SESSION['program'] ?? new PersonalProgram();
        $program->removeTicket($ticketId);

        $_SESSION['program'] = $program;
        $this->redirect('/personalProgram', "Item removed from program.");
    }

    public function updateQuantity()
    {
        $itemId = $_POST['program_item_id'] ?? null;
        $action = $_POST['action'] ?? null;

        if ($itemId && $action) {
            $this->ticketService->updateProgramQuantity($itemId, $action);
        }

        $this->redirect('/personalProgram');
    }

    /**
     * Simple ticket scan method for employees
     */
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


    public function adminIndex()
    {
        $tickets = $this->ticketRepository->getAllWithDetails();

        require __DIR__ . '/../Views/admin/dashboard.php';
    }
    public function exportCsv(): void
    {
        $tickets = $this->ticketRepository->getAllWithDetails();

        $selectedColumns = array_intersect(
            $_POST['columns'] ?? [],
            array_keys($tickets[0] ?? [])
        );
        if (empty($selectedColumns)) {
            header("Location: /admin");
            exit;
        }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="tickets.csv"');

        $output = fopen('php://output', 'w');

        fputcsv($output, $selectedColumns);

        foreach ($tickets as $row) {
            fputcsv(
                $output,
                array_map(fn($col) => $row[$col] ?? '', $selectedColumns)
            );
        }

        fclose($output);
        exit;
    }
    public function exportExcel(): void
    {
        $tickets = $this->ticketRepository->getAllWithDetails();

        $selectedColumns = array_intersect(
            $_POST['columns'] ?? [],
            array_keys($tickets[0] ?? [])
        );
        if (empty($selectedColumns)) {
            header("Location: /admin");
            exit;
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="tickets.xls"');

        $output = fopen('php://output', 'w');

        fputcsv($output, $selectedColumns);

        foreach ($tickets as $row) {
            fputcsv(
                $output,
                array_map(fn($col) => $row[$col] ?? '', $selectedColumns)
            );
        }

        fclose($output);
        exit;
    }
}
