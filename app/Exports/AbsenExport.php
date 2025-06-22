<?php

namespace App\Exports;

use App\Models\Anak;
use App\Models\Kelompok;
use App\Models\Absen;
use App\Models\AbsenDetail;
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
use Carbon\CarbonPeriod;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class AbsenExport implements FromCollection, WithHeadings, WithMapping, 
WithEvents, WithStyles, WithTitle, WithCustomStartCell, WithColumnWidths, ShouldAutoSize
{    
    protected $headingLength;
    private $rowNumber = 1;
    public function __construct($kelompok, $tgl)
    {
        $this->kelompok = $kelompok;
        $this->tgl = $tgl;
        $this->headingLength = 0;
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
        $days = [];
        $headings =  [
            'No',
            'Nama',
            'L/P',
        ];

        $start = Carbon::parse($this->tgl)->startOfMonth();
        $end = Carbon::parse($this->tgl)->lastOfMonth();
        $periode = CarbonPeriod::create($start, $end);
        foreach($periode as $p)
        {
            $headings[] = $p->format('d');
        }
        $headings = array_merge($headings, ['H', 'I', 'S', 'A']);


        $this->headingLength = count($headings);
        return $headings;
    }

    public function map($data): array
    {
        // dd($data);
        $start = Carbon::parse($this->tgl)->startOfMonth();
        $end = Carbon::parse($this->tgl)->lastOfMonth();
        $periode = CarbonPeriod::create($start, $end);

        $content = [
            $this->rowNumber++,
            $data->nama, 
            $data->jk == 'Laki-Laki' ? 'L' : 'P', 
        ];

        foreach($periode as $p){
            $status = AbsenDetail::whereHas('absen', function($q) use($p){
                            return $q->whereDate('tgl', $p);
                        })
                        ->where('anak_id', $data->id)
                        ->first();
            // $st = '';
            //  = $status ? $status->status : '-';
            if($status){
                if($status->status == 'Hadir'){
                    $st = 'H';
                }else if($status->status == 'Izin'){
                    $st = 'I';
                }else if($status->status == 'Sakit'){
                    $st = 'S';
                }else if($status->status == 'Alpa'){
                    $st = 'A';
                }
            }else{
                $st = '-';
            }
            $content[] = $st;
        }

        $content[] = AbsenDetail::where('anak_id', $data->id)->where('status', 'Hadir')->count() ?? '0'; // H
        $content[] = AbsenDetail::where('anak_id', $data->id)->where('status', 'Izin')->count() ?? '0'; // I
        $content[] = AbsenDetail::where('anak_id', $data->id)->where('status', 'Sakit')->count() ?? '0'; // S
        $content[] = AbsenDetail::where('anak_id', $data->id)->where('status', 'Alpa')->count() ?? '0'; // A

        return $content;
    }

    private function getHeadingLength()
    {
        $columnLetter = '';
        $columnNumber = $this->headingLength;
        while ($columnNumber > 0) {
            $remainder = ($columnNumber - 1) % 26;
            $columnLetter = chr(65 + $remainder) . $columnLetter;
            $columnNumber = (int)(($columnNumber - 1) / 26);
        }
        return $columnLetter;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                // $this->getHighetColumn() = chr(64 + $this->headingLength);
                // dd($this->getHighetColumn());
                // foreach (range('A', $sheet->getHighestColumn()) as $col) {
                //     $sheet->getColumnDimension($col)->setAutoSize(true);
                // }

                // Merge cells for title
                $sheet->mergeCells('A1:'.$this->getHeadingLength().'2');
                $sheet->setCellValue('A1', 'Data Absen');
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

                $sheet->mergeCells('A1:'.$this->getHeadingLength().'2');
                if($this->kelompok){
                    $kelompok = Kelompok::where('id', $this->kelompok)->first();

                    $sheet->mergeCells('A4:G4');
                    $sheet->setCellValue('A4', 'Kelompok : '.$kelompok->nama . ' ('. $kelompok->usia .')');
                }

                
                $bln = Carbon::parse($this->tgl)->translatedFormat('F Y');
                $sheet->mergeCells('A5:G5');
                $sheet->setCellValue('A5', 'Bulan : '.$bln);
                // Set header styles

                // $sheet->setCellValue('A1', 'Data Anak');

                $sheet->getStyle('A7:'.$this->getHeadingLength() . '7')->applyFromArray([
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
    public function columnWidths(): array
    {
        return [
            'A' => 4,
            'B' => 33,
            'C' => 5,
            'D' => 5,         
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
        return 'Data Absen';
    }
}

