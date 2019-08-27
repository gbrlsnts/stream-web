<?php

namespace App\Services;

use App\Models\User;

class Auth
{
    /**
     * User model
     *
     * @var User
     */
    protected $user;

    /**
     * The password hash algorithm
     *
     * @var integer
     */
    protected $algo;

    /**
     * Initialize the service
     *
     * @param integer $algo The password hash algorithm to use
     * @see https://www.php.net/manual/en/function.password-hash.php
     */
    public function __construct(int $algo)
    {
        $this->user = new User();
        $this->algo = $algo;
    }

    /**
     * Authenticates an user
     *
     * @return bool True, if authenticated
     */
    public function authenticate(string $username, string $password): bool
    {
        $user = $this->user
                ->where('username', $username)
                ->first(['id', 'password']);

        if (is_null($user) || !\password_verify($password, $user->password)) {
            return false;
        }

        \session_regenerate_id();

        $_SESSION['user_id'] = $user->id;

        return true;
    }

    /**
     * Logs out the user and destroys the current session
     *
     * @return void
     */
    public function logout()
    {
        if (!$this->isAuthenticated()) {
            return;
        }

        \session_destroy();
    }

    /**
     * Check if an user is authenticated
     *
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        $userId = $_SESSION['user_id'];

        return !is_null($userId);
    }

    /**
     * Get the authenticated user id
     *
     * @return integer|null
     */
    public function getAuthenticatedUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get the authenticated user
     *
     * @return User|null
     */
    public function getAuthenticatedUser(): ?User
    {
        $userId = $this->getAuthenticatedUserId();

        if (is_null($userId)) {
            return null;
        }

        $user = $this->user->where('id', $userId)->first();

        return $user;
    }
}