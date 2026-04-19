<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadTemplateController extends Controller
{
    public function __invoke(): StreamedResponse
    {
        $path = 'templates/events_template.xlsx';

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'Файл шаблона не найден');
        }

        return Storage::disk('public')->download($path, 'events_import_template.xlsx');
    }
}
