<?php

namespace App\View\Components;

use Illuminate\View\Component;

class modal extends Component
{
    public $modalId;
    public $formAction;
    
    public function __construct($modalId, $formAction)
    {
        $this->modalId = $modalId;
        $this->formAction = $formAction;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.modal');
    }
}
