<?php

namespace App\Exports;

use App\Models\Anak;
use App\Models\Kelompok;
use App\Models\Paket;
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

class AnakExport implements FromCollection, WithHeadings, WithMapping, 
WithEvents, WithStyles, WithTitle, WithCustomStartCell
{    
    protected $headingLength;
    public function __construct($kelompok, $paket)
    {
        $this->kelompok = $kelompok;
        $this->paket = $paket;
        $this->headingLength = count($this->headings());
    }

    public function collection()
    {
        return Anak::with('kelompok')
        ->when($this->kelompok, function($query, $kelompok){
            return $query->whereHas('kelompok', function($q) use($kelompok){
                return $q->where('id', $kelompok);
            });
        })
        ->orderBy('id', 'DESC')->get();
    }

    public function startCell(): string
    {
        return 'A7';
    }

    public function headings(): array
    {
        $headings =  [
            'Nama Lengkap', 
            'Nama Panggilan', 
            'L/P', 
            'Tempat Lahir', 
            'Tanggal Lahir', 
            'Anak Ke', 
            'Alamat', 
            'Laundry', 
            'Sosialisasi dengan Lingkungan', 
            'Sakit yang Pernah Diderita', 
            'Makanan yang Disukai', 
            'Makanan yang Tidak Disukai', 
            'Alergi', 
            'Tanggal Masuk',
            'Jarak',
            'Antar Jemput', 
        ];

        if(!$this->kelompok){
            array_splice($headings, 3, 0, "Kelompok");
        }

        if(!$this->paket){
            array_splice($headings, 13, 0, "Paket");
        }


        return $headings;
    }

    public function map($data): array
    {
        // dd($data);
        $content = [
            $data->nama, 
            $data->username, 
            $data->jk == 'Laki-Laki' ? 'L' : 'P', 
            $data->tmp_lahir, 
            $data->tgl_lahir, 
            $data->anak_ke, 
            $data->alamat, 
            $data->jarak, 
            $data->sosialisasi_dengan_lingkungan, 
            $data->sakit_yang_pernah_diderita, 
            $data->makanan_yang_disukai, 
            $data->makanan_yang_tidak_disukai, 
            $data->alergi, 
            $data->tgl_terima,
            $data->isAntarJemput == 1 ? 'Ya' : 'Tidak', 
            $data->isLaundry == 1 ? 'Ya' : 'Tidak', 
        ];

        if(!$this->kelompok){
            $kelompok = 'Kelompok '. $data->kelompok->nama . ' ('. $data->kelompok->usia .')';
            // dd($data->kelompok);
            array_splice($content, 3, 0, $kelompok);
        }

        if(!$this->paket){
            array_splice($content, 13, 0, $data->paket->nama);
        }
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
                $sheet->setCellValue('A1', 'Data Anak');
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

                $sheet->setCellValue('A1', 'Data Anak');

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

