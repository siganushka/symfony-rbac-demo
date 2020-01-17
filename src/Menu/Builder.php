<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Builder
{
    private $factory;
    private $authorizationChecker;

    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->factory = $factory;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function createNavbar(array $options)
    {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttribute('class', 'navbar-nav');

        $menu->addChild('navbar.role', ['route' => 'app_role'])
            ->setExtra('disabled', !$this->authorizationChecker->isGranted('app_role'));

        $menu->addChild('navbar.user', ['route' => 'app_user'])
            ->setExtra('disabled', !$this->authorizationChecker->isGranted('app_user'));

        foreach ($menu->getChildren() as $child) {
            $linkAttribute = 'nav-link';
            if ($child->getExtra('disabled')) {
                $linkAttribute .= ' disabled';
            }

            $child
                ->setAttribute('class', 'nav-item')
                ->setLinkAttribute('class', $linkAttribute)
            ;
        }

        return $menu;
    }
}
