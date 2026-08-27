<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>

<body>
    <style>
        @page {
            margin: 35px 45px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #000;
            
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 5px;
        }

        .gov-title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .memo-title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 4px;
        }

        .ministry-title {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 4px;
        }

        .contact {
            font-size: 10px;
            margin-top: 4px;
        }

        
       .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
           
        }

        .meta-table td {
            border: 1px solid #bfc5cc;
            padding: 2px 2px;
            vertical-align: top;
        }

        .meta-table .label {
            width: 16%;
            font-weight: 700;
            color: #111827;
            white-space: nowrap;
            padding: 2px 3px;
        }

        .meta-table .value {
            color: #1f2937;
            padding: 2px 2px;
            word-break: break-word;
        }

        .meta-table .to-cell {
            line-height: 1.45;
        }

        .meta-table .muted {
            color: #6b7280;
        }
    
        .label {
            font-weight: bold;
            width: 70px;
        }

        .subject {
            font-weight: bold;
            margin-top: 16px;
            margin-bottom: 18px;
            font-size: 14px;
        }

        .content p {
            margin: 0 0 0 0;
            line-height: 1.3;
        }

         .signature-section {
            margin-top: 30px;
        }

        .signature-image {
            height: 70px;
            width: auto;
            display: block;
            margin-bottom: 5px;
        }

        .signatory-name {
            font-weight: bold;
        }

        .content table {
            width: 100%;
            border-collapse: collapse;
        }

        .content table,
        .content th,
        .content td {
            border: 0.5px solid #000;
        }

        .content th,
        .content td {
            padding: 4px;
            vertical-align: top;
        }

    </style>

    <div class="header">
        {{-- Flag --}}
        <div style="margin-bottom: 8px;">
            <img
                src="{{ public_path('images/flag1.png') }}"
                style="width: 90px; height: auto;"
            >
        </div>

        <div class="gov-title">
            GOVERNMENT OF KIRIBATI
        </div>

        <div class="memo-title">
            MEMORANDUM
        </div>

        <div class="ministry-title">
            {{ strtoupper($file->ministry->name ?? 'MINISTRY') }}
        </div>

        <div class="contact">
            P.O. Box {{ $file->ministry->po_box ?? '' }},
            {{ $file->ministry->address ?? 'Betio, Tarawa, KIRIBATI' }} KIRIBATI
            P:(+686) {{ $file->ministry->phone ?? '' }}
            E: {{ $file->ministry->email ?? '' }}
            W: {{ $file->ministry->website ?? '' }}
        </div>
    </div>

    <table class="meta-table mb-4">
        <tr>
            <td class="label">From</td>
            <td class="value">
                {{ $file->memo_from_field ?? 'Secretary' }}
            </td>

            <td class="label">To</td>
            <td class="value to-cell">
                @if($isAllMinistries)
                    All Secretaries, Chairperson, Chief Justice, Commissioner of Police, Clerk to Parliament, Auditor General, Attorney General
                @elseif($showRecipientListAtEnd)
                    See distribution list below.
                @else
                    @foreach ($recipients as $ministry)
                        {{ $ministry->reviewer_title }} - {{ $ministry->code }}
                        @if (!$loop->last)
                            <br>
                        @endif
                    @endforeach
                @endif
            </td>
        </tr>

        <tr>
            <td class="label">File Ref</td>
            <td class="value">
                {{ $file->reference_no }}
            </td>
            <td class="label">Attention</td>
            <td class="value">
                {{ $file->memo_attention_to ?? '' }}
            </td>
        </tr>
        <tr>
            <td class="label">Date</td>

            <td class="value">
                {{ $file->letter_date
                    ? \Carbon\Carbon::parse($file->letter_date)->format('d/m/Y')
                    : now()->format('d/m/Y') }}
            </td>

            <td class="label">Cc</td>

            <td class="value">
                {{ $file->memo_cc_field ?? '' }}
            </td>
        </tr>
    </table>

    <div class="subject">
        Subject:   {{ $file->subject }}
    </div>

    <div class="content">
        {!! $file->content !!}
    </div>

    @if($file->signature_path)
            <div class="signature-section">
                <img
                    src="{{ public_path('storage/' . $file->signature_path) }}"
                    alt="Signature"
                    class="signature-image">
                <div class="signatory-name">
                    {{ $file->signedBy?->name }}
                </div>
                <div>
                    {{ $file->signedBy?->designation ?? '' }}
                </div>
                <div style="font-size: 12px; color: #666;">
                    Signed Electronically
                </div>
            </div>
    @endif
            
    {{-- Distribution List --}}
    @if($showRecipientListAtEnd)
        <div style="margin-top: 60px; page-break-inside: avoid;">
            <h4 style="
                font-size: 13px;
                font-weight: bold;
                margin-bottom: 10px;
                text-transform: uppercase;
            ">
                TO Distribution List
            </h4>
            <table style="width:100%; border-collapse: collapse; font-size:12px;">
                @foreach($recipients as $recipient)
                    <tr>
                        <td>
                            {{ $recipient->reviewer_title }} {{ $recipient->code }}
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

</body>
</html>