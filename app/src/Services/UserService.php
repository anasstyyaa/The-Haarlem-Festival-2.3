<?php

namespace App\Services;

use App\Models\UserModel;
use App\Repositories\Interfaces\IUserRepository;
use App\Services\Interfaces\IUserService;
use App\Services\Interfaces\IAuthService;
use App\Services\Interfaces\ICommunicationService;
use Exception;
use InvalidArgumentException;


class UserService implements IUserService
{
    private IUserRepository $userRepository;
    private IAuthService $authService;
    private ICommunicationService $communicationService;

    public function __construct(IUserRepository $userRepository, IAuthService $authService, ICommunicationService $communicationService)
    {
        $this->userRepository = $userRepository;
        $this->authService = $authService;
        $this->communicationService = $communicationService;
    }

    public function getAllUsers(): array
    {
        return $this->userRepository->getAll();
    }

    public function getUserById(int $id): ?UserModel
    {
        return $this->userRepository->getById($id);
    }

    public function createUser(UserModel $user, string $password, ?array $file): void
    {
        $this->authService->validateUser($user, $password, true);

        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $imgName = $this->handleSecureUpload($file, 'user');

            if (!$imgName) {
                throw new InvalidArgumentException("Invalid image format. Only JPG, PNG, and WebP are allowed.");
            }

            $user->setProfilePicture('/assets/uploads/users/' . $imgName);
        }

        $user->setPassword(password_hash($password, PASSWORD_DEFAULT));

        if (!$this->userRepository->create($user)) {
            throw new Exception("Database error: Could not create user.");
        }
    }

    public function updateUser(UserModel $user, ?array $file = null): void
    {
        $this->authService->validateUser($user, '', false);

        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $imgName = $this->handleSecureUpload($file, 'user');
            if ($imgName) {
                $user->setProfilePicture('/assets/uploads/users/' . $imgName);
            }
        }

        if (!$this->userRepository->update($user)) {
            throw new Exception("Failed to update user in the database.");
        }
    }

   public function updateProfile(UserModel $user, array $data, ?array $imageFile): void
{
    // Keep the original values so we can compare them later
    // and only send an email for fields that actually changed.
    $oldEmail = $user->getEmail();
    $oldUserName = $user->getUserName();
    $oldFullName = $user->getFullName();
    $oldPhoneNumber = $user->getPhoneNumber();

    // Normalize incoming form data before validation.
    $newEmail = strtolower(trim($data['email'] ?? $user->getEmail()));
    $newUserName = trim($data['userName'] ?? $user->getUserName());
    $newFullName = trim($data['fullName'] ?? $user->getFullName());
    $newPhoneNumber = trim($data['phoneNumber'] ?? $user->getPhoneNumber());
    $newPassword = trim($data['newPassword'] ?? '');

    // Check what changed so we can notify the user later.
    $changedFields = [];

    if (strtolower($oldEmail) !== strtolower($newEmail)) {
        $changedFields[] = 'Email address';
    }

    if ($oldUserName !== $newUserName) {
        $changedFields[] = 'Username';
    }

    if ($oldFullName !== $newFullName) {
        $changedFields[] = 'Full name';
    }

    if ($oldPhoneNumber !== $newPhoneNumber) {
        $changedFields[] = 'Phone number';
    }

    if ($newPassword !== '') {
        $changedFields[] = 'Password';
    }

    // Apply the new values to the model.
    $user->setEmail($newEmail);
    $user->setUserName($newUserName);
    $user->setFullName($newFullName);
    $user->setPhoneNumber($newPhoneNumber);

    // Validate the updated model.
    $this->authService->validateUser($user, '', false);

    // If the email was changed, make sure it is not already in use.
    if (strtolower($oldEmail) !== strtolower($newEmail) && $this->authService->emailExists($newEmail)) {
        throw new InvalidArgumentException('Email already exists!');
    }

    // Only update the password if the user entered a new one.
    if ($newPassword !== '') {
        if (strlen($newPassword) < 8 || !preg_match('/[0-9]/', $newPassword)) {
            throw new InvalidArgumentException("Password must be at least 8 characters and include a number.");
        }

        $user->setPassword(password_hash($newPassword, PASSWORD_DEFAULT));
    }

    // Update the profile picture if a new one was uploaded.
    if ($imageFile && $imageFile['error'] === UPLOAD_ERR_OK) {
        $fileName = $this->handleSecureUpload($imageFile, 'user');

        if ($fileName) {
            $user->setProfilePicture('/assets/uploads/users/' . $fileName);
            $changedFields[] = 'Profile picture';
        }
    }

    // First save the profile changes in the database.
    if (!$this->userRepository->update($user)) {
        throw new Exception('Failed to update profile in database.');
    }

    // Only send a notification if something important actually changed.
   if (!empty($changedFields)) {
    $this->communicationService->sendAccountChangeNotification([
        'email' => $user->getEmail(),
        'full_name' => $user->getFullName()
    ], $changedFields);
}
    
}

    public function deleteUser(int $id): bool
    {
        return $this->userRepository->delete($id);
    }

    public function restoreUser(int $id): bool
    {
        return $this->userRepository->restore($id);
    }



    public function adminGetAll(): array
    {
        return $this->userRepository->adminGetAll();
    }
    public function updateOwnProfile(UserModel $user): bool
    {
        return $this->userRepository->updateProfile($user);
    }

    public function getFilteredUsers(string $search = '', string $role = '', string $sort = ''): array
    {
        return $this->userRepository->getAllFiltered($search, $role, $sort);
    }

    private function handleSecureUpload(array $file, string $prefix): ?string
    {
        // validating MIME type (actual file content, not just extension)
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];

        if (!in_array($mimeType, $allowed)) return null;

        $uploadDir = __DIR__ . '/../../public/assets/uploads/users/';
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newName = uniqid($prefix . '_', true) . '.' . $ext;

        return move_uploaded_file($file['tmp_name'], $uploadDir . $newName) ? $newName : null;
    }
}
