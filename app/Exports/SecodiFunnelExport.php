<?php

namespace App\Exports;

use App\Helpers\Helpers;
use App\Models\Exportcliente;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SecodiFunnelExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $filtro;

    protected $user;

    public function __construct($filtro, $user)
    {
        $this->filtro = $filtro;
        $this->user = $user;
    }

    public function query()
    {
        $where = Helpers::filtroExportCliente(json_decode($this->filtro), $this->user);

        return Exportcliente::query()->where($where);
    }

    public function headings(): array
    {
        return [
            'Equipo',
            'Consultor o Ejecutivo',
            'Fecha de Ingreso',
            'Empresa',
            'Ruc',
            'Monto Plan',
            'Plan',
            'Etapa',
            'Multipuntos',
            'Nro. Multipuntos',
            'Fecha de Ultima Gestión',
            'Comentario',
        ];
    }

    public function map($cliente): array
    {
        return [
            $cliente->ejecutivo_equipo,
            $cliente->ejecutivo,
            $cliente->fecha_creacion,
            $cliente->razon_social,
            $cliente->ruc,
            '',
            '',
            $cliente->etapa,
            '',
            '',
            $cliente->fecha_ultimo_contacto,
            $cliente->comentario_5,
        ];
    }
}
