<?php

use App\Models\ArticuloLey;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;

Route::get('/', function () {
    $publications = ArticuloLey::where('estado', 'Activo')
        ->orderBy('fecha_publicacion', 'desc')
        ->get()
        ->map(function ($item) {
            $hasFile = ! empty($item->archivo_pdf) && $item->getFilePath() !== null;
            $fileType = $item->file_type ?: 'PDF';

            // Formato de fecha en español
            $dateFormatted = '';
            if ($item->fecha_publicacion) {
                $meses = [
                    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
                ];
                $dia = $item->fecha_publicacion->format('d');
                $mes = $meses[(int) $item->fecha_publicacion->format('m')] ?? $item->fecha_publicacion->format('M');
                $anio = $item->fecha_publicacion->format('Y');
                $dateFormatted = "{$dia} de {$mes}, {$anio}";
            }

            return [
                'id' => $item->id,
                'codigo' => $item->codigo,
                'title' => $item->titulo,
                'author' => $item->autor ?: 'Institucional / Estado',
                'date' => $dateFormatted,
                'description' => $item->descripcion,
                'category' => ($item->tipo === 'LEY') ? 'normas' : 'articulos',
                'tipo' => $item->tipo,
                'tipo_label' => ($item->tipo === 'LEY') ? 'Ley / Código' : 'Artículo Doctrinal',
                'has_file' => $hasFile,
                'file_type' => $fileType,
                'pdf' => $hasFile ? route('articulos.descargar', $item->id) : null,
            ];
        });

    return Inertia::render('Welcome', [
        'publications' => $publications,
    ]);
})->name('home');

Route::get('/articulos/{id}/descargar', function (int $id) {
    $articulo = ArticuloLey::findOrFail($id);
    $filePath = $articulo->getFilePath();

    if (! $filePath || ! file_exists($filePath)) {
        abort(404, 'El archivo solicitado no se encuentra disponible para descarga.');
    }

    $ext = pathinfo($filePath, PATHINFO_EXTENSION);
    $slug = Str::slug(mb_substr($articulo->titulo, 0, 45));
    $downloadName = "{$articulo->codigo}_{$slug}.{$ext}";

    return response()->download($filePath, $downloadName);
})->name('articulos.descargar');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
