<?php

namespace App\Http\Controllers\Api\Children;

use Joalvm\Utils\Response;
use Illuminate\Http\Request;
use App\Components\Controller;
use Illuminate\Validation\Rule;
use App\Models\Children\Children;
use App\Models\Children\Requests;
use App\Models\Children\Documents;
use Illuminate\Support\Facades\DB;
use App\Repositories\PeriodsRepository;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Children\ChildrenRepository;
use App\Repositories\Children\RequestsRepository;
use App\Repositories\Children\DocumentsRepository;
use Illuminate\Support\Arr;

class ChildrenController extends Controller
{
    private $repository;

    public function __construct(ChildrenRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $this->repository->setUser(config('user'));

        return Response::collection(
            $this->repository
                ->setParent($request->get('parent'))
                ->setGender($request->get('gender'))
                ->all()
        );
    }

    public function show(int $id)
    {
        $this->repository->setUser(config('user'));

        return Response::item($this->repository->find($id));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        $this->repository->setUser(config('user'));

        $model = $this->repository->save($request);

        if ($request->has('request')) {
            $this->registerRequests($model, $request);
        }

        DB::commit();

        return Response::created($this->repository->find($model->id));
    }

    public function update(int $id, Request $request)
    {
        $this->repository->setUser(config('user'));

        return Response::updated(
            $this->repository->find(
                $this->repository->update($id, $request)->id
            )
        );
    }

    private function registerRequests(Children $child, Request $request): void
    {
        $requestsRepository = new RequestsRepository();
        $period = (new PeriodsRepository())->getActive();
        $req = $request->input('request');

        $data = [
            'children_id' => $child->id,
            'periods_id' => $period->get('id'),
            'education_levels_id' => to_int($req['education_levels_id']),
            'get_loan' => $req['get_loan'],
            'get_pack' => $req['get_pack'],
            'delivery_type' => Arr::get($req, 'delivery_type'),
            'plants_id' => to_int($req['plants_id']),
            'responsable_name' => Arr::get($req, 'responsable_name'),
            'responsable_dni' => Arr::get($req, 'responsable_dni'),
            'responsable_phone' => Arr::get($req, 'responsable_phone'),
            'address' => Arr::get($req, 'address'),
            'address_reference' => Arr::get($req, 'address_reference'),
            'districts_id' => to_int(Arr::get($req, 'districts_id')),
        ];

        $requestsRepository->setUser(config('user'));

        $model = $requestsRepository->save(new Request($data));

        if ($request->has('request.documents')) {
            $this->registerDocuments($model, $request);
        }
    }

    private function registerDocuments(
        Requests $requests,
        Request $request
    ): void {
        $documentRepository = new DocumentsRepository();
        $documents = $request->input('request.documents', []);

        $this->validateDocumentsData($documents);

        foreach ($documents as $document) {
            $data = [
                'children_requests_id' => $requests->id,
                'type' => $document['type'],
                'file' => $document['file'],
            ];

            $documentRepository->save(new Request($data));
        }
    }

    private function validateDocumentsData(array $documents)
    {
        Validator::make(
            $documents,
            [
                '*.type' => [
                    'required',
                    'string',
                    Rule::in(Documents::ALLOWED_TYPES),
                ],
                '*.file' => ['required', 'string'],
            ]
        )->validate();
    }
}
