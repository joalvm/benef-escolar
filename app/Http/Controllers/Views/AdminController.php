<?php

namespace App\Http\Controllers\Views;

use App\Models\Units;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Components\Controller;
use App\Models\Children\Requests;
use App\Repositories\BoatsRepository;
use App\Repositories\PeriodsRepository;
use Illuminate\Support\Facades\Session;
use App\Repositories\Children\ChildrenRepository;
use App\Repositories\Children\DocumentsRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Repositories\Custom\PersonsRepository as CustomPersonsRepository;
use App\Repositories\Custom\ChildrenRepository as CustomChildrenRepository;
use App\Repositories\Persons\RequestsRepository as PersonsRequestsRepository;
use App\Repositories\Children\RequestsRepository as ChildrenRequestsRepository;

class AdminController extends Controller
{
    private $personsRequestsRepository;

    private $childrenRequestsRepository;

    private $periodRepository;

    private $childrenRepository;

    private $customChildrenRepository;

    private $customPersonsRepository;

    public function __construct(
        PeriodsRepository $periodsRepository,
        ChildrenRepository $childrenRepository,
        PersonsRequestsRepository $personRequestRepository,
        ChildrenRequestsRepository $childrenRequestsRepository,
        CustomChildrenRepository $customChildrenRepository,
        CustomPersonsRepository $customPersonsRepository
    ) {
        $this->periodRepository = $periodsRepository;
        $this->childrenRepository = $childrenRepository;

        $this->personsRequestsRepository = $personRequestRepository;
        $this->childrenRequestsRepository = $childrenRequestsRepository;

        // Custom
        $this->customPersonsRepository = $customPersonsRepository;
        $this->customChildrenRepository = $customChildrenRepository;
    }

    public function changePeriod(Request $request)
    {
        $id = $request->input('periods');

        dd($id);

        Session::put(
            'selected_period',
            Arr::only(
                $this->periodRepository->find($id)->toArray(),
                ['id', 'name']
            )
        );

        if (Str::contains(url()->previous(), 'change-period')) {
            return redirect('admin/dashboard');
        }

        return redirect(url()->previous());
    }

    public function dashboard()
    {
        return view('pages.admin.dashboard.dashboard');
    }

    public function persons()
    {
        return view('pages.admin.persons.persons');
    }

    public function requests(Request $request)
    {
        return view('pages.admin.requests.requests', [
            'boats' => (new BoatsRepository())->all(),
            'units' => Units::all()->toArray(),
        ]);
    }

    public function approval(int $id, Request $request)
    {
        $periodId = session('selected_period.id');

        $this->customPersonsRepository->setPeriod($periodId);
        $this->customChildrenRepository->setPeriod($periodId);

        $person = $this->customPersonsRepository->getFullRequests($id);

        if ($person->isEmpty()) {
            abort(404, 'El trabajador no ha sido encontrado');
        }

        $request = collect(Arr::except($person->toArray(), 'person'));

        $children = $this->customChildrenRepository->getChildrenFullRequests(
            $request->get('id')
        );

        return view('pages.admin.requests.approval.approval', [
            'request' => $request,
            'person' => collect($person->get('person')),
            'documents' => collect($person->get('documents')),
            'children' => $children->toArray(),
        ]);
    }

    public function educationLevels()
    {
        return view('pages.admin.education_levels.education_levels');
    }

    public function periods()
    {
        return view('pages.admin.periods.periods');
    }

    public function bondsChildren()
    {
        $periodId = session('selected_period.id');

        return view('pages.users.bonds.children.children', [
            'period' => $this->periodRepository->find($periodId)->toArray(),
            'children' => [],
            'request' => [],
            'documents' => [],
        ]);
    }

    public function bondsChildrenEdit(int $requestId)
    {
        $period = session('selected_period.id');

        $this->childrenRequestsRepository->setPeriod($period);
        $this->childrenRepository->setUser(session('user'));

        $request = $this->childrenRequestsRepository->find($requestId);

        $id = $request->get('child.id');

        $children = $this->childrenRepository->find($id);

        if ($children->isEmpty()) {
            throw new NotFoundHttpException();
        }

        return view('pages.users.bonds.children.children', [
            'previewPage' => url()->previous(),
            'period' => $this->periodRepository->find($period)->toArray(),
            'children' => $children->toArray(),
            'documents' => $this->getChildDocuments($id),
            'request' => $this->getchildRequestActive($id, $period),
        ]);
    }

    private function getchildRequestActive(int $childId, int $periodId): array
    {
        $this->childrenRequestsRepository->setUser(session('user'));
        $this->childrenRequestsRepository->setPeriod($periodId);

        $request = $this->childrenRequestsRepository->getActiveRequest($childId);

        return $request->toArray();
    }

    private function getChildDocuments(int $childId): array
    {
        $documentsRepository = new DocumentsRepository();

        $documentsRepository->setUser(session('user'));

        return $documentsRepository->setChildren([$childId])->all()->toArray();
    }
}
