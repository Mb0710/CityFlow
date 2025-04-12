<?php

namespace App\Http\Controllers;

use App\Models\ConnectedObject;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class RapportController extends Controller
{
    public function showReport()
    {
        $report = ConnectedObject::generateReport();

        return view('rapport', compact('report'));
    }
    
    public function showFilteredReport(Request $request)
    {
        $query = ConnectedObject::query();
        
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }
        
        $objects = $query->with('zone')->get();
        
        $types = ConnectedObject::distinct('type')->pluck('type');
        $zones = CityZone::all();
        
        return view('reports.filtered_report', compact('objects', 'types', 'zones'));
    }
/* PDF / EXCEL NE FONCTIONNENT PAS
    public function exportReportPdf()
    {
        $report = ConnectedObject::generateReport();
        
        $pdf = PDF::loadView('reports.pdf_report', compact('report'));
        
        return $pdf->download('rapport-objets-connectes.pdf');
    }

    public function exportReportExcel()
    {
        return Excel::download(new ConnectedObjectsExport, 'rapport-objets-connectes.xlsx');
    }*/

}

