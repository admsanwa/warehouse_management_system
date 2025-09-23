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
            width: 80mm;
            height: 40mm;
            box-sizing: border-box;
            padding: 4mm 7mm;
        }

        .label-frame {
            width: 90%;
            height: 90%;
            border: 0.3mm solid #ffffff;
            border-radius: 1mm;
            padding: 1mm 2mm;
            box-sizing: border-box;
        }

        table {
            width: 90%;
            height: 90%;
            border-collapse: collapse;
        }

        td {
            vertical-align: middle;
        }

        .qrcode {
            width: 32mm;
        }

        .qrcode img {
            width: 30mm;
            height: 30mm;
        }

        .text {
            margin-left: 1mm;
            width: 57mm;
            /* batasi lebar text agar tidak nempel ke kanan */
            flex-direction: column;
            justify-content: center;
            text-align: center;
            align-items: center;
            padding-right: 8mm;

        }

        .code {
            font-weight: bold;
            font-size: 13pt;
            text-decoration: underline;
            margin-bottom: 1mm;
            padding-right: 8mm;
        }

        .name {
            font-size: 11pt;
            line-height: 1.2;
            max-height: 28pt;
            /* batasi tinggi (≈2 baris) */
            overflow: hidden;
            text-overflow: ellipsis;
            word-break: break-word;

            display: block;
            text-align: center;
            /* rapikan text rata kiri */
            padding-right: 5mm;
            /* beri space ke kanan */
            box-sizing: border-box;
            /* padding dihitung dalam lebar */
            align-items: center;
            margin-bottom: 2mm;
            padding-right: 8mm;

        }

        .date {
            font-size: 8pt;
            text-decoration: underline;
        }

        .po {
            font-size: 8pt;
            margin: 2;
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
                                    {{ \Illuminate\Support\Str::limit($barcode->name, 80, '...') }}
                        </tr>
                    </table>
                </div>
            </div>
        @endfor
    @endforeach
</body>

</html>
