<?php

namespace App\Controllers;

use App\Services\Interfaces\IPaymentService;
use App\Framework\Controller;

use Exception;
use InvalidArgumentException;
use App\Exceptions\OrderException;

class PaymentController extends Controller
{
    private IPaymentService $paymentService;

    public function __construct(
        IPaymentService $paymentService,
    ) {
        $this->paymentService = $paymentService;
    }

    public function checkout(): void
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
        }

        try {
            $program = $_SESSION['program'] ?? null;
            if (!$program || count($program->getTickets()) === 0) {
                $this->redirect('/personalProgram', 'Your cart is empty.', 'error');
            }

            $stripeUrl = $this->paymentService->prepareCheckout($program, $this->getCurrentUser()['id']);
            $this->redirect($stripeUrl);
        } catch (InvalidArgumentException $e) {
            $this->redirect('/personalProgram', $e->getMessage(), 'error');
        } catch (Exception $e) {
            $this->redirect('/personalProgram', 'We encountered a problem initiating checkout. Please try again.', 'error');
        }
    }

    public function repay(): void
    {
        $this->isLoggedIn() ?: $this->redirect('/login');
        $orderId = $_GET['orderId'] ?? '';

       
        try {
            if ($this->paymentService->isOrderExpired($orderId)) {
                $this->paymentService->releaseExpiredOrder($orderId);
                $this->redirect('/personalProgram', 'Your 24-hour payment window has expired.', 'error');
            }

            $stripeUrl = $this->paymentService->prepareRepayCheckout($orderId);
            $this->redirect($stripeUrl);
        } catch (Exception $e) {
            $this->redirect('/personalProgram', 'Order recovery failed: ' . $e->getMessage(), 'error');
        }
    }

    public function paymentSuccess(): void
    {
        $orderId = $_GET['orderId'] ?? null;
        $stripeSessionId = $_GET['session_id'] ?? 'unknown';

        try {
            if (!$orderId) {
                $this->redirect('/', 'Invalid order reference.', 'error');
                return; // Stop execution after redirect
            }
            $this->paymentService->completePurchase($orderId, $stripeSessionId, $this->getCurrentUser()['id']);
            unset($_SESSION['program']);
            $this->view('payment/success');
            
        } catch (OrderException $e) {
            $this->redirect('/personalProgram', $e->getMessage(), 'error');
        } catch (Exception $e) {
            error_log($e->getMessage());
            $this->view('payment/failed', ['error' => 'An unexpected error occurred. Please contact support.']);
        }
    }

    public function paymentFailed(): void
    {
        $orderId = $_GET['orderId'] ?? null;

        try {
            if ($orderId && $this->isLoggedIn()) {
                $this->paymentService->handleFailedPayment($orderId, $this->getCurrentUser()['id']);
            }
        } catch (Exception $e) {
            error_log("Failed to process payment failure logic for Order {$orderId}: " . $e->getMessage());
        }

        $this->view('payment/failed');
    }
}