<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Official Letter</title>
    <style>
        @page {
            margin: 40px 50px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            line-height: 1.7;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }

        .flag {
            width: 55px;
            height: auto;
            margin-bottom: 8px;
        }

        .gov-title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .ministry-title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .contact {
            font-size: 10px;
        }

        .top-meta {
            width: 100%;
            font-size: 14px;
        }

        .top-meta td {
            padding: 4px 0;
            vertical-align: top;
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
            font-size: 14px;
        }

        .subject {
            margin-top: 15px;
            margin-bottom: 20px;
            font-weight: bold;
            font-size: 13px;
        }


        .content p {
            margin: 0 0 0 0;
            line-height: 1.3;
            font-size: 13px;
        }

        .signature-section {
            text-align: left;                        
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
@foreach($recipientCopies as $recipient)

    {{-- Header --}}
    <div class="header">
        <img
            src="{{ public_path('images/flag1.png') }}"
            alt=""
            class="flag"
        >
        <div class="gov-title">
            GOVERNMENT OF KIRIBATI
        </div>
        <div class="ministry-title">
            {{ strtoupper($file->ministry->name ?? '') }}
        </div>
        <div class="contact">
            P.O. Box {{ $file->ministry->po_box ?? '' }},
            {{ $file->ministry->address ?? '' }}
            P: (686)
            {{ $file->ministry->phone ?? '' }}
            E:
            {{ $file->ministry->email ?? '' }}
            W:
            {{ $file->ministry->website ?? '' }}
        </div>
    </div>

    {{-- Meta --}}
    <table class="top-meta">
        <tr>
            <td style="width: 50%;">
                <span class="label">File Ref:</span>
                {{ $file->reference_no }}
            </td>

            <td style="width: 50%; text-align: right;">
                <span class="label">Date:</span>
                {{ $file->letter_date
                    ? \Carbon\Carbon::parse($file->letter_date)->format('d F Y')
                    : now()->format('d F Y') }}
            </td>
        </tr>
    </table>

    {{-- Recipient --}}
    <div class="to-section">
        <p><strong>To:</strong>{{ $recipient->name }}</p>
    </div>

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

                <div style="font-size: 12px; color: #668;">
                    Approved Electronically
                </div>
            </div>
        @endif

    </div>

    @if(!$loop->last)
        <div style="page-break-after: always;"></div>
    @endif

@endforeach
    
</body>


</html>