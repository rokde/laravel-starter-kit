<?php

declare(strict_types=1);

namespace Modules\FoundationLayout\Service;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use Modules\FoundationLayout\Variants\ApplicationHeaderLayout;
use Modules\FoundationLayout\Variants\ApplicationSidebarFloatingLayout;
use Modules\FoundationLayout\Variants\ApplicationSidebarInsetLayout;
use Modules\FoundationLayout\Variants\ApplicationSidebarSidebarLayout;
use Modules\FoundationLayout\Variants\AuthenticationSimpleLayout;
use Modules\FoundationLayout\Variants\AuthenticationSplitLayout;

class LayoutService
{
    /**
     * @var array<string, \Modules\FoundationLayout\Variants\Variant>
     */
    private array $authenticationLayoutVariants;

    /**
     * @var array<string, \Modules\FoundationLayout\Variants\Variant>
     */
    private array $applicationLayoutVariants;

    public function __construct()
    {
        $this->authenticationLayoutVariants['simple'] = new AuthenticationSimpleLayout();
        $this->authenticationLayoutVariants['split'] = new AuthenticationSplitLayout();

        $this->applicationLayoutVariants['header'] = new ApplicationHeaderLayout();
        $this->applicationLayoutVariants['sidebar-inset'] = new ApplicationSidebarInsetLayout();
        $this->applicationLayoutVariants['sidebar-sidebar'] = new ApplicationSidebarSidebarLayout();
        $this->applicationLayoutVariants['sidebar-floating'] = new ApplicationSidebarFloatingLayout();
    }

    /**
     * @return array<int, string>
     */
    public function getAuthenticationLayoutVariants(): array
    {
        return array_keys($this->authenticationLayoutVariants);
    }

    public function getDefaultAuthenticationLayoutVariant(): string
    {
        return Arr::first($this->getAuthenticationLayoutVariants());
    }

    public function configureAuthenticationLayout(string $variant): void
    {
        if (! array_key_exists($variant, $this->authenticationLayoutVariants)) {
            throw new InvalidArgumentException('Invalid variant');
        }

        $this->authenticationLayoutVariants[$variant]->proceed();
    }

    /**
     * @return array<int, string>
     */
    public function getApplicationLayoutVariants(): array
    {
        return array_keys($this->applicationLayoutVariants);
    }

    public function getDefaultApplicationLayoutVariant(): string
    {
        return Arr::first($this->getApplicationLayoutVariants());
    }

    public function configureApplicationLayout(string $variant): void
    {
        if (! array_key_exists($variant, $this->applicationLayoutVariants)) {
            throw new InvalidArgumentException('Invalid variant');
        }

        $this->applicationLayoutVariants[$variant]->proceed();
    }
}
