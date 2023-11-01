<?php

namespace App\View\Components;

use App\Models\Platform;
use Illuminate\View\Component;

class PlatformLabel extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public Platform $platform)
    {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.platform-label');
    }
}
