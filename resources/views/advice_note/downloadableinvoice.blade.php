<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GeMEMIS exhibition invoice</title>
    <style>
        @page { margin: 15mm; }
        body { margin: 0; }
        {!! file_get_contents(public_path('assets/css/exhibition-invoice.css')) !!}
        .geita-invoice { padding: 0; border: 0; }
    </style>
</head>
<body>
    @foreach ($adviceNotes as $item)
        @include('advice_note.invoice', ['invoicePdf' => true])
    @endforeach
</body>
</html>
