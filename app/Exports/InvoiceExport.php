<?php

namespace App\Exports;

use App\Models\Anak;
use App\Models\Kelompok;
use App\Models\Paket;
use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Carbon\Carbon;
class InvoiceExport implements FromCollection, WithHeadings, WithMapping, 
WithEvents, WithStyles, WithTitle, WithCustomStartCell
{    
    protected $headingLength;
    private $rowNumber = 0;
    public function __construct($kelompok, $paket)
    {
        $this->kelompok = $kelompok;
        $this->paket = $paket;
        $this->headingLength = count($this->headings());
    }

    public function collection()
    {
        return Anak::with(['paket'])
        ->when($this->kelompok, function($query, $kelompok){
            return $query->where('kelompok_id', $kelompok);
        })
        ->when($this->paket, function($query, $paket){
            return $query->where('paket_id', $paket);
        })
        ->orderBy('nama', 'DESC')
        ->get();
    }

    public function startCell(): string
    {
        return 'A7';
    }

    public function headings(): array
    {
        $headings =  [
            'No', 
            'Nama Anak', 
            'Besar Iuran',
            'Januari', 
            'Februari', 
            'Maret', 
            'April', 
            'Mei', 
            'Juni', 
            'Juli', 
            'Agustus', 
            'September', 
            'Oktober', 
            'November',
            'Desember',
            'Jumlah'
        ];

        return $headings;
    }

    public function map($data): array
    {
        $this->rowNumber++;
        $total = 0;
        $content = [
            $this->rowNumber,
            $data->nama,
            $data->paket->spp,
        ];

        for ($month = 1; $month <= 12; $month++) {
            $invoiceInMonth = Invoice::where('anak_id', $data->id)
                                ->whereMonth('tgl', $month)
                                ->first();
            if ($invoiceInMonth) {
                $content[] = $invoiceInMonth->total;
                $total += $invoiceInMonth->total;
            } else {
                $content[] = '-';
            }
        }

        $content[] = $total;
    
        return $content;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $highestColumn = chr(64 + $this->headingLength);

                $sheet->setAutoFilter('A7:'.chr(64 + $this->headingLength) . '7');
                foreach (range('A', $sheet->getHighestColumn()) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Merge cells for title
                $sheet->mergeCells('A1:'.$highestColumn.'2');
                $sheet->setCellValue('A1', 'Data Iuran SPP');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['argb' => 'FFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'F0B86E'],
                    ],
                ]);

                if($this->kelompok){
                    $kelompok = Kelompok::where('id', $this->kelompok)->first();

                    $sheet->setCellValue('A4', 'Kelompok');
                    $sheet->setCellValue('B4', 'Kelompok '. $kelompok->nama . ' ('. $kelompok->usia .')');
                }

                if($this->paket){
                    $paket = Paket::where('id', $this->paket)->first();

                    $sheet->setCellValue('A4', 'Paket');
                    $sheet->setCellValue('B4', $paket->nama);
                }
                // Set header styles

                $sheet->setCellValue('A1', 'Data Invoice');

                $sheet->getStyle('A7:'.chr(64 + $this->headingLength) . '7')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'F0B86E'],
                    ],
                ]);
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            7    => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Data Anak';
    }
}

