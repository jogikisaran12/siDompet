<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Halaman "/" mengarahkan pengguna ke dashboard, dan untuk tamu
     * halaman dashboard dilanjutkan ke halaman login.
     */
    public function test_the_application_redirects_to_login_for_guests(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/dashboard');
        $response->assertRedirect(route('dashboard'));

        $this->get('/dashboard')->assertRedirect(route('login'));
    }
}
