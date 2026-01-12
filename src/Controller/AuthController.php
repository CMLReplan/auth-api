<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthController extends AbstractController
{
    #[Route('/api/register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword($hasher->hashPassword($user, $data['password']));

        $em->persist($user);
        $em->flush();

        return new JsonResponse(['message' => 'User registered']);
    }

    #[Route('/api/login', methods: ['POST'])]
    public function login(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);

        if (!$user || !$hasher->isPasswordValid($user, $data['password'])) {
            return new JsonResponse(['error' => 'Invalid credentials'], 401);
        }

        $otp = rand(1000, 9999);
        $user->setOtp((string)$otp);
        $user->setOtpExpiresAt(new \DateTime('+5 minutes'));

        $em->flush();

        // OTP sending simulated
        return new JsonResponse(['message' => 'OTP sent', 'otp' => $otp]);
    }

    #[Route('/api/verify-otp', methods: ['POST'])]
    public function verifyOtp(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);

        if (!$user || $user->getOtp() !== $data['otp']) {
            return new JsonResponse(['error' => 'Invalid OTP'], 401);
        }

        if ($user->getOtpExpiresAt() < new \DateTime()) {
            return new JsonResponse(['error' => 'OTP expired'], 401);
        }

        $user->setOtp(null);
        $user->setOtpExpiresAt(null);
        $em->flush();

        return new JsonResponse(['message' => 'Login successful']);
    }
}
