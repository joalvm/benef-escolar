<?php

namespace App\Http\Controllers\Api\Children;

use Illuminate\Support\Arr;
use Joalvm\Utils\Response;
use App\Mail\DocumentStatus;
use Illuminate\Http\Request;
use App\Mail\ApprovedRequest;
use App\Components\Controller;
use Illuminate\Validation\Rule;
use App\Models\Persons\Requests;
use App\Models\Children\Documents;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Repositories\Children\DocumentsRepository;

class DocumentsController extends Controller
{
    /**
     * @var DocumentsRepository
     */
    private $repository;

    public function __construct(DocumentsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $this->repository->setUser(config('user'));

        return Response::collection(
            $this->repository
                ->setChildren($request->get('children'))
                ->setPlants($request->get('plants'))
                ->setDocumentTypes($request->get('document_types'))
                ->setPersons($request->get('persons'))
                ->setRequests($request->get('requests'))
                ->setRequestStatus($request->get('request_status'))
                ->setResponsable($request->get('responsable'))
                ->setStatus($request->get('status'))
                ->all()
        );
    }

    public function store(Request $request)
    {
        $data = [];
        $this->repository->setUser(config('user'));

        DB::beginTransaction();

        if (Arr::isAssoc($request->all())) {
            array_push(
                $data,
                $this->repository->find(
                    $this->repository->save($request)->id
                )->toArray()
            );
        } else {
            foreach ($request->all() as $doc) {
                array_push(
                    $data,
                    $this->repository->find(
                        $this->repository->save(new Request($doc))->id
                    )->toArray()
                );
            }
        }

        DB::commit();

        return Response::created($data);
    }

    public function show(int $id)
    {
        $this->repository->setUser(config('user'));

        return Response::item($this->repository->find($id));
    }

    public function update(int $id, Request $request)
    {
        $this->repository->setUser(config('user'));

        $model = $this->repository->update($id, $request);
        $requests = $model->requests()->with('personsRequests')->first();

        if (Requests::STATUS_OBSERVED == $model->status) {
            $this->sendNotificationEmail(
                new Request(['documents_id' => $model->id])
            );
        }

        $personRequests = $requests->personsRequests;

        if (Requests::STATUS_APPROVED == $personRequests->status) {
            $person = $personRequests->persons()->first();
            Mail::to($person->email)->send(new ApprovedRequest($person->names));
        }

        return Response::updated($this->repository->find($model->id));
    }

    public function destroy(int $id)
    {
        $this->repository->setUser(config('user'));

        return Response::destroyed(['deleted' => $this->repository->delete($id)]);
    }

    public function sendNotificationEmail(Request $request)
    {
        $result = null;
        $this->validRequestNotificationEmail($request);

        $model = Documents::with('requests.children.persons')->find(
            $request->input('documents_id')
        );

        if ($model->requests->children->persons->email) {
            $result = Mail::to($model->requests->children->persons->email)->send(
                new DocumentStatus([
                    'file' => file_url($model->file),
                    'status' => $model->status,
                    'type' => 'document_children',
                    'children' => sprintf(
                        '%s %s %s',
                        $model->requests->children->name,
                        $model->requests->children->paternal_surname,
                        $model->requests->children->maternal_surname
                    ),
                    'observation' => $model->observation,
                ])
            );
        }

        return Response::created($result);
    }

    private function validRequestNotificationEmail(Request $request)
    {
        $request->validate([
            'documents_id' => [
                'required',
                'integer',
                Rule::exists('children_documents', 'id')->whereNull('deleted_at'),
            ],
        ]);
    }
}
