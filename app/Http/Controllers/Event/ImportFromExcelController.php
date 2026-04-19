<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\ImportFromExcelRequest;
use App\Services\Event\EventsImport;
use Exception;

class ImportFromExcelController extends Controller
{
    public function __invoke(ImportFromExcelRequest $request)
    {

        try {
            $import = new EventsImport;
            $import->import($request->file('file'));

            if (count($import->failures) > 0) {
                return back()
                    ->with('import_failures', $import->failures)
                    ->with('warning', 'Импорт завершен, но в некоторых строках были ошибки.');
            }

            return back()->with('success', 'Все мероприятия успешно импортированы');
        } catch (Exception $e) {
            return back()->withErrors(['file' => 'Произошла ошибка при чтении файла: ' . $e->getMessage()]);
        }
    }
}
