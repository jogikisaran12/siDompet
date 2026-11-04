<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * Pengaman: seluruh pengujian WAJIB memakai database testing terpisah
     * (SQLite in-memory). Database utama aplikasi tidak boleh disentuh,
     * termasuk oleh proses migrate yang dijalankan RefreshDatabase.
     */
    protected function setUp(): void
    {
        $this->ensureIsolatedTestingDatabase();

        parent::setUp();
    }

    private function ensureIsolatedTestingDatabase(): void
    {
        $connection = $_ENV['DB_CONNECTION'] ?? getenv('DB_CONNECTION');
        $database = $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE');

        if ($connection !== 'sqlite' || $database !== ':memory:') {
            throw new RuntimeException(
                'Pengujian hanya boleh berjalan pada database testing in-memory (SQLite). '
                . 'Database utama aplikasi tidak boleh digunakan untuk pengujian.'
            );
        }
    }
}
