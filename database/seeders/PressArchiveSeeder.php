<?php

namespace Database\Seeders;

use App\Models\PressArticle;
use Illuminate\Database\Seeder;

/**
 * Imports the historic press archive scraped from the old Wix site
 * (docs/raw/website/press.md). Idempotent: keyed on url (or title for the
 * two persberichten without one). The two NL persbericht PDFs attach as
 * `document` media. Dates marked "approx" below were not in the scrape and
 * are estimated from context or the article URL; correct them in the admin
 * if better information surfaces. `title_fr` is a required column but the
 * scrape only transcribed a single (source-language) headline per entry, so
 * it is seeded equal to `title_nl`; a bilingual admin can split it later.
 */
class PressArchiveSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->entries() as $entry) {
            $article = PressArticle::updateOrCreate(
                $entry['url'] ? ['url' => $entry['url']] : ['title_nl' => $entry['title']],
                [
                    'title_nl' => $entry['title'],
                    'title_fr' => $entry['title'],
                    'outlet' => $entry['outlet'],
                    'url' => $entry['url'],
                    'published_at' => $entry['published_at'],
                ],
            );

            if (($entry['document'] ?? null) && $article->getFirstMedia('document') === null) {
                $path = database_path('seeders/files/press/'.$entry['document']);

                if (is_file($path)) {
                    $article->addMedia($path)->preservingOriginal()->toMediaCollection('document');
                }
            }
        }
    }

    /**
     * @return array<int, array{outlet: string, title: string, url: ?string, published_at: string, document?: string}>
     */
    private function entries(): array
    {
        return [
            // 2025
            ['outlet' => 'RTBF', 'title' => '“Kidical mass” : des enfants dans la rue pour demander plus de sécurité lors de leurs déplacements à vélo', 'url' => 'https://www.rtbf.be/article/kidical-mass-des-enfants-dans-la-rue-pour-demander-plus-de-securite-lors-de-leurs-deplacements-a-velo-11565713', 'published_at' => '2025-06-21'],
            ['outlet' => 'RTBF', 'title' => 'Morning Rush et Kidical Mass : le vélo à l\'honneur à Namur ce samedi', 'url' => 'https://www.rtbf.be/article/morning-rush-et-kidicall-mass-le-velo-a-l-honneur-a-namur-ce-samedi-11562950', 'published_at' => '2025-05-13'],
            ['outlet' => 'Het Laatste Nieuws', 'title' => 'Vijf jaar Kidical Mass: feest en fietsprotest voor een kindvriendelijke stad', 'url' => 'https://www.hln.be/brussel/vijf-jaar-kidical-mass-feest-en-fietsprotest-voor-een-kindvriendelijke-stad~a4a43002/', 'published_at' => '2025-05-05'],
            ['outlet' => 'Bruzz', 'title' => 'Kidical Mass bepleit fietsvriendelijk Brussel voor jongeren (video)', 'url' => 'https://www.bruzz.be/actua/veiligheid/kidical-mass-bepleit-fietsvriendelijk-brussel-voor-jongeren-2025-05-04', 'published_at' => '2025-05-04'],
            ['outlet' => 'BX1', 'title' => 'Le Tram : la mobilité des jeunes (video)', 'url' => 'https://bx1.be/emission/le-tram-la-mobilite-des-jeunes/', 'published_at' => '2025-02-21'],

            // 2024
            ['outlet' => 'BX1', 'title' => 'Kidical Mass : un millier d\'enfants et parents paradent à vélo', 'url' => 'https://bx1.be/categories/mobilite/kidical-mass-un-millier-denfants-et-parents-paradent-a-velo/', 'published_at' => '2024-10-14'],
            ['outlet' => 'Persbericht', 'title' => 'Kidical Mass mobiliseert bijna 1.150 ouders en kinderen om een fiets- en kindvriendelijkere stad te eisen', 'url' => null, 'published_at' => '2024-10-07', 'document' => '2024-10-07-persbericht-grote-kidical-mass-nl.pdf'],
            ['outlet' => 'Bruzz', 'title' => '“Kidical Mass lokt duizend deelnemers: \'Door dit soort initiatieven zien we ook beterschap\'”', 'url' => 'https://www.bruzz.be/actua/mobiliteit/grote-opkomst-voor-jaarlijkse-grote-kidical-mass-2024-10-06', 'published_at' => '2024-10-06'],
            ['outlet' => 'BX1', 'title' => 'Bruxelles Vit : Kidical Mass (radio)', 'url' => 'https://bx1.be/radio-emission/bruxelles-vit-kidical-mass-03-10-2024/', 'published_at' => '2024-10-03'],
            ['outlet' => 'Het Laatste Nieuws', 'title' => '“Dit jaar organiseren we 60 fietstochten doorheen Brussel”', 'url' => 'https://www.hln.be/brussel/leticia-37-bouwt-aan-een-fietscultuur-in-brussel-met-kidical-mass-dit-jaar-organiseren-we-60-fietstochten-doorheen-brussel~a2dc9830/', 'published_at' => '2024-03-05'],
            ['outlet' => 'Persbericht', 'title' => 'Persbericht start seizoen 2024', 'url' => null, 'published_at' => '2024-02-20', 'document' => '2024-02-20-persbericht-start-seizoen-nl.pdf'],

            // 2023
            ['outlet' => 'Bruzz', 'title' => 'Fietsambassadeur Leticia Sere bij Melina: \'Er is nood aan conflictvrije kruispunten\'', 'url' => 'https://www.bruzz.be/videoreeks/melina/video-leticia-sere-bij-melina-er-nood-aan-conflictvrije-kruispunten', 'published_at' => '2023-12-07'],
            ['outlet' => 'Politico', 'title' => 'Living Cities: Turning Helsinki\'s empty offices into homes (vermelding)', 'url' => 'https://www.politico.eu/newsletter/global-policy-lab/living-cities-turning-helsinkis-empty-offices-into-homes/', 'published_at' => '2023-11-02'],
            ['outlet' => 'La Dernière Heure', 'title' => 'Près d\'un millier de participants à la "Kidical Mass" organisée à Bruxelles ce dimanche', 'url' => 'https://www.dhnet.be/regions/bruxelles/bruxelles-mobilite/2023/09/11/pres-dun-millier-de-participants-a-la-kidical-mass-organisee-a-bruxelles-ce-dimanche-YIPY45NFIVEBDES67UI2FJTXQI/', 'published_at' => '2023-09-11'],
            ['outlet' => 'Het Nieuwsblad', 'title' => 'Kidical Mass lokt bijna 1.000 deelnemers', 'url' => 'https://www.nieuwsblad.be/cnt/dmf20230911_93580288', 'published_at' => '2023-09-11'],
            ['outlet' => 'Het Laatste Nieuws', 'title' => 'Driejarig bestaan Kidical Mass gevierd met tocht door Brussel', 'url' => 'https://www.hln.be/brussel/driejarig-bestaan-kidical-mass-gevierd-met-tocht-door-brussel~a0badfe0/', 'published_at' => '2023-09-10'],
            ['outlet' => 'BX1', 'title' => 'Kidical Mass : plus de sécurité pour les enfants à vélo', 'url' => 'https://bx1.be/categories/news/kidical-mass-plus-de-securite-pour-les-enfants-a-velo/', 'published_at' => '2023-09-10'],
            ['outlet' => 'Bruzz', 'title' => 'Leticia Sere (Kidical Mass): \'Liever Schaarbike dan Carbeek\'', 'url' => 'https://www.bruzz.be/mobiliteit/leticia-sere-kidical-mass-kinderen-kunnen-zoveel-bijdragen-aan-verkeersveiligheid-2023', 'published_at' => '2023-09-07'],

            // 2022 (scrape had no dates; approx from context/URL)
            ['outlet' => 'La Dernière Heure', 'title' => 'Une "grande Kidical Mass" ce dimanche à Bruxelles au départ du Grand-Hospice', 'url' => 'https://www.dhnet.be/regions/bruxelles/bruxelles-mobilite/2022/09/09/une-grande-kidical-mass-ce-dimanche-a-bruxelles-au-depart-du-grand-hospice-DM7FZ2QGXFBKFKAROND3OCU2JQ/', 'published_at' => '2022-09-09'],
            ['outlet' => 'BX1', 'title' => 'Kidical Mass : la manifestation à vélo s\'adapte aux enfants', 'url' => 'https://bx1.be/categories/news/kidical-mass-la-manifesttion-a-velo-sadapte-aux-enfants/', 'published_at' => '2022-09-09'], // approx
            ['outlet' => 'Het Laatste Nieuws', 'title' => 'Kidical Mass in Elsene: “Willen kinderen leren fietsen op laagdrempelige manier”', 'url' => 'https://www.hln.be/brussel/kidical-mass-in-elsene-willen-kinderen-leren-fietsen-op-laagdrempelige-manier~a0cd3db6/', 'published_at' => '2022-05-01'], // approx

            // 2020
            ['outlet' => 'Het Nieuwsblad', 'title' => 'Eerste Kidical Mass is schot in de roos', 'url' => 'https://www.nieuwsblad.be/cnt/dmf20200701_93495053', 'published_at' => '2020-07-01'],
            ['outlet' => 'Bruzz', 'title' => 'Kidical Mass: \'Fietsen moet vanzelfsprekend worden\' (video)', 'url' => 'https://www.bruzz.be/videoreeks/vrijdag-26-juni-2020/video-kidical-mass-fietsen-moet-vanzelfsprekend-worden', 'published_at' => '2020-06-26'],
        ];
    }
}
