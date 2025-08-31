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
            height: 30mm;
            box-sizing: border-box;
            padding: 3mm 5mm;
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
            width: 28mm;
            height: 28mm;
        }

        .text {
            margin-left: 1mm;
            width: 58mm;
            /* batasi lebar text agar tidak nempel ke kanan */
            flex-direction: column;
            justify-content: center;
            text-align: center;
            align-items: center;
            padding-right: 10mm;

        }

        .code {
            font-weight: bold;
            font-size: 13pt;
            text-decoration: underline;
            margin-bottom: 1mm;
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
            /* ✅ jarak ke bawah */

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
                <table>
                    <tr>
                        <!-- QR Code -->
                        <td class="qrcode">
                            <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG($barcode->prod_no, 'QRCODE', 5, 5) }}"
                                alt="QR" />
                        </td>
                        <!-- Text -->
                        <td class="text">
                            <div class="code">{{ $barcode->prod_no }}</div>
                            <div class="name">
                                {{ \Illuminate\Support\Str::limit($barcode->prod_desc, 60, '...') }}</div>
                            <div class="date ">{{ \Carbon\Carbon::parse($barcode->updated_at)->format('d/m/Y') }}
                            </div>
                            <div class="po ">{{ $barcode->doc_num }}</div>
                    </tr>
                </table>
            </div>
        @endfor
    @endforeach
</body>

</html>
