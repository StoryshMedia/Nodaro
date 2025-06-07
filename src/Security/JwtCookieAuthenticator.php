<?php

namespace Smug\Core\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\JWTAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\CookieTokenExtractor;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;
use Symfony\Component\HttpFoundation\Request;

class JwtCookieAuthenticator extends JWTAuthenticator
{
    public function supports(Request $request): ?bool
    {
        return $request->cookies->has('jwt_token');
    }

    public function getCredentials(Request $request): string
    {
        return $request->cookies->get('jwt_token');
    }
    
    protected function getTokenExtractor(): TokenExtractorInterface
    {
        // Return a custom extractor, no matter of what are configured
        return new CookieTokenExtractor('jwt_token');
    }
}
