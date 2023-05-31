<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data)
    {
        // Check if user already exists
        if ($this->userRepository->findByEmail($data['email'])) {
            return null; // Or throw an exception
        }

        // Create a new user
        $data['password'] = Hash::make($data['password']);
        $user = $this->userRepository->create($data);

        // Generate JWT token
        $token = JWTAuth::fromUser($user);
        return $token;
    }

    public function login(array $credentials)
    {
        // Attempt to authenticate user
        if (!$token = JWTAuth::attempt($credentials)) {
            return null; // Or throw an exception
        }
        return $token;
    }

    public function logout()
    {
        JWTAuth::invalidate();
    }

    public function getCurrentUser()
    {
        return JWTAuth::user();
    }
}
