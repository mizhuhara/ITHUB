<?php

namespace App\Http\Controllers;

use App\Models\EventRegistration;
use Dompdf\Dompdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    /**
     * Generate and download the participant certificate as a PDF.
     */
    public function download(string $ticketCode): Response
    {
        $registration = EventRegistration::with('event')
            ->where('ticket_code', $ticketCode)
            ->firstOrFail();

        $event = $registration->event;

        abort_unless($event->certificateAvailableFor($registration), 404);

        $disk = Storage::disk('public');
        $templatePath = $event->certificate_template;

        abort_unless($templatePath && $disk->exists($templatePath), 404);

        $imageSize = getimagesize($disk->path($templatePath));

        abort_if($imageSize === false, 404);

        [$width, $height] = $imageSize;
        $widthPt = $width * 0.75;
        $heightPt = $height * 0.75;

        $html = view('certificates.pdf', [
            'event' => $event,
            'registration' => $registration,
            'imageData' => base64_encode($disk->get($templatePath)),
            'imageMime' => $disk->mimeType($templatePath) ?: 'image/png',
            'widthPt' => $widthPt,
            'heightPt' => $heightPt,
        ])->render();

        $dompdf = new Dompdf;
        $dompdf->loadHtml($html);
        $dompdf->setPaper([0, 0, $widthPt, $heightPt]);
        $dompdf->render();

        $filename = 'Sertifikat-'.preg_replace('/[^A-Za-z0-9]+/', '-', $registration->name).'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
