<?php

namespace App\Http\Controllers;

use App\Exports\IndotechFunnelExport;
use App\Exports\SecodiFunnelExport;

class ExportController extends Controller
{
    public function secodiFunnel()
    {
        $filtro = request('filtro');
        $user = auth()->user();
        $nameExport = 'FunelWin360.xlsx';

        return (new SecodiFunnelExport($filtro, $user))->download($nameExport);
    }

    public function indotechFunnel()
    {
        $filtro = request('filtro');
        $user = auth()->user();

        $export = new IndotechFunnelExport($filtro, $user);

        return $export->exportToCsv();
    }
}
