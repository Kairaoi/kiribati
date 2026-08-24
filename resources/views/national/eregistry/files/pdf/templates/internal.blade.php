<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <title>Internal Memo</title>

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

    </style>
</head>

<body>
    {{-- Header --}}
    <div class="header">
        <img
            src="{{ Auth::user()->ministry?->logo_path
                ? public_path('storage/' . Auth::user()->ministry->logo_path)
                : public_path('images/flag1.png') }}"
            alt="Ministry Logo"
            style="
                width: 110px;
                height: auto;
                object-fit: contain;
                margin-bottom: 10px;
            "
        >
        <div class="gov-title">
            Internal Memorandum
        </div>
    </div>

    {{-- Meta --}}
    <table class="meta-table mb-2">
        <tr>
            <td class="label">From</td>
            <td class="value">
                {{ $file->internal_from_field }}
            </td>

            <td class="label">To</td>
            <td class="value to-cell">
                {{ $file->internal_to_field }}
            </td>
        </tr>

        <tr>
            <td class="label">File Ref</td>
            <td class="value">
                {{ $file->reference_no }}
            </td>

            <td class="label">UFS</td>
            <td class="value">
                {{ $file->ufsOfficer?->name ?? '' }}
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
                {{ $file->internal_cc_field ?? '' }}
            </td>
        </tr>
    </table>

    {{-- Subject --}}
    <div class="subject">
        Subject: {{ $file->subject }}
    </div>

    {{-- Content --}}
    <div class="content">
        {!! $file->content !!}
    </div>

    {{-- Signature --}}
    <div class="signature-section">
        @if($file->signature_path)
            <img
                src="{{ public_path('storage/' . $file->signature_path) }}"
                alt="File Signature"
                class="signature-image"
            >
            <div>
                {{ $file->signedBy?->name ?? '' }}
            </div>
            <div>
                {{ $file->signedBy?->designation ?? '' }}
            </div>
        @endif
    </div>
    
</body>
</html>