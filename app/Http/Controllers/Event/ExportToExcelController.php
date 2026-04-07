<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Services\Event\EventsExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportToExcelController extends Controller
{
    public function __invoke(): BinaryFileResponse
    {
        return Excel::download(new EventsExport, 'events_' . now()->format('Y-m-d') . '.xlsx');
    }
}
