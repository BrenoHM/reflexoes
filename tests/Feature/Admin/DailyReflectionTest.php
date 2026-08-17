<?php

namespace Tests\Feature\Admin;

use App\Models\DailyReflection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyReflectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_the_admin_area(): void
    {
        $response = $this->get(route('admin.reflections.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_admin_can_view_the_listing(): void
    {
        $user = User::factory()->create();
        DailyReflection::factory()->count(3)->create();

        $response = $this->actingAs($user)->get(route('admin.reflections.index'));

        $response->assertOk();
    }

    public function test_admin_can_create_a_reflection(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.reflections.store'), [
            'paragrafo' => 42,
            'descricao_paragrafo' => 'Texto do parágrafo do Diário.',
            'reflexao' => 'Texto de teste.',
        ]);

        $response->assertRedirect(route('admin.reflections.index'));
        $this->assertDatabaseHas('daily_reflections', [
            'paragrafo' => 42,
            'descricao_paragrafo' => 'Texto do parágrafo do Diário.',
            'reflexao' => 'Texto de teste.',
        ]);
    }

    public function test_admin_can_update_a_reflection(): void
    {
        $user = User::factory()->create();
        $reflection = DailyReflection::factory()->create(['paragrafo' => 10]);

        $response = $this->actingAs($user)->put(route('admin.reflections.update', $reflection), [
            'paragrafo' => 20,
            'descricao_paragrafo' => 'Descrição atualizada.',
            'reflexao' => 'Texto atualizado.',
        ]);

        $response->assertRedirect(route('admin.reflections.index'));
        $reflection->refresh();
        $this->assertSame(20, $reflection->paragrafo);
        $this->assertSame('Descrição atualizada.', $reflection->descricao_paragrafo);
    }

    public function test_admin_can_delete_a_reflection(): void
    {
        $user = User::factory()->create();
        $reflection = DailyReflection::factory()->create();

        $response = $this->actingAs($user)->delete(route('admin.reflections.destroy', $reflection));

        $response->assertRedirect(route('admin.reflections.index'));
        $this->assertSoftDeleted($reflection);
    }

    public function test_paragrafo_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.reflections.store'), [
            'descricao_paragrafo' => 'Texto do parágrafo.',
            'reflexao' => 'Texto de teste.',
        ]);

        $response->assertSessionHasErrors('paragrafo');
    }

    public function test_descricao_paragrafo_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.reflections.store'), [
            'paragrafo' => 1,
            'reflexao' => 'Texto de teste.',
        ]);

        $response->assertSessionHasErrors('descricao_paragrafo');
    }

    public function test_reflexao_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.reflections.store'), [
            'paragrafo' => 1,
            'descricao_paragrafo' => 'Texto do parágrafo.',
        ]);

        $response->assertSessionHasErrors('reflexao');
    }
}
