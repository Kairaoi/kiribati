<?php

namespace App\Services;

use App\Models\National\Eregistry\FileCirculation;
use App\Models\National\Eregistry\DocumentOverlay;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;


class PdfOverlayService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function render(FileCirculation $fileCirculation): string
    {
        $file = $fileCirculation->file;

        $sourcePdf = $file->main_file_path;

        $overlays = $fileCirculation->overlays()
            ->orderBy('page_number')
            ->get();

        $outputPath = sprintf(
            'rendered-files/file-%s/circulation-%s-final.pdf',
            $file->id,
            $fileCirculation->id
        );

        Storage::disk('public')->makeDirectory(
            dirname($outputPath)
        );

        $pdf = new Fpdi();

        $pageCount = $pdf->setSourceFile(
            Storage::disk('public')->path($sourcePdf)
        );

        for ($page = 1; $page <= $pageCount; $page++) {

            $templateId = $pdf->importPage($page);
            $size = $pdf->getTemplateSize($templateId);

            $pdf->AddPage(
                $size['orientation'],
                [$size['width'], $size['height']]
            );

            $pdf->useTemplate($templateId);

            $pageOverlays = $overlays->where(
                'page_number',
                $page
            );

            foreach ($pageOverlays as $overlay) {
                $this->applyOverlay($pdf, $overlay);
            }
        }

        $fullPath = Storage::disk('public')->path($outputPath);

        $pdf->Output('F', $fullPath);

        $fileCirculation->update([
            'rendered_pdf_path' => $outputPath,
            'rendered_pdf_at'   => now(),
            'updated_by'        => auth()->id(),
        ]);

        return $outputPath;
    }


    protected function applyOverlay(Fpdi $pdf, DocumentOverlay $overlay): void
    {
        switch ($overlay->overlay_type) {

            case 'review_comment':

                $content = $overlay->content;
                $status = strtoupper($content['status'] ?? '');
                $date = $content['date'] ?? '';
                $comment = $content['comment'] ?? '';
                $status = $content['status'] ?? '';
                $reference = $content['reference'] ?? '';
                $approvedBy = $content['approved_by' ?? ''];
                $designation = $content['designation'] ?? '';

                // dd($comment);
                $pageWidth = $pdf->GetPageWidth();
                $pageHeight = $pdf->GetPageHeight();

                $canvasWidth = $overlay->content['canvas_width'] ?? 794;
                $canvasHeight = $overlay->content['canvas_height'] ?? 1123;

                $scaleX = $pageWidth / $canvasWidth;
                $scaleY = $pageHeight / $canvasHeight;

                $x = $overlay->x_position * $scaleX;
                $y = $overlay->y_position * $scaleY;
                $w = $overlay->width * $scaleX;
                $h = $overlay->height * $scaleY;
                // dd([$x, $y, $w]);
                $padding = 8;
                
                $pdf->SetAutoPageBreak(false);

                // Border box
                $pdf->Rect($x, $y, $w, $overlay->height);

                // Status
                $pdf->SetXY($x, $y);
                $pdf->SetFont('Helvetica', 'B', 20);
                $pdf->Cell($x, $y, $status);

                // Details
                $detailsY = $pdf->GetY() + 4;

                // $pdf->SetXY($x + $padding, $detailsY);
                // $pdf->SetFont('Helvetica', '', $overlay->font_size ?? 13);

                $text = $comment .
                        "\nApproved by: " . $approvedBy .
                        "\nDesignation: " . $designation .
                        "\nDate: " . $date .
                        "\nRef No: " . $reference;

                $pdf->SetXY($x, $y);
                $pdf->SetFont('Helvetica', 'B', 11);
                $pdf->MultiCell($y, $y, $text);

                // Signature
                // if ($signaturePath) {
                //     $path = Storage::disk('public')->path($signaturePath);
                //     if (file_exists($path)) {
                //         $pdf->Image(
                //             $path,
                //             $x + $padding,
                //             $pdf->GetY() + 3,
                //             40
                //         );
                //     }
                // }

                break;

            case 'signature':

                $pdf->Image(
                    Storage::disk('public')->path($overlay->content['path']),
                    $overlay->x_position,
                    $overlay->y_position,
                    $overlay->width,
                    $overlay->height
                );

                break;

            case 'stamp':

                $pdf->Image(
                    Storage::disk('public')->path($overlay->content['path']),
                    $overlay->x_position,
                    $overlay->y_position,
                    $overlay->width,
                    $overlay->height
                );

                break;

            case 'text':

                $pdf->SetFont('Helvetica', '', $overlay->font_size ?? 12);

                $pdf->SetXY(
                    $overlay->x_position,
                    $overlay->y_position
                );

                $pdf->MultiCell(
                    $overlay->width,
                    5,
                    $overlay->content['text']
                );

                break;

            case 'status':

                $pdf->SetFont('Helvetica', 'B', 12);

                $pdf->SetXY(
                    $overlay->x_position,
                    $overlay->y_position
                );

                $pdf->MultiCell(
                    $overlay->width,
                    6,
                    strtoupper($overlay->content['status'])
                );

                break;
        }
    }
}
