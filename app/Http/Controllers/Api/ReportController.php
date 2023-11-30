<?php

namespace App\Http\Controllers\Api;

use DateTime;
use ZipStream\ZipStream;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Joalvm\Utils\Collection;
use App\Components\Controller;
use Illuminate\Support\Facades\Log;
use Barryvdh\Snappy\Facades\SnappyPdf;
use App\Repositories\PeriodsRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use ZipStream\Option\Archive as ZipArchive;
use App\Repositories\Custom\PersonsRepository;
use App\Repositories\Custom\ChildrenRepository;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportController extends Controller
{
    public function requestsZip(Request $request)
    {
        $filename = 'documents_' . date('Y-m-d_H_i_s') . '.zip';
        $options = new ZipArchive();
        $options->setSendHttpHeaders(true);

        $units = to_array_int($request->get('units')) ?? [];

        $zip = new ZipStream($filename, $options);

        foreach ($this->handleZipData($request) as $data) {
            $file = [$data->unit];

            if (in_array(6, $units)) {
                $file[] = $data->boat;
            }

            $file[] = $data->names;

            try {
                if ($data->children) {
                    $this->loadChildrenDocumentZip($zip, $file, $data->children);
                }

                if ($data->documents) {
                    $this->loadFormatsToZip($zip, $file, $data->documents);
                }
            } catch (\Exception $ex) {
                Log::critical($ex->getMessage());
            }
        }

        $zip->finish();
    }

    public function requestsExcel(Request $request)
    {
        \PhpOffice\PhpSpreadsheet\Settings::setLocale('es_pe');

        $filename = 'campania_escolar_2021_' . date('Y-m-d_h_m_s') . '.xlsx';
        $mime = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $this->createHeaders($sheet);
        $this->handleExcelData($sheet, $request);

        $sheet->setTitle('Campaña escolar 2021');
        $sheet->setAutoFilter($sheet->calculateWorksheetDimension());

        $writer = new Xlsx($spreadsheet);

        header("Content-Type: ${mime}");
        header("Content-Disposition: attachment;filename=\"${filename}\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    private function handleExcelData(Worksheet &$sheet, Request $request)
    {
        $repository = new ChildrenRepository();
        $active = (new PeriodsRepository())->getActive();

        $repository->setUser(session('user'));

        $repository->setBoats($request->get('boats'));
        $repository->setUnits($request->get('units'));
        $repository->setStatus($request->get('status'));

        $rows = $repository->getChildrenRequestsExcel($active->get('id'))->toArray();

        $colIndex = 1;
        $rowIndex = 2;

        foreach ($rows as $row) {
            $bono = Arr::get($row, 'period.amount_bonds');
            $getLoan = Arr::get($row, 'get_loan');

            if (Arr::get($row, 'has_superior_child')) {
                $bono += 50;
            }

            $data = [
                Arr::get($row, 'person.dni'),
                $this->formatDate(Arr::get($row, 'request_date')),
                'approved' === Arr::get($row, 'status') ? $this->formatDate(Arr::get($row, 'approved.at')) : '-',
                status_message(Arr::get($row, 'status'), true),
                (int) Arr::get($row, 'person.id'), // person_id
                Arr::get($row, 'person.names') ?? '-',
                Arr::get($row, 'person.phone', '-') ?? '-',
                Arr::get($row, 'approved.by', '-') ?? '-',
                Arr::get($row, 'unit.name', '-') ?? '-',
                Arr::get($row, 'boat.name', '-') ?? '-',
                Arr::get($row, 'education_level.name', '-') ?? '-',
                Arr::get($row, 'register_child', '-') ?? '-',
                Arr::get($row, 'child.fullname', '-') ?? '-',
                Arr::get($row, 'child.birth_date') ?? '-',
                'pick_in_plant' == Arr::get($row, 'delivery_type') ? 'Recojo en planta' : 'Envio A domicilio',
                Arr::get($row, 'plant.name', '-') ?? '-',
                Arr::get($row, 'department.name', '-') ?? '-',
                Arr::get($row, 'province.name', '-') ?? '-',
                Arr::get($row, 'district.name', '-') ?? '-',
                Arr::get($row, 'address', '-') ?? '-',
                Arr::get($row, 'address_reference', '-') ?? '-',
                Arr::get($row, 'responsable_name', '-') ?? '-',
                Arr::get($row, 'responsable_dni', '-') ?? '-',
                (string) Arr::get($row, 'responsable_phone', '-') ?? '-',
                (int) $bono,
                $getLoan ? (int) Arr::get($row, 'period.max_amount_loan') : 0,
            ];

            foreach ($data as $value) {
                $sheet->setCellValueByColumnAndRow($colIndex, $rowIndex, $value);
                ++$colIndex;
            }

            ++$rowIndex;
            $colIndex = 1;
        }
    }

    private function loadFormatsToZip(
        ZipStream &$zip,
        array $path,
        array $documents
    ) {
        foreach ($documents as $document) {
            $info = pathinfo(public_path($document['file']));
            $filename = $info['filename'] . '.pdf';
            $ipath = $path;

            $ipath[] = 'Formatos';
            $ipath[] = $filename;

            $stream = $this->getStreamPDF($document['file']);

            $zip->addFileFromStream(implode('/', $ipath), $stream);

            fclose($stream);
        }
    }

    private function loadChildrenDocumentZip(
        ZipStream &$zip,
        array $path,
        array $children
    ) {
        foreach ($children as $child) {
            $info = pathinfo(public_path($child['file']));
            $filename = $info['filename'] . '.pdf';
            $ipath = $path;

            $ipath[] = $child['fullname'];
            $ipath[] = 'dni' === $child['type']
                ? 'Documento de Identidad'
                : 'Sustentos Escolares';
            $ipath[] = $filename;

            if ('pdf' === strtolower($info['extension'])) {
                $zip->addFileFromPath(implode('/', $ipath), public_path($child['file']));

                continue;
            }

            $stream = $this->getStreamPDF($child['file']);

            $zip->addFileFromStream(implode('/', $ipath), $stream);

            fclose($stream);
        }
    }

    private function getStreamPDF(string $file)
    {
        $imageInfo = getimagesize(public_path($file));

        /** @var \Barryvdh\Snappy\PdfWrapper $wrapper */
        $wrapper = SnappyPdf::loadView('reports.document', [
            'file' => public_path($file),
            'info' => $imageInfo,
        ]);

        $wrapper->setOrientation('Portrait');

        if ($imageInfo[0] >= $imageInfo[1]) {
            $wrapper->setOrientation('Landscape');
        }

        return $this->toStream($wrapper->output());
    }

    private function toStream(string $string)
    {
        $stream = fopen('php://memory', 'r+');

        fwrite($stream, $string);
        rewind($stream);

        return $stream;
    }

    private function handleZipData(Request $request): Collection
    {
        $repository = new PersonsRepository();

        $repository->setUser(session('user'));
        $repository->setPeriod(session('selected_period.id'));

        $repository->setBoats($request->get('boats'));
        $repository->setUnits($request->get('units'));

        return $repository->getDataFilesZip();
    }

    private function formatDate($date): ?string
    {
        if (!$date) {
            return null;
        }

        $date = new DateTime($date . ' 00:00:00');

        return $date->format('d-m-Y');
    }

    public function createHeaders(Worksheet &$sheet)
    {
        $headers = [
            'DNI',
            'Fecha de solicitud',
            'Fecha de Aprobación',
            'estado de la solicitud',
            'Código',
            'Nombre trabajador',
            'Celular',
            'Admin',
            'Unidad',
            'Embarcación',
            'Nivel Educativo',
            'Fecha hijo',
            'Nombre del hijo',
            'Fecha de nacimiento hijo',
            'Tipo de recojo',
            'Planta de recojo',
            'Departamento',
            'Provincia',
            'Distrito',
            'Dirección',
            'Referencia',
            'Persona recepción',
            'DNI persona recepción',
            'Celular persona recepción',
            'Monto Bono',
            'Monto Prestamo',
        ];

        foreach ($headers as $index => $header) {
            $sheet->setCellValueByColumnAndRow($index + 1, 1, $header);
        }
    }
}
