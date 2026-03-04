<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Labels PDF</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 8mm 10mm 8mm;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10pt;
        }
        .sheet {
            width: 100%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td.label-cell {
            width: 20%;
            height: 34mm;
            vertical-align: top;
            padding: 2mm 3mm;
            
        }
        .item-name { font-weight: bold; font-size: 10pt; }
        .item-price { font-size: 9pt; margin-top:6px; }
        .page-break { page-break-after: always; }
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
