<?php

namespace App\Http\Controllers\Views;

use App\Components\Controller;
use App\Repositories\BoatsRepository;
use App\Repositories\PeriodsRepository;
use App\Repositories\PersonsRepository;
use App\Repositories\Children\ChildrenRepository;
use App\Repositories\Children\RequestsRepository;
use App\Repositories\Children\DocumentsRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Repositories\Custom\PersonsRepository as CustomPersonsRepository;
use App\Repositories\Custom\ChildrenRepository as ChildrenCustomRepository;

class UsersController extends Controller
{
    /**
     * @var PeriodsRepository
     */
    private $periodRepository;

    /**
     * @var ChildrenRepository
     */
    private $childrenRepository;

    /**
     * @var ChildrenCustomRepository
     */
    private $childrenCustomRepository;

    /**
     * @var CustomPersonsRepository
     */
    private $personRepository;

    public function __construct()
    {
        $this->periodRepository = new PeriodsRepository();
        $this->childrenRepository = new ChildrenRepository();

        // Custom
        $this->childrenCustomRepository = new ChildrenCustomRepository();
        $this->personRepository = new CustomPersonsRepository();
    }

    public function bonds()
    {
        $period = $this->periodRepository->getActive();

        return view('pages.users.bonds.bonds', [
            'period' => $period->toArray(),
            'person' => $this->getPerson(),
            'boats' => (new BoatsRepository())->all(),
            'requests' => $this->getRequests($period->get('id')),
            'children' => $this->getChildren($period->get('id')),
        ]);
    }

    public function bondsChildren()
    {
        return view('pages.users.bonds.children.children', [
            'period' => $this->periodRepository->getActive()->toArray(),
            'children' => [],
            'request' => [],
            'documents' => [],
        ]);
    }

    public function bondsChildrenEdit(int $id)
    {
        $period = $this->periodRepository->getActive();

        $this->childrenRepository->setUser(session('user'));

        $children = $this->childrenRepository->find($id);

        if ($children->isEmpty()) {
            throw new NotFoundHttpException();
        }

        return view('pages.users.bonds.children.children', [
            'period' => $period->toArray(),
            'children' => $children->toArray(),
            'documents' => $this->getChildDocuments($id),
            'request' => $this->getchildRequestActive($id),
        ]);
    }

    public function children()
    {
        return view('pages.users.children.children');
    }

    public function formats()
    {
        return view('pages.users.formats.formats');
    }

    private function getChildren($period): array
    {
        $this->childrenCustomRepository->setUser(session('user'));

        return $this->childrenCustomRepository->getChildrenRequests($period)->all();
    }

    private function getRequests(int $periodId): ?array
    {
        $this->personRepository->setUser(session('user'));
        $this->personRepository->setPeriod($periodId);

        return $this->personRepository->getActiveRequest();
    }

    private function getPerson(): array
    {
        return (new PersonsRepository())->find(session('user.person_id'))->toArray();
    }

    private function getchildRequestActive(int $childId): array
    {
        $requestRepository = new RequestsRepository();

        $requestRepository->setUser(session('user'));

        $request = $requestRepository->getActiveRequest($childId);

        return $request->toArray();
    }

    private function getChildDocuments(int $childId): array
    {
        $documentsRepository = new DocumentsRepository();

        $documentsRepository->setUser(session('user'));

        return $documentsRepository->setChildren([$childId])->all()->toArray();
    }
}
