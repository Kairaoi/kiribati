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
            margin-bottom: 20px;
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
            margin-top: 6px;
        }

        .contact {
            font-size: 10px;
            margin-top: 4px;
        }

        
       .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 13px;
            line-height: 1.35;
        }

        .meta-table td {
            border: 1px solid #bfc5cc;
            padding: 2px 3px;
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
            padding: 2px 3px;
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
        }

        .signature-image {
            height: 60px;
            margin-bottom: 5px;
        }

        .signatory-name {
            font-weight: bold;
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

    @php
        $recipients = $file->memoRecipients();
        $showRecipientListAtEnd = $recipients->count() > 4;
    @endphp

    <table class="meta-table mb-4">
        <tr>
            <td class="label">From</td>
            <td class="value">
                {{ $file->memo_from_field ?? 'Secretary' }}
            </td>

            <td class="label">To</td>
            <td class="value to-cell">
                @if($showRecipientListAtEnd)
                    See distribution list below.
                @else
                    @foreach($recipients as $recipient)
                        {{ $recipient->reviewer_title }} ({{ $recipient->code }})
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

    @if($fileCirculation?->rendered_pdf_path && file_exists(public_path('storage/' . $fileCirculation->rendered_pdf_path)))
        {{-- Final Rendered PDF Exists --}}
        <iframe
            src="{{ asset('storage/' . $fileCirculation->rendered_pdf_path) }}"
            width="100%"
            height="900px"
            style="border: none;">
        </iframe>
    @else
        {{-- Live Signature Preview --}}
        @if($fileCirculation?->signature_path)
            <div class="signature-section">

                <img
                    src="{{ public_path('storage/' . $fileCirculation->signature_path) }}"
                    alt="Signature"
                    class="signature-image">

                <div class="signatory-name">
                    {{ $fileCirculation->signedBy?->first_name }}
                    {{ $fileCirculation->signedBy?->last_name }}
                </div>

                <div>
                    {{ $fileCirculation->signedBy?->designation ?? '' }}
                </div>

                <div style="font-size: 12px; color: #666;">
                    Approved Electronically
                </div>

            </div>
        @endif
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