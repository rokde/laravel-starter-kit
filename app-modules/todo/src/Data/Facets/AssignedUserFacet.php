<?php

declare(strict_types=1);

namespace Modules\Todo\Data\Facets;

use App\Data\Facets\AbstractFacet;
use App\Data\Facets\FilterValueEnum;
use Illuminate\Support\Collection;

class AssignedUserFacet extends AbstractFacet
{
    /**
     * @param  Collection<\App\Models\User>  $users
     */
    public function setPossibleUsers(Collection $users): self
    {
        $this->options = $users
            ->map(fn ($user): array => [
                'value' => $user->id,
                'label' => $user->name,
            ])
            ->sortBy('label')
            ->values()
            ->toArray();

        return $this;
    }

    public function includeNoUserFilter(): self
    {
        $this->options[] = [
            'value' => FilterValueEnum::NULL->value,
            'label' => __('Unassigned'),
        ];

        return $this;
    }
}
