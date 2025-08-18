<?php

namespace App\Livewire\Tiles;

use Livewire\Component;

class BaseComponent extends Component
{
    public $position;
    public $refreshInterval;

    public function mount(string $position)
    {
        $this->position = $position;
        $this->refreshInterval = config('dashboard.tiles.charts.refresh_interval_in_seconds', 300);
    }
}