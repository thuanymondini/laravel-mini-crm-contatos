<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Infrastructure\Contact\Eloquent\ContactModel;

class ContactCrudTest extends TestCase
{
    use RefreshDatabase;

    // ─── POST /api/contacts ─────────────────────────────────

    /** @test */
    public function test_creates_a_contact(): void
    {
        $payload = [
            'name'  => 'João Silva',
            'email' => 'joao@empresa.com.br',
            'phone' => '11999999999',
        ];

        $response = $this->postJson('/api/contacts', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name'   => 'João Silva',
                'email'  => 'joao@empresa.com.br',
                'score'  => 0,
                'status' => 'pending',
            ]);

        $this->assertDatabaseHas('contacts', [
            'email'  => 'joao@empresa.com.br',
            'status' => 'pending',
            'score'  => 0,
        ]);
    }

    /** @test */
    public function test_validates_required_fields_on_create(): void
    {
        $response = $this->postJson('/api/contacts', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'phone']);
    }

    /** @test */
    public function test_validates_unique_email_on_create(): void
    {
        ContactModel::factory()->create(['email' => 'joao@empresa.com.br']);

        $response = $this->postJson('/api/contacts', [
            'name'  => 'João',
            'email' => 'joao@empresa.com.br',
            'phone' => '11999999999',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function test_validates_email_format_on_create(): void
    {
        $response = $this->postJson('/api/contacts', [
            'name'  => 'João',
            'email' => 'not-an-email',
            'phone' => '11999999999',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    // ─── GET /api/contacts ──────────────────────────────────

    /** @test */
    public function test_lists_contacts_with_pagination(): void
    {
        ContactModel::factory()->count(20)->create();

        $response = $this->getJson('/api/contacts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [['id', 'name', 'email', 'phone', 'score', 'status']],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);
    }

    // ─── GET /api/contacts/{id} ─────────────────────────────

    /** @test */
    public function test_shows_a_single_contact(): void
    {
        $contact = ContactModel::factory()->create();

        $response = $this->getJson("/api/contacts/{$contact->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $contact->id]);
    }

    /** @test */
    public function test_returns_404_for_nonexistent_contact(): void
    {
        $response = $this->getJson('/api/contacts/999');

        $response->assertStatus(404);
    }

    // ─── PUT /api/contacts/{id} ─────────────────────────────

    /** @test */
    public function test_updates_a_contact(): void
    {
        $contact = ContactModel::factory()->create();

        $response = $this->putJson("/api/contacts/{$contact->id}", [
            'name'  => 'Maria Souza',
            'email' => 'maria@nova.com',
            'phone' => '21988887777',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Maria Souza']);

        $this->assertDatabaseHas('contacts', [
            'id'    => $contact->id,
            'name'  => 'Maria Souza',
            'email' => 'maria@nova.com',
        ]);
    }

    /** @test */
    public function test_allows_same_email_on_update_for_same_contact(): void
    {
        $contact = ContactModel::factory()->create(['email' => 'joao@test.com']);

        $response = $this->putJson("/api/contacts/{$contact->id}", [
            'name'  => 'João Atualizado',
            'email' => 'joao@test.com',
            'phone' => '11999999999',
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function test_rejects_duplicate_email_from_another_contact_on_update(): void
    {
        ContactModel::factory()->create(['email' => 'maria@test.com']);
        $contact = ContactModel::factory()->create(['email' => 'joao@test.com']);

        $response = $this->putJson("/api/contacts/{$contact->id}", [
            'name'  => 'João',
            'email' => 'maria@test.com',
            'phone' => '11999999999',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    // ─── DELETE /api/contacts/{id} ──────────────────────────

    /** @test */
    public function test_soft_deletes_a_contact(): void
    {
        $contact = ContactModel::factory()->create();

        $response = $this->deleteJson("/api/contacts/{$contact->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted('contacts', ['id' => $contact->id]);
    }

    /** @test */
    public function test_does_not_list_soft_deleted_contacts(): void
    {
        $contact = ContactModel::factory()->create();
        $contact->delete();

        $response = $this->getJson('/api/contacts');

        $response->assertStatus(200);
        $response->assertJsonMissing(['id' => $contact->id]);
    }
}
