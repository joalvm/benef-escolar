<?php

namespace App\Http\Controllers\Api;

use DateTime;
use DateTimeZone;
use Illuminate\Support\Str;
use Joalvm\Utils\Response;
use Illuminate\Http\Request;
use App\Components\Controller;

class FilesController extends Controller
{
    public function index(Request $request)
    {
        $file = $request->file('file');
        $name = Str::random(16) . '-' . $this->getTime();
        $ext = $file->getClientOriginalExtension();

        $result = $file->storeAs('images', "${name}.${ext}");

        return Response::collection(['path' => $result]);
    }

    private function getTime(): string
    {
        return (new DateTime('now', new DateTimeZone('America/Lima')))->format('Ymd_His');
    }
}
