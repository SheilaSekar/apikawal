{{--
    Partial: Kontribusi APIKAWAL terhadap SDGs
    Lokasi yang disarankan: resources/views/pages/partials/sdg-section.blade.php
    Cara pakai di halaman Tentang: @include('pages.partials.sdg-section')
    Teks mudah diedit: ubah isi array $sdgs di bawah, markup di bawahnya otomatis menyesuaikan.
--}}
@php
    $sdgs = [
        [
            'number'  => 13,
            'name'    => 'Penanganan Perubahan Iklim',
            'role'    => 'Tujuan utama',
            'why'     => 'Kebakaran hutan dan lahan, terutama di lahan gambut, melepaskan emisi karbon dalam jumlah besar, dan risikonya meningkat saat kondisi kering seperti pada musim El Niño. Laporan Reuters (22 Oktober 2026) menyebut Indonesia menghadapi kebakaran hutan terparah dalam 11 tahun terakhir, dengan El Niño memperburuk kerentanan hutan dan gambut. Semakin lama api dibiarkan, semakin besar lahan yang terbakar dan semakin besar emisi yang dilepas.',
            'how'     => [
                'Warga dan relawan dapat melaporkan asap atau api sejak dini, sehingga jeda antara kemunculan api dan penanganan dapat dipersingkat.',
                'Indeks Prioritas mengurutkan laporan terverifikasi sehingga petugas dapat menangani kasus yang paling mendesak lebih dulu.',
                'Riwayat kejadian dan analisis wilayah rawan membantu perencanaan pencegahan, bukan hanya pemadaman.',
            ],
            'targets' => [
                ['13.1', 'Memperkuat ketahanan dan kemampuan adaptasi terhadap bahaya terkait iklim dan bencana alam.'],
                ['13.3', 'Meningkatkan kapasitas masyarakat dan kelembagaan dalam menghadapi dampak perubahan iklim, termasuk dukungan pelaporan dini.'],
            ],
        ],
        [
            'number'  => 15,
            'name'    => 'Ekosistem Daratan',
            'role'    => 'Tujuan pendukung',
            'why'     => 'Hutan, semak, dan lahan gambut rusak bila kebakaran terlambat ditangani. Kementerian Kehutanan menyatakan bahwa hotspot satelit hanyalah anomali suhu permukaan: satu kebakaran dapat terdeteksi sebagai beberapa hotspot, dan bara dapat bertahan di dalam tanah meski hotspot tidak lagi terdeteksi, terutama di gambut. Karena itu data satelit perlu dilengkapi pengamatan langsung di lapangan.',
            'how'     => [
                'Verifikasi oleh beberapa pengguna atau petugas melengkapi data satelit dengan konfirmasi dari lapangan.',
                'Jenis lahan dicatat pada setiap laporan, dan lahan gambut diberi bobot lebih tinggi dalam Indeks Prioritas karena sulit dipadamkan.',
                'Data historis dipetakan menjadi wilayah rawan sehingga kawasan yang berulang terbakar lebih mudah dikenali.',
            ],
            'targets' => [
                ['15.1', 'Pelestarian dan pemanfaatan berkelanjutan ekosistem daratan, termasuk hutan dan lahan basah.'],
                ['15.2', 'Pengelolaan hutan secara berkelanjutan dan upaya menghentikan hilangnya hutan.'],
            ],
        ],
        [
            'number'  => 11,
            'name'    => 'Kota dan Permukiman yang Berkelanjutan',
            'role'    => 'Tujuan pendukung',
            'why'     => 'Kebakaran lahan dan asapnya tidak berhenti di batas kebun atau hutan. Permukiman, sekolah, dan fasilitas kesehatan di sekitarnya ikut terdampak, dan warga sering tidak tahu apakah laporan mereka sudah ditindaklanjuti.',
            'how'     => [
                'Kedekatan lokasi dengan permukiman, sekolah, dan fasilitas kesehatan menjadi salah satu faktor dalam Indeks Prioritas.',
                'Peta publik menampilkan status penanganan setiap laporan, sehingga komunitas dapat memantau perkembangan secara terbuka.',
                'Notifikasi dan riwayat status memberi kepastian kepada pelapor bahwa laporannya diproses.',
            ],
            'targets' => [
                ['11.5', 'Mengurangi dampak bencana terhadap masyarakat, termasuk melalui respons yang lebih cepat dan terkoordinasi.'],
            ],
        ],
        [
            'number'  => 3,
            'name'    => 'Kehidupan Sehat dan Sejahtera',
            'role'    => 'Tujuan pendukung',
            'why'     => 'Asap kebakaran lahan menurunkan kualitas udara dan dapat membahayakan kesehatan pernapasan, terutama bagi anak-anak, lansia, dan orang dengan gangguan pernapasan. Makin cepat sumber asap ditangani, makin singkat warga terpapar.',
            'how'     => [
                'Asap adalah salah satu tanda yang dapat dilaporkan warga, sehingga sumber asap dapat dikenali dan diverifikasi lebih awal.',
                'Penanganan yang lebih terarah melalui prioritas dan koordinasi berpotensi memperpendek durasi paparan asap bagi warga sekitar.',
                'Wilayah rawan dari data historis membantu pihak terkait menyiapkan antisipasi di kawasan yang sering terdampak.',
            ],
            'targets' => [
                ['3.9', 'Mengurangi dampak kesehatan akibat pencemaran udara, air, dan tanah.'],
            ],
        ],
        [
            'number'  => 17,
            'name'    => 'Kemitraan untuk Mencapai Tujuan',
            'role'    => 'Tujuan pendukung',
            'why'     => 'Pencegahan dan penanganan kebakaran lahan membutuhkan kerja sama banyak pihak: warga yang pertama melihat, relawan yang memeriksa di lapangan, petugas yang menangani, dan pihak lain yang memanfaatkan datanya. Tanpa alur yang menghubungkan mereka, informasi mudah terputus.',
            'how'     => [
                'Satu alur menghubungkan empat peran (warga, relawan, petugas, admin) dengan hak akses yang jelas.',
                'API publik read-only menyediakan data anonim dalam format JSON yang dapat dipakai instansi, peneliti, atau aplikasi lain.',
                'Koordinat pada data publik dibulatkan dan identitas pelapor tidak dibuka, sehingga data dapat dibagikan tanpa mengorbankan privasi.',
            ],
            'targets' => [
                ['17.17', 'Mendorong kemitraan yang efektif antara pemerintah, swasta, dan masyarakat sipil.'],
                ['17.18', 'Meningkatkan ketersediaan data yang tepat waktu, andal, dan dapat dimanfaatkan.'],
            ],
        ],
    ];
@endphp

<section aria-labelledby="sdg-heading" class="w-full">
    <h2 id="sdg-heading" class="text-2xl font-bold text-gray-900">Kontribusi terhadap Tujuan Pembangunan Berkelanjutan (SDGs)</h2>

    <p class="mt-3 text-base leading-relaxed text-gray-800">
        APIKAWAL dirancang untuk mendukung lima tujuan Tujuan Pembangunan Berkelanjutan (SDGs).
        <strong>SDG 13 (Penanganan Perubahan Iklim)</strong> adalah tujuan utama, sedangkan SDG 15, SDG 11, SDG 3, dan SDG 17
        adalah tujuan pendukung yang ikut terjawab oleh cara kerja sistem ini: pelaporan dini dari warga, verifikasi berlapis,
        prioritas penanganan yang transparan, pelacakan status respons, dan data terbuka.
    </p>

    <div class="mt-8 grid gap-6 md:grid-cols-2">
        @foreach ($sdgs as $sdg)
            <article
                aria-labelledby="sdg-{{ $sdg['number'] }}-title"
                class="flex flex-col rounded-lg border border-gray-300 bg-white p-5 shadow-sm {{ $loop->first ? 'md:col-span-2' : '' }}"
            >
                <header class="flex items-start gap-4">
                    <span
                        class="inline-flex h-12 w-12 flex-none items-center justify-center rounded-md bg-gray-900 text-lg font-bold text-white"
                        aria-hidden="true"
                    >{{ $sdg['number'] }}</span>
                    <div>
                        <p class="text-sm font-semibold text-gray-700">
                            <span class="rounded border border-gray-500 px-2 py-0.5">{{ $sdg['role'] }}</span>
                        </p>
                        <h3 id="sdg-{{ $sdg['number'] }}-title" class="mt-1 text-lg font-bold text-gray-900">
                            SDG {{ $sdg['number'] }}: {{ $sdg['name'] }}
                        </h3>
                    </div>
                </header>

                <h4 class="mt-4 text-sm font-semibold uppercase tracking-wide text-gray-700">Mengapa relevan</h4>
                <p class="mt-1 leading-relaxed text-gray-800">{{ $sdg['why'] }}</p>

                <h4 class="mt-4 text-sm font-semibold uppercase tracking-wide text-gray-700">Bagaimana APIKAWAL mendukung</h4>
                <ul class="mt-1 list-disc space-y-1 pl-5 leading-relaxed text-gray-800">
                    @foreach ($sdg['how'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>

                <h4 class="mt-4 text-sm font-semibold uppercase tracking-wide text-gray-700">Target terkait</h4>
                <ul class="mt-1 space-y-1 leading-relaxed text-gray-800">
                    @foreach ($sdg['targets'] as [$code, $text])
                        <li>
                            <span class="font-semibold">Target {{ $code }}:</span> {{ $text }}
                        </li>
                    @endforeach
                </ul>
            </article>
        @endforeach
    </div>

    <aside class="mt-8 rounded-lg border border-gray-300 bg-gray-50 p-5" aria-labelledby="sdg-note-title">
        <h3 id="sdg-note-title" class="text-base font-bold text-gray-900">Catatan tentang pemetaan ini</h3>
        <ul class="mt-2 list-disc space-y-1 pl-5 leading-relaxed text-gray-800">
            <li>Pemetaan ini menunjukkan <strong>area kontribusi yang dituju</strong>, bukan klaim dampak yang sudah terukur. APIKAWAL adalah prototipe dan belum diuji bersama instansi penanganan kebakaran.</li>
            <li>APIKAWAL <strong>bukan pendeteksi api otomatis</strong> dan <strong>bukan pengganti layanan darurat</strong>. Untuk kebakaran yang mengancam jiwa, hubungi 112 atau BPBD/pemadam kebakaran setempat.</li>
            <li>Indeks Prioritas bersifat heuristik awal sebagai alat triase untuk mengurutkan antrean, dan bobotnya dapat dikalibrasi bersama pakar pada tahap pengembangan berikutnya.</li>
            <li>Peran petugas instansi pada prototipe ini disimulasikan, dan data demo diberi label "DATA SIMULASI".</li>
        </ul>
    </aside>
</section>
