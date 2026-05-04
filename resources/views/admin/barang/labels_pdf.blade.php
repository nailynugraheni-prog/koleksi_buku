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
            font-family: DejaVu Sans, sans-serif;
        }

        .sheet {
            width: 210mm;
            height: 167mm;
            page-break-after: always;
            box-sizing: border-box;
        }

        table {
            border-collapse: separate;
            border-spacing: 2mm 2mm;
            margin: 0 auto;
            table-layout: fixed;
        }

        td {
            width: 38mm;
            height: 18mm;
            background: #ffffff;
            border: none;
            border-radius: 10px;
            text-align: center;
            vertical-align: middle;
            padding: 0.4mm;
            box-sizing: border-box;
            overflow: hidden;
        }

        .item-name {
            font-size: 6pt;
            font-weight: bold;
            line-height: 1;
            margin-bottom: 0.2mm;
        }

        .barcode {
            line-height: 0;
            margin: 0;
        }

        .barcode img {
            display: block;
            margin: 0 auto;
            width: 78%;
            height: auto;
        }

        .kode {
            font-size: 5pt;
            line-height: 1;
            margin-top: 0.2mm;
        }

        .item-price {
            font-size: 7pt;
            font-weight: bold;
            line-height: 1;
            margin-top: 0.2mm;
        }
    </style>
</head>
<body>
    @php
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
    @endphp

    @foreach($pages as $pIndex => $page)
        <div class="sheet">
            <table>
                <tbody>
                @php $pos = 0; @endphp
                @for($r = 0; $r < $rows; $r++)
                    <tr>
                    @for($c = 0; $c < $columns; $c++)
                        @php $cell = $page[$pos] ?? null; @endphp
                        <td>
                            @if($cell)
                                <div class="item-name">{{ $cell->nama }}</div>

                                @php
                                    $barcode = base64_encode(
                                        $generator->getBarcode(
                                            (string) $cell->id_barang,
                                            $generator::TYPE_CODE_128,
                                            1.0,
                                            10
                                        )
                                    );
                                @endphp

                                <div class="barcode">
                                    <img src="data:image/png;base64,{{ $barcode }}" alt="barcode">
                                </div>

                                <div class="kode">ID: {{ $cell->id_barang }}</div>
                                <div class="item-price">Rp {{ number_format($cell->harga ?? 0,0,',','.') }}</div>
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