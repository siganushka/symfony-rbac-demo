<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class UsernamePasswordAuthenticator extends AbstractGuardAuthenticator
{
    use TargetPathTrait;

    protected $httpUtils;
    protected $passwordEncoder;
    protected $options = [];

    public function __construct(HttpUtils $httpUtils, UserPasswordEncoderInterface $passwordEncoder, array $options = [])
    {
        $this->httpUtils = $httpUtils;
        $this->passwordEncoder = $passwordEncoder;

        $this->options = array_replace([
            'login_path' => 'app_login',
            'default_target_path' => 'app_index',
        ], $options);
    }

    public function supports(Request $request)
    {
        return $request->isMethod('POST') &&
            $this->httpUtils->checkRequestPath($request, $this->options['login_path']);
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'username' => $request->request->get('username'),
            'password' => $request->request->get('password'),
        ];

        $session = $request->getSession();
        $session->set(Security::LAST_USERNAME, $credentials['username']);

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            return $userProvider->loadUserByUsername($credentials['username']);
        } catch (AuthenticationException $e) {
            throw new CustomUserMessageAuthenticationException('Invalid credentials.');
        }
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        if (!$this->passwordEncoder->isPasswordValid($user, $credentials['password'])) {
            throw new CustomUserMessageAuthenticationException('Invalid credentials.');
        }

        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $session = $request->getSession();
        $session->set(Security::AUTHENTICATION_ERROR, $exception);

        return $this->httpUtils->createRedirectResponse($request, $this->options['login_path']);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $targetPath = $this->getTargetPath($request->getSession(), $providerKey);

        if (!$targetPath) {
            $targetPath = $this->httpUtils->generateUri($request, $this->options['default_target_path']);
        }

        return $this->httpUtils->createRedirectResponse($request, $targetPath);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return $this->httpUtils->createRedirectResponse($request, $this->options['login_path']);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
