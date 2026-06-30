<?php

namespace App\Http\Controllers\National\Eregistry;
use App\Http\Controllers\Controller;

use App\Models\National\Eregistry\FileAssignment;
use App\Models\National\Eregistry\File;
use App\Models\National\Eregistry\FileCirculation;
use App\Models\National\Eregistry\DocumentOverlay;
use App\Models\User;
use App\Repositories\National\Eregistry\DivisionRepository;
use App\Repositories\National\Eregistry\FileCirculationRepository;
use App\Repositories\National\Eregistry\FileRepository;
use App\Repositories\National\Eregistry\MinistryRepository;
use App\Repositories\National\Eregistry\UserRepository;
use auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\PdfOverlayService;


class DocumentOverlayController extends Controller
{

    protected PdfOverlayService $pdfOverlayService;

    public function __construct(PdfOverlayService $pdfOverlayService)
    {
        $this->pdfOverlayService = $pdfOverlayService;
    }


    public function edit(FileCirculation $fileCirculation)
    {
        $fileCirculation->load('file', 'overlays');

        return view('national.eregistry.overlays.edit', [
            'fileCirculation' => $fileCirculation,
            'file' => $fileCirculation->file,
            'overlays' => $fileCirculation->overlays,
            'pdfUrl' => Storage::url($fileCirculation->file->main_file_path),
        ]);
    }

    public function save(Request $request, FileCirculation $fileCirculation)
    {

        $validated = $request->validate([
            'overlays' => ['required', 'array'],
            'overlays.*.id' => ['required', 'exists:document_overlays,id'],
            'overlays.*.x_position' => ['required', 'numeric'],
            'overlays.*.y_position' => ['required', 'numeric'],
            'overlays.*.canvas_height' => ['required', 'numeric'],
            'overlays.*.canvas_width' => ['required', 'numeric'],
        ]);

        foreach ($validated['overlays'] as $overlayData) {
            DocumentOverlay::where('id', $overlayData['id'])
                ->where('file_circulation_id', $fileCirculation->id)
                ->where('is_locked', false)
                ->update([
                    'x_position' => $overlayData['x_position'],
                    'y_position' => $overlayData['y_position'],
                    'canvas_height' => $overlayData['canvas_height'],
                    'canvas_width' => $overlayData['canvas_width'],
                ]);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function finalize(FileCirculation $fileCirculation)
    {
        $renderedPath = $this->pdfOverlayService->render($fileCirculation);

        $fileCirculation->overlays()->update([
            'is_locked' => true,
        ]);

        return redirect()
            ->route('registry.files.show', $fileCirculation->file_id)
            ->with('success', 'Final PDF generated successfully.');
    }

    
}
