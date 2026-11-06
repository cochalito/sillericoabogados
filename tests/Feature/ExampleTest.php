<?php

namespace Tests\Feature;

use App\Models\ArticuloLey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_a_successful_response()
    {
        $response = $this->get(route('home'));

        $response->assertOk();
    }

    public function test_home_page_renders_with_publications()
    {
        ArticuloLey::create([
            'codigo' => 'ART-001',
            'tipo' => 'ARTICULO',
            'autor' => 'Test Autor',
            'titulo' => 'Test Titulo',
            'descripcion' => 'Test Descripcion',
            'fecha_publicacion' => now(),
            'estado' => 'Activo',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
    }

    public function test_download_non_existent_article_returns_404()
    {
        $response = $this->get(route('articulos.descargar', 99999));

        $response->assertNotFound();
    }

    public function test_artisan_migrate_route_requires_valid_token()
    {
        $response = $this->get(route('artisan.migrate', 'invalid-token'));

        $response->assertForbidden();
    }
}
