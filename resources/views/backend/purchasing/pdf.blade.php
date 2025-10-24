<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .label {
            width: 35mm;
            height: 15mm;
            box-sizing: border-box;
            padding: 3mm 2.5mm;
        }

        .label-frame {
            width: 100%;
            height: 100%;
            border: 0.3mm solid #ffffff;
            border-radius: 1mm;
            padding: 1.5mm;
            box-sizing: border-box;
        }

        table {
            width: 100%;
            height: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: middle;
        }

        .qrcode {
            width: 10mm;
        }

        .qrcode img {
            width: 10mm;
            height: 9mm;
        }

        .text {
            margin-left: 0.5mm;
            width: 33mm;
            /* batasi lebar text agar tidak nempel ke kanan */
            flex-direction: column;
            justify-content: center;
            text-align: center;
            align-items: center;
            padding-right: 7mm;

        }

        .code {
            font-weight: bold;     
            font-size: 6pt;
            text-decoration: underline;
            margin-bottom: 1mm;
            padding-right: 7mm;
        }

        .name {
            font-size: 5pt;
            line-height: 1.2;
            max-height: 36pt;
            /* batasi tinggi (≈2 baris) */
            overflow: hidden;
            text-overflow: ellipsis;
            word-break: break-word;

            display: block;
            text-align: left;
            /* rapikan text rata kiri */
            padding-right: 7mm;
            /* beri space ke kanan */
            box-sizing: border-box;
            /* padding dihitung dalam lebar */
            text-align: center;
            align-items: center;
        }

        .date {
            font-size: 3pt;
            padding-right: 7mm;
        }
    </style>
</head>

<body>
    @foreach ($addedBarcodes as $barcode)
        @for ($i = 0; $i < $barcode->qty; $i++)
            <div class="label">
                <div class="label-frame">
                    <table>
                        <tr>
                            <!-- QR Code -->
                            <td class="qrcode">
                                <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG($barcode->code, 'QRCODE', 5, 5) }}"
                                    alt="QR" />
                            </td>
                            <!-- Text -->
                            <td class="text">
                                <div class="code">{{ $barcode->code }}</div>
                                <div class="name">
                                    {{ \Illuminate\Support\Str::limit($barcode->name, 60, '...') }}</div>
                                <div class="date">Receipt Date: {{ \Carbon\Carbon::parse($barcode->date_po)->format('d/m/Y') }}
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        @endfor
    @endforeach
</body>

</html>
