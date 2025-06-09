<?php declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ExtendedJWTAuthenticationSuccessHandler extends AuthenticationSuccessHandler
{
    public function onAuthenticationSuccess(Request $request, $token): JsonResponse
    {
        $user = $this->extractUserFromToken($token);

        // Check if 2FA is enabled
        if ($user instanceof UserInterface && $user->isTotpAuthenticationEnabled()) {
            // Generate a temporary token
            $token = $this->jwtManager->createFromPayload($user, [
                '2fa_required' => true,
                'user_id' => $user->getId(),
                'roles' => $user->getRoles(),
                'exp' => time() + 300 // 5 minutes expiration
            ]);

            return new JsonResponse([
                'is2faEnabled' => true,
                'token' => $token,
                'refreshToken' => null,
            ], 200);
        }

        // Call the parent to get the default response
        $response = parent::onAuthenticationSuccess($request, $token);
        $data = json_decode($response->getContent(), true);

        $data['is2faEnabled'] = false;

        return new JsonResponse($data);
    }

    /**
     * @param TokenInterface $token
     * @return User|null
     */
    private function extractUserFromToken(TokenInterface $token): ?User
    {
        return $token->getUser();
    }
}
