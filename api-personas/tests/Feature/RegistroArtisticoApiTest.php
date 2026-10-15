<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistroArtisticoApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_registro_artistico_successfully(): void
    {
        $payload = [
            'disciplina_artistica' => 'Pintura al Óleo',
            'nivel_artistico'      => 'Avanzado',
        ];

        $response = $this->postJson('/api/registro-artistico', $payload);

        $response->assertStatus(201)
                 ->assertJson([
                     'mensaje' => 'Registro artístico creado exitosamente',
                     'data'    => [
                         'disciplina_artistica' => 'Pintura al Óleo',
                         'nivel_artistico'      => 'Avanzado',
                     ]
                 ]);

        $this->assertDatabaseHas('registro_artisticos', [
            'disciplina_artistica' => 'Pintura al Óleo',
            'nivel_artistico'      => 'Avanzado',
        ]);
    }

    public function test_can_create_registro_artistico_with_optional_field_null(): void
    {
        $payload = [
            'disciplina_artistica' => 'Música',
        ];

        $response = $this->postJson('/api/registro-artistico', $payload);

        $response->assertStatus(201)
                 ->assertJson([
                     'mensaje' => 'Registro artístico creado exitosamente',
                     'data'    => [
                         'disciplina_artistica' => 'Música',
                     ]
                 ]);

        $this->assertDatabaseHas('registro_artisticos', [
            'disciplina_artistica' => 'Música',
            'nivel_artistico'      => null,
        ]);
    }

    public function test_validates_required_disciplina_artistica(): void
    {
        $response = $this->postJson('/api/registro-artistico', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['disciplina_artistica']);
    }

    public function test_validates_max_length_constraints(): void
    {
        $response = $this->postJson('/api/registro-artistico', [
            'disciplina_artistica' => str_repeat('a', 81),
            'nivel_artistico'      => str_repeat('b', 41),
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['disciplina_artistica', 'nivel_artistico']);
    }
}
