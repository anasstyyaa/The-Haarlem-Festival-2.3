<?php 

namespace App\ViewModels;

class CustomerViewModel {
    public string $fullName;
    public string $email;
    public string $phone;
    public string $invoiceNumber;
    public string $invoiceDate;

    public function __construct(object $user, string $orderId) {
        $this->fullName = $user->getFullName();
        $this->email = $user->getEmail();
        $this->phone = $user->getPhoneNumber() ?? '';
        $this->invoiceNumber = "INV-" . $orderId;
        $this->invoiceDate = date('d F Y');
    }
}