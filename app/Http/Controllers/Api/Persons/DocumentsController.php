<?php

namespace App\Http\Controllers\Api\Persons;

use Illuminate\Support\Arr;
use Joalvm\Utils\Response;
use App\Mail\DocumentStatus;
use Illuminate\Http\Request;
use App\Mail\ApprovedRequest;
use App\Components\Controller;
use Illuminate\Validation\Rule;
use App\Models\Persons\Requests;
use App\Models\Persons\Documents;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Repositories\Persons\DocumentsRepository;

class DocumentsController extends Controller
{
    private $repository;

    public function __construct(DocumentsRepository $repository)
    {
        $this->repository = $repository;
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

    public function update(int $id, Request $request)
    {
        $this->repository->setUser(config('user'));

        $model = $this->repository->update($id, $request);

        if (Requests::STATUS_OBSERVED == $model->status) {
            $this->sendNotificationEmail(
                new Request(['documents_id' => $model->id])
            );
        }

        $requests = $model->requests()->with('persons')->first();

        if (Requests::STATUS_APPROVED == $requests->status) {
            Mail::to($requests->persons->email)->send(
                new ApprovedRequest($requests->persons->names)
            );
        }

        return Response::updated($this->repository->find($model->id));
    }

    public function sendNotificationEmail(Request $request)
    {
        $result = null;
        $this->validRequestNotificationEmail($request);

        $model = Documents::with('requests.persons')->find(
            $request->input('documents_id')
        );

        if ($model->requests->persons->email) {
            Mail::to($model->requests->persons->email)->send(
                new DocumentStatus([
                    'file' => file_url($model->file),
                    'status' => $model->status,
                    'type' => 'document_person',
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
                Rule::exists('persons_documents', 'id')->whereNull('deleted_at'),
            ],
        ]);
    }
}
