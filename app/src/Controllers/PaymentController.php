<?php

namespace App\Controllers;

use App\Services\Interfaces\IPaymentService;
use App\Services\Interfaces\ICommunicationService;
use App\Services\Interfaces\IUserService;
use App\Services\Interfaces\ITextService;
use App\Framework\Controller;
use App\Config\AppConfig;
use App\Services\Interfaces\ITicketService;
use App\ViewModels\CustomerViewModel;
use App\ViewModels\TicketViewModel;

class PaymentController extends Controller
{
    private IPaymentService $paymentService;
    private ICommunicationService $communicationService;
    private IUserService $userService;
    private ITicketService $ticketService; 

    public function __construct(
        IPaymentService $paymentService,
        ICommunicationService $communicationService,
        IUserService $userService, 
        ITicketService $ticketService
    ) {
        $this->paymentService = $paymentService;
        $this->communicationService = $communicationService;
        $this->userService = $userService;
        $this->ticketService = $ticketService;
    }

    public function checkout(): void
    {
        $this->isLoggedIn() ?: $this->redirect('/login');

        $program = $_SESSION['program'] ?? null;
        if (!$program || count($program->getTickets()) === 0) {
            $this->redirect('/personalProgram');
        }

        $tempOrderId = $this->paymentService->createPendingOrder($program, $this->getCurrentUser()['id']);
        $ticketViewModels = array_map(fn($t) => new TicketViewModel($t), $program->getTickets());  
        $this->redirectToStripe($ticketViewModels, $tempOrderId);
    }

    public function repay(): void
    {
        $this->isLoggedIn() ?: $this->redirect('/login');
        $orderId = $_GET['orderId'] ?? '';

        // cheecking if the 24-hour window has closed
        if ($this->paymentService->isOrderExpired($orderId)) {
            $this->paymentService->releaseExpiredOrder($orderId); // if closed: return items to stock and mark as expired in DB
            
            $_SESSION['flash_error'] = "Your 24-hour payment window has expired. The items have been released.";
            $this->redirect('/personalProgram');
        }

        // if window is still open: get the tickets and send back to Stripe
        $tickets = $this->paymentService->getTicketsByOrderId($orderId); 
        $ticketViewModels = array_map(fn($t) => new TicketViewModel($t), $tickets);
        $this->redirectToStripe($ticketViewModels, $orderId);
    }

    public function paymentSuccess(): void
    {
        $tempOrderId = $_GET['orderId'] ?? null;
        $stripeSessionId = $_GET['session_id'] ?? 'unknown';

        if ($tempOrderId) {
            $tickets = $this->paymentService->finalizeOrder($tempOrderId, $stripeSessionId);

            if (!empty($tickets)) {
               
                $tickets = $this->ticketService->hydrateTickets($tickets);
                $userModel = $this->userService->getUserById($_SESSION['user']['id']);
                $customer = new CustomerViewModel($userModel, $tempOrderId);
                $ticketViewModels = array_map(fn($t) => new TicketViewModel($t), $tickets);
                $this->communicationService->sendOrderConfirmation($customer, $ticketViewModels, $tempOrderId);
            }

            unset($_SESSION['program']);
            require __DIR__ . '/../Views/payment/success.php';
        }
    }

    public function paymentFailed(): void
    {
        $tempOrderId = $_GET['orderId'] ?? null;
        if ($tempOrderId && $this->isLoggedIn()) {
            $user = $this->userService->getUserById($this->getCurrentUser()['id']);
            $this->communicationService->sendPaymentReminder(['email' => $user->getEmail(), 'full_name' => $user->getFullName()], $tempOrderId);
        }
        require __DIR__ . '/../Views/payment/failed.php';
    }

    private function redirectToStripe(array $ticketViewModels, string $orderId): void 
    {
        $apiKey = getenv('STRIPE_SECRET_KEY');
        $baseUrl = AppConfig::getBaseUrl();

        $data = [
            'success_url' => "{$baseUrl}/payment-success?orderId=$orderId&session_id={CHECKOUT_SESSION_ID}",
            'cancel_url' => "{$baseUrl}/payment-failed?orderId=$orderId",
            'mode' => 'payment',
            'payment_method_types[0]' => 'card',
            'payment_method_types[1]' => 'ideal',
        ];

        foreach ($ticketViewModels as $i => $vm) {
            $data["line_items[$i][price_data][currency]"] = 'eur';
            $data["line_items[$i][price_data][unit_amount]"] = (int)($vm->unitPrice * 100);
            $data["line_items[$i][price_data][product_data][name]"] = $vm->title; 
            $data["line_items[$i][quantity]"] = $vm->guestCount;
        }

        $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ':');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if (isset($response['url'])) {
            $this->redirect($response['url']);
        } else {
            die("Stripe Error: " . ($response['error']['message'] ?? 'Unknown error'));
        }
    }

    private function getDisplayName($event): string {
        $details = $event->getDetails();
        
        if (is_array($details)) {
        if (isset($details['artist']) && $details['artist']) {
            return $details['artist']->getName();
        }

        if (isset($details['name'])) {
            return $details['name'];
        }
        }

        if (is_object($details) && method_exists($details, 'getTitle')) {
            return $details->getTitle();
        }

        if (is_object($details) && method_exists($details, 'getName')) {
            return $details->getName();
        }

        return "Festival Ticket";
    }
}