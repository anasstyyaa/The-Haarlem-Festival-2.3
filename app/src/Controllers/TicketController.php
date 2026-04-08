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

    public function updateQuantity() {
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
    $this->requireEmployee();

    try {
        $token = $_GET['token'] ?? '';
        if ($token === '') {
            $status = 'error';
            $message = 'No ticket token provided.';
            $ticket = null;

            require __DIR__ . '/../Views/employee/scanResult.php';
            return;
        }
        $ticket = $this->ticketRepository->getByToken($token);

        if (!$ticket) {
            $status = 'error';
            $message = 'Invalid ticket.';
            $ticket = null;

            require __DIR__ . '/../Views/employee/scanResult.php';
            return;
        }

        if ($ticket['is_scanned'] == 1) {
            $status = 'warning';
            $message = 'This ticket has already been scanned.';

            require __DIR__ . '/../Views/employee/scanResult.php';
            return;
        }

        $this->ticketRepository->markAsScanned($token);

        $status = 'success';
        $message = 'Ticket is valid. Entry allowed.';

        require __DIR__ . '/../Views/employee/scanResult.php';

    } catch (\Exception $e) {
        error_log("Scan error: " . $e->getMessage());

        $status = 'error';
        $message = 'Something went wrong.';
        $ticket = null;

        require __DIR__ . '/../Views/employee/scanResult.php';
    }
}

    // private function requireEmployee(): void //checks if current user is employee
    // {
    //     try {
    //         if (!isset($_SESSION['user'])) {
    //             throw new \Exception("User not logged in.");
    //         }
    //         if (($_SESSION['user']['role'] ?? '') !== 'Employee') {
    //             throw new \Exception("User is not an employee.");
    //         }
    //     } catch (\Exception $e) {
    //         error_log("Access error: " . $e->getMessage());
    //         echo "Access denied. Employees only.";
    //         exit;
    //     }
    // }
    
    public function scanPage(): void
    {
        $this->requireEmployee();

        require __DIR__ . '/../Views/employee/scan.php';
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
