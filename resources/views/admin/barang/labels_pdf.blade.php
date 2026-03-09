<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Labels PDF</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            margin: 0;
            width: 210mm;
            height: 167mm;
        }

        
        table {
            border-collapse: separate;
            border-spacing: 2mm 2mm; /* 0,3 cm samping | 0,2 cm bawah */
            margin: 0 auto;
        }

        td {
            width: 38mm;   /* 3,8 cm */
            height: 18mm;  /* 1,8 cm */
            background: #ffffff; /* label putih */
            border: 0.3 px solid #000; /* border per label */
            border-radius: 10px; /* lengkungan */
            text-align: center;
            vertical-align: middle;
        }

       
        .nama {
            font-size: 7pt;
            font-weight: bold;
            line-height: 1.1;
        }

        .kode {
            font-size: 6pt;
        }

        .harga {
            font-size: 9pt;
            font-weight: bold;
        }

        .footer {
            font-size: 5pt;
        }
    </style>
</head>
<body>
    @foreach($pages as $pIndex => $page)
        <div class="sheet">
            <table>
                <tbody>
                @php $pos = 0; @endphp
                @for($r = 0; $r < $rows; $r++)
                    <tr>
                    @for($c = 0; $c < $columns; $c++)
                        @php $cell = $page[$pos] ?? null; @endphp
                        <td class="label-cell">
                            @if($cell)
                                <div class="item-name">{{ $cell->nama }}</div>
                                <div>ID: {{ $cell->id_barang }}</div>
                                <div class="item-price">Rp {{ number_format($cell->harga ?? 0,0,',','.') }}</div>
                            @else
                                {{-- kosong --}}
                            @endif
                        </td>
                        @php $pos++; @endphp
                    @endfor
                    </tr>
                @endfor
                </tbody>
            </table>
        </div>


        @if($pIndex + 1 < count($pages))
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>
