import * as pdfjsLib from 'pdfjs-dist';
import Konva from 'konva';

import pdfWorker from 'pdfjs-dist/build/pdf.worker.min.mjs?url';

pdfjsLib.GlobalWorkerOptions.workerSrc = pdfWorker;

const canvas = document.getElementById('pdf-canvas');
const context = canvas.getContext('2d');

const pdfUrl = window.overlayConfig?.pdfUrl;

console.log('PDF URL:', pdfUrl);

if (!pdfUrl) {
    throw new Error('PDF URL is missing.');
}

const pdf = await pdfjsLib.getDocument({url: pdfUrl}).promise;

const page = await pdf.getPage(1);

const scale = 1.5;
const viewport = page.getViewport({ scale });

canvas.width = viewport.width;
canvas.height = viewport.height;

await page.render({
    canvasContext: context,
    viewport: viewport
}).promise;

const outputScale = window.devicePixelRatio || 1;

canvas.width = Math.floor(viewport.width * outputScale);
canvas.height = Math.floor(viewport.height * outputScale);

canvas.style.width = `${viewport.width}px`;
canvas.style.height = `${viewport.height}px`;

const transform = outputScale !== 1
    ? [outputScale, 0, 0, outputScale, 0, 0]
    : null;

await page.render({
    canvasContext: context,
    viewport,
    transform,
}).promise;

const container = document.getElementById('konva-container');
container.innerHTML = '';

const stage = new Konva.Stage({
    container: 'konva-container',
    width: viewport.width,
    height: viewport.height,
});

const layer = new Konva.Layer();
stage.add(layer);

const overlays = window.overlayConfig?.overlays;

if (!overlays) {
    throw new Error('Overlays data is missing.');
}

layer.destroyChildren();

overlays.forEach(item => {

    const data = JSON.parse(item.content);

    const group = new Konva.Group({
        x: Number(item.x_position),
        y: Number(item.y_position),
        draggable: !item.is_locked,
        id: String(item.id),
    });

    const signatureHeight = data.signature_path ? 60 : 0;
    const signatureGap = data.signature_url ? 12 : 0;
    const padding = 10;

    // const text = new Konva.Text({
    //     text: content,
    //     x: padding,
    //     y: padding,
    //     width: Number(item.width ?? 350) - (padding * 2),
    //     fontSize: Number(item.font_size) || 14,
    //     align: 'left',
    //     listening: false,
    // });

    const statusColor = {
    APPROVED: '#16a34a', // green
    REJECTED: '#dc2626', // red
    REVIEWED: '#2563eb', // blue
    RECEIVED: '#ea580c', // orange
}[data.status?.toUpperCase()] ?? '#000';

    const statusText = new Konva.Text({
        x: padding,
        y: padding,
        text: (data.status ?? '').toUpperCase(),
        width: Number(item.width ?? 350) - (padding * 2),
        fontSize: 20,
        fontStyle: 'bold',
       
        listening: false,
    });

    const detailsText = new Konva.Text({
        x: padding,
        y: padding + statusText.height() + 10,
        width: Number(item.width ?? 350) - (padding * 2),
        text:
            (data.comment ?? '') +
            '\n\nApproved by: ' + (data.approved_by ?? '') +
            '\nDesignation: ' + (data.designation ?? '') +
            '\n\nDate: ' + (data.date ?? '') +
            '\nReference: ' + (data.reference ?? ''),
        fontSize: Number(item.font_size ?? 13),
        fill: '#000',
        listening: false,
    });


    const boxHeight =
        statusText.height() + detailsText.height() + signatureGap + signatureHeight + (padding * 2);

        const box = new Konva.Rect({
        width: Number(item.width ?? 350),
        height: boxHeight,
        stroke: item.overlay_type.includes('approved') ? 'green' : 'black',
        strokeWidth: 1,
     
    });

    group.add(box);
    group.add(statusText);
    group.add(detailsText);

    // Signature block goes here
    if (data.signature_path) {
        const imageObj = new Image();
        imageObj.onload = function () {
            const signature = new Konva.Image({
                image: imageObj,
                x: 10,
                y: statusText.height() + detailsText.height() + signatureGap + padding,
                width: 120,
                height: 60,
            });
            group.add(signature);
            layer.draw();
        };
        imageObj.src = '/storage/' + data.signature_path;
    }

    group.on('dragend', async () => {
        await fetch(saveUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify({
                overlay_id: item.id,
                x_position: group.x(),
                y_position: group.y(),
            }),
        });
    });

    layer.add(group);
});

layer.draw();