<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <title>Internal Memo</title>

    <style>
        @page {
            margin: 40px 50px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.7;
            color: #000;
        }

        .header {
            text-align: center;
            padding-bottom: 10px;
        }

        .flag {
            width: 55px;
            height: auto;
            margin-bottom: 8px;
        }

        .gov-title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .ministry-title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .contact {
            font-size: 10px;
            margin-top: 8px;
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
            width: 90px;
        }

        .date {
            text-align: right;
        }

        .to-section {
            margin-bottom: 20px;
        }

        .subject {
            margin-top: 10px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .greeting {
            margin-top: 15px;
            margin-bottom: 20px;
            font-size: 16px;
        }

        

        .content p {
            margin: 0 0 0 0;
            line-height: 1.3;
        }

       
        .signature-section {
            margin-top: 60px;
        }

        .signature-image {
            height: 70px;
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
            src="{{ auth()->user()->ministry?->logo_path
                ? public_path('storage/' . auth()->user()->ministry->logo_path)
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

        <div style="margin-bottom: 15px;">
            Sincerely,
        </div>

        @if($file->approver?->signature_path)
            <img
                src="{{ public_path('storage/' . $file->approver->signature_path) }}"
                class="signature-image"
            >
        @endif

    </div>

</body>
</html>