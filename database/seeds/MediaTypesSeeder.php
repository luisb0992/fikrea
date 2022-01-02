<?php

namespace Database\Seeders;

use App\Models\MediaType;
use App\Utils\FileUtils;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MediaTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('media_types')->truncate();

        // Cargar el listado oficial de la IANA
        $filename = database_path() . '/data/media-types.csv';

        $data = FileUtils::seedFromCSV($filename);

        foreach ($data as $item) {
            try {
                MediaType::create(
                    [
                        'media_type' => $item['Media Type'],
                        'type' => $item['Type'],
                        'subtype' => $item['Subtype'],
                        'extensions' => $item['Extensions'],
                        'signable' => // Se pueden firmar:
                            ('image' === $item['Type']) // 1) todos los formatos de imagen
                            || ('text' === $item['Type'])  // 2) todos los formatos de texto plano
                            || (
                                ('application' === $item['Type']) // 3) de los formatos de aplicación
                                // Las extensiones conocidas  de documentos de ofimática o PDF
                                && ([] !== array_intersect(
                                        explode(' ', $item['Extensions']),
                                        [
                                            'xml',
                                            'xsl',
                                            'pdf',
                                            'doc',
                                            'dot',
                                            'docx',
                                            'xls',
                                            'xlm',
                                            'xla',
                                            'xlc',
                                            'xlt',
                                            'xlw',
                                            'xlsx',
                                            'xlsm',
                                            'xlsb',
                                            'odt',
                                            'ods',
                                            'xps',
                                            'pptx',
                                            'ppt',
                                            'pps',
                                            'pot',
                                            'pptm',
                                        ]
                                    ))),
                        'can_apply_ocr' => ('image' === $item['Type']) || Str::contains($item['Extensions'], 'pdf'),
                    ]
                );
            } catch (Exception $exception) {
            }
        }

        // Complementar el listado con otros tipos no estándares, asociando además una descripción
        $filename = database_path() . '/data/media-types-with-description.csv';

        $data = FileUtils::seedFromCSV($filename);

        foreach ($data as $item) {
            try {
                MediaType::updateOrCreate(
                    [
                        'media_type' => $item['Media Type'],
                    ],
                    [
                        'type' => $item['Type'],
                        'subtype' => $item['Subtype'],
                        'extensions' => $item['Extensions'],
                        'signable' => // Se pueden firmar:
                            ('image' === $item['Type']) // 1) todos los formatos de imagen
                            || ('text' === $item['Type'])  // 2) todos los formatos de texto plano
                            || (
                                ('application' === $item['Type']) // 3) de los formatos de aplicación
                                // Las extensiones conocidas  de documentos de ofimática o PDF
                                && ([] !== array_intersect(
                                        explode(' ', $item['Extensions']),
                                        [
                                            'xml',
                                            'xsl',
                                            'pdf',
                                            'doc',
                                            'dot',
                                            'docx',
                                            'xls',
                                            'xlm',
                                            'xla',
                                            'xlc',
                                            'xlt',
                                            'xlw',
                                            'xlsx',
                                            'xlsm',
                                            'xlsb',
                                            'odt',
                                            'ods',
                                            'xps',
                                            'pptx',
                                            'ppt',
                                            'pps',
                                            'pot',
                                            'pptm',
                                        ]
                                    ))),
                        'can_apply_ocr' => ('image' === $item['Type']) || Str::contains($item['Extensions'], 'pdf'),
                        'description' => $item['Description'],
                    ]
                );
            } catch (Exception $exception) {
            }
        }

        Schema::enableForeignKeyConstraints();
    }
}
