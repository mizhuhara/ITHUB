@php
    $leftPt = ($event->certificate_name_x / 100) * $widthPt;
    $topPt = ($event->certificate_name_y / 100) * $heightPt;
    $fontSize = $event->certificate_name_font_size;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; }
        html, body {
            margin: 0;
            padding: 0;
            width: {{ $widthPt }}pt;
            height: {{ $heightPt }}pt;
        }
        .certificate-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: {{ $widthPt }}pt;
            height: {{ $heightPt }}pt;
        }
        .participant-name {
            position: absolute;
            top: {{ $topPt }}pt;
            left: {{ $leftPt }}pt;
            width: {{ $widthPt }}pt;
            margin-left: -{{ $widthPt / 2 }}pt;
            margin-top: -{{ $fontSize / 2 }}pt;
            text-align: center;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: {{ $fontSize }}pt;
            font-weight: bold;
            color: {{ $event->certificate_name_color }};
        }
    </style>
</head>
<body>
    <img class="certificate-bg" src="data:{{ $imageMime }};base64,{{ $imageData }}" alt="">
    <div class="participant-name">{{ $registration->name }}</div>
</body>
</html>
