<?php

namespace App\Http\Controllers\Views;

use Illuminate\View\View;
use App\Components\Controller;
use App\Repositories\PersonsRepository;

class PersonsController extends Controller
{
    /**
     * @var PersonsRepository
     */
    private $repository;

    public function __construct(PersonsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function edit(int $id): View
    {
        $person = $this->repository->find($id);

        if ($person->isEmpty()) {
            abort(404);
        }

        return view('pages.persons.persons', [
            'person' => $person->toArray(),
        ]);
    }
}
