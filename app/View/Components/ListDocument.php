<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ListDocument extends Component
{
    public $id;

    public $file;

    public $status;

    public $createdAt;

    public $observation;

    public $type;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($id, $file, $status, $type, $createdAt, $observation)
    {
        $this->id = $id;
        $this->file = $file;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->type = $type;
        $this->observation = $observation;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.list-document');
    }
}
