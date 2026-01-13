<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = [
            [
                'user_id' => 1,
                'judul' => 'konser musik rock',
                'deskripsi' => 'Nikmati malam penuh energi dengan band rock terkenal.',
                'tanggal_waktu' => '2024-8-15 19:00:00',
                'lokasi' => 'Stadion Utama',
                'kategori_id' => 1,
                'gambar' => 'events/konser_rock.jpg',
            ],
            [
                'user_id' => 1,
                'judul' => 'Pemeran seni kontemporer',
                'deskripsi' => 'jelajahi karya seni moderen dari seniman lokal dan internasional.',
                'tanggal_waktu' => '2024-8-20 10:00:00',
                'lokasi' => 'Galeri Seni Kota',
                'kategori_id' => 2,
                'gambar' => 'events/pameran_seni.jpg',
            ],
            [
                'user_id' => 1,
                'judul' => 'Festifal makanan internasional',
                'deskripsi' => 'Cicipi berbagai hidangan lezat dari seluruh dunia.',
                'tanggal_waktu' => '2024-8-15 19:00:00',
                'lokasi' => 'Taman Kota',
                'kategori_id' => 3,
                'gambar' => 'events/festival_makanan.jpg',
            ]
        ];

        foreach ($events as $event) {
            Event::create($event);
        }
    }
}
