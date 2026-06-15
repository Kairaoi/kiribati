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
