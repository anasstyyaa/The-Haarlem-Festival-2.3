<?php

namespace App\Services;

use App\Services\Interfaces\IPasswordResetService;
use App\Repositories\Interfaces\IUserRepository;
use App\Repositories\Interfaces\IPasswordResetRepository;
use App\Services\Mailer;

class PasswordResetService implements IPasswordResetService
{
    private IUserRepository $userRepository;
    private IPasswordResetRepository $passwordResetRepository;
    private Mailer $mailer;
    private string $appUrl;

    public function __construct(
        IUserRepository $userRepository,
        IPasswordResetRepository $passwordResetRepository,
        Mailer $mailer,
        string $appUrl
    ) {
        //save the helper
        $this->userRepository = $userRepository;
        $this->passwordResetRepository = $passwordResetRepository;
        $this->mailer = $mailer;
        $this->appUrl = $appUrl;
    }

 public function sendResetLink(string $email): void
{
    // checks user existence by email
    $user = $this->userRepository->findByEmail($email);

    // Stops when the user is not found
    if ($user === null) {
        return;
    }

    // Random secret code generated
    $token = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $token); // secure hashed token is saved instead of the real token

    $expiresAt = date('Y-m-d H:i:s', strtotime('+30 minutes'));

    // Saves the token in the database
    $this->passwordResetRepository->create((int)$user['Id'], $tokenHash, $expiresAt);

    // The reset link is built using the app URL
    $link = $this->appUrl . '/resetPassword?token=' . $token;

    // messages
    $subject = 'Reset your password';
    $body = 'Click this link to reset your password: ' . $link;

    // Mailer sends email to the user
    $this->mailer->send($email, $subject, $body);
}


//Submitted by the user to reset password
public function resetPassword(string $token, string $newPassword): void
{
    try {
         


     //  minimum length
        if (strlen($newPassword) < 8) {
            throw new \Exception("Password must be at least 8 characters long.");
        }


        //  password must not be empty
        if (trim($newPassword) === '') {
            throw new \Exception("Password cannot be empty.");
        }

        

        //  token must not be empty
        if (trim($token) === '') {
            throw new \Exception("Reset token is missing.");
        }

       

        //  must contain uppercase
        if (!preg_match('/[A-Z]/', $newPassword)) {
            throw new \Exception("Password must contain at least one uppercase letter.");
        }

        //  must contain lowercase
        if (!preg_match('/[a-z]/', $newPassword)) {
            throw new \Exception("Password must contain at least one lowercase letter.");
        }

        // must contain number
        if (!preg_match('/[0-9]/', $newPassword)) {
            throw new \Exception("Password must contain at least one number.");
        }

        //Stored secure hashed token not the raw token 
        $tokenHash = hash('sha256', $token);

        //Helps to locate the valid token in the database 
        $resetRow = $this->passwordResetRepository->findValidToken($tokenHash);

        //If the reset token is invalid then stop
        if ($resetRow === null) {
            throw new \Exception("Invalid or expired reset token link.");
        }

        //This stores the new password as a secure hashed password not plain text
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        //Updates the new password in the users table
        $this->userRepository->updatePassword((int)$resetRow['user_id'], $hashedPassword);

        //Marks the reset request as used 
        $this->passwordResetRepository->markAsUsed((int)$resetRow['id']);

    } catch (\Exception $e) {

        // Pass error to controller
        throw $e;
    }
}  



}