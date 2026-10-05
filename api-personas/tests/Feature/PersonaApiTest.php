<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PersonaApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_persona_successfully(): void
    {
        $payload = [
            'nombres' => 'Ana María',
            'apellidos' => 'Gómez Pérez',
            'email' => 'ana.gomez@ejemplo.com',
            'telefono' => '3001234567',
        ];

        $response = $this->postJson('/api/personas', $payload);

        $response->assertStatus(201)
                 ->assertJson([
                     'mensaje' => 'Persona registrada exitosamente',
                     'data' => [
                         'nombres' => 'Ana María',
                         'apellidos' => 'Gómez Pérez',
                         'email' => 'ana.gomez@ejemplo.com',
                         'telefono' => '3001234567',
                     ]
                 ]);

        $this->assertDatabaseHas('personas', [
            'email' => 'ana.gomez@ejemplo.com'
        ]);
    }

    public function test_validates_required_fields(): void
    {
        $response = $this->postJson('/api/personas', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['nombres', 'apellidos', 'email']);
    }
}
