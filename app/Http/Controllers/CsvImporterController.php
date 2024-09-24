<?php

namespace App\Http\Controllers;

use App\Alert;
use App\CsvImporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CsvImporterController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('import-csv-data')) {
            abort(403);
        }
        
        return view('csv-importer.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! Gate::allows('import-csv-data')) {
            abort(403);
        }
        
        $request->validate(CsvImporter::rules());
        
        $errors = false;
        $errorMessages = [];
        
        if ($request->has('shopfindings_file') && $request->file('shopfindings_file')->isValid()) {
            $handle = fopen($request->file('shopfindings_file'), "r");
            
            $i = 0;
            $length = filesize($request->file('shopfindings_file'));
            $sfRows = [];
            
            if ($length == 0) {
                $errors = true;
                $errorMessages[] = 'Shop Findings CSV file is empty.';
            } else {
                while (($data = fgetcsv($handle, $length, ",")) !== FALSE) {
                    if ($i == 0) {
                        $columns = CsvImporter::checkShopFindingCsvColumns($data);
                        
                        if (!$columns) {
                            $errors = true;
                            $errorMessages[] = 'Shop Findings CSV file contains unexpected columns.';
                        }
                    } elseif (!empty($data[0]) && !$errors) { // Skip empty rows.
                        $sfRows[] = CsvImporter::mapShopFindingRowData($data);
                    }
                    
                    $i++;
                }
            }
            
            fclose($handle);
        }
        
        if ($request->has('pieceparts_file') && $request->file('pieceparts_file')->isValid()) {
            $handle2 = fopen($request->file('pieceparts_file'), "r");
            
            $n = 0;
            $length2 = filesize($request->file('pieceparts_file'));
            $ppRows = [];
            
            if ($length2 == 0) {
                $errors = true;
                $errorMessages[] = 'Piece Parts CSV file is empty.';
            } else {
                while (($data2 = fgetcsv($handle2, $length2, ",")) !== FALSE) {
                    if ($n == 0) {
                        $columns2 = CsvImporter::checkPiecePartCsvColumns($data2);
                        
                        if (!$columns2) {
                            $errors = true;
                            $errorMessages[] = 'Piece Parts CSV file contains unexpected columns.';
                        }
                    } elseif (!empty($data2[0]) && !$errors) { // Skip empty rows.
                        $ppRows[] = CsvImporter::mapPiecePartRowData($data2);
                    }
                    
                    $n++;
                }
            }
            
            fclose($handle2);
        }
        
        if ($errors) {
            $errorsString = implode(' ', $errorMessages);
            
            return redirect()->back()
                ->with(Alert::error('CSV data could not be imported. Errors: ' . $errorsString));
        }
        
        if (!empty($sfRows)) CsvImporter::importShopFindingCsv($sfRows);
        if (!empty($ppRows)) CsvImporter::importPiecePartCsv($ppRows);
        
        return redirect(route('notifications.index'))
            ->with(Alert::success('CSV data imported successfully!'));
    }
}
