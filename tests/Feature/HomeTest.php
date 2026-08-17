<?php

namespace Tests\Feature;

use App\Models\DailyReflection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_returns_200(): void
    {
        $response = $this->get('/');

        $response->assertOk();
    }

    public function test_home_page_can_show_a_registered_reflection(): void
    {
        DailyReflection::factory()->create([
            'paragrafo' => 123,
            'descricao_paragrafo' => 'Texto de teste do parágrafo do Diário.',
            'reflexao' => 'Texto de teste da reflexão.',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('123');
        $response->assertSee('Texto de teste do parágrafo do Diário.');
        $response->assertSee('Texto de teste da reflexão.');
    }

    public function test_home_page_selection_does_not_depend_on_any_date(): void
    {
        // Sem coluna "dia": qualquer reflexão ativa é elegível para o
        // sorteio, independentemente de quando foi cadastrada.
        $reflection = DailyReflection::factory()->create();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee((string) $reflection->paragrafo);
    }

    public function test_soft_deleted_reflections_are_not_selected(): void
    {
        $reflection = DailyReflection::factory()->create([
            'reflexao' => 'Reflexão excluída, não deveria aparecer.',
        ]);
        $reflection->delete();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertDontSee('Reflexão excluída, não deveria aparecer.');
        $response->assertSee('Ainda não há reflexões cadastradas.');
    }

    public function test_home_page_shows_friendly_message_when_there_are_no_reflections(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Ainda não há reflexões cadastradas.');
    }

    public function test_home_page_shows_the_pix_donation_section(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Ajude a manter este projeto');
        $response->assertSee(config('donation.receiver_name'));
        $response->assertSee(config('donation.bank'));
        $response->assertSee('Copiar código Pix');
    }

    public function test_home_page_pix_section_contains_a_valid_qr_code_svg(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('<svg', false);
    }

    public function test_home_page_pix_copy_and_paste_code_contains_the_configured_pix_key(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(config('donation.pix_key'));
    }

    public function test_home_page_does_not_show_a_fixed_donation_amount(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertDontSee('R$ 5');
        $response->assertDontSee('R$ 10');
        $response->assertDontSee('R$ 20');
        $response->assertDontSee('R$ 50');
    }

    public function test_home_page_shows_the_listen_button_and_speech_data_when_there_is_a_reflection(): void
    {
        DailyReflection::factory()->create([
            'paragrafo' => 55,
            'descricao_paragrafo' => 'Descrição de teste.',
            'reflexao' => 'Reflexão de teste.',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Ouvir reflexão');
        $response->assertSee('reflection-speech', false);
        $response->assertSee('reflectionSpeechData', false);
        $response->assertSee('Descrição de teste.');
    }

    public function test_home_page_does_not_show_the_listen_button_when_there_is_no_reflection(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertDontSee('Ouvir reflexão');
        $response->assertDontSee('reflectionSpeechData', false);
    }

    public function test_speech_data_strips_html_tags_from_the_reflection_text(): void
    {
        DailyReflection::factory()->create([
            'descricao_paragrafo' => '<b>Negrito</b> e <script>alert(1)</script> texto.',
            'reflexao' => 'Reflexão normal.',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        // O texto falado (dentro do <script> de dados) não deve conter tags.
        $response->assertDontSee('<script>alert(1)</script>', false);
        $response->assertSee('Negrito', false);
    }
}
