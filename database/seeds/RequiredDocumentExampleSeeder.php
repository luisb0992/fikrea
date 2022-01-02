<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RequiredDocumentExampleSeeder extends Seeder
{
    /**
     * Inserta los documentos requeridos que se proporcionan de manera predeterminada
     *
     * @return void
     */
    public function run()
    {
        // Elimina los documentos anteriores si los hubiera
        DB::statement(
            "DELETE FROM required_document_examples"
        );

        DB::table('required_document_examples')->insert(
            [
                // Documento nacional de identidad (DNI)
                [
                    'lang'          => 'es',
                    'name'          => 'DNI',
                ],

                // Documento de identidad extranjero (NIE)
                [
                    'lang'          => 'es',
                    'name'          => 'NIE',
                ],

                // Permiso de Trabajo/Estudiante
                [
                    'lang'          => 'es',
                    'name'          => 'Permiso de Trabajo/Estudiante',
                ],

                // Pasaporte
                [
                    'lang'          => 'es',
                    'name'          => 'Pasaporte',
                ],

                // Carné de Conducir
                [
                    'lang'          => 'es',
                    'name'          => 'Carné de Conducir',
                ],

                // Curriculum Vitae
                [
                    'lang'          => 'es',
                    'name'          => 'Curriculum Vitae',
                ],

                // Tarjeta de afiliación a la Seguridad Social
                [
                    'lang'          => 'es',
                    'name'          => 'Tarjeta de la Seguridad Social',
                ],

                // Tarjeta Sanitaria Europea
                [
                    'lang'          => 'es',
                    'name'          => 'Tarjeta Sanitaria Europea',
                ],

                // Certificado de titularidad bancaria
                [
                    'lang'          => 'es',
                    'name'          => 'Certificado de titularidad bancaria',
                ],

                // Última declaración del impuesto sobre la renta de personas físicas (IRPF)
                [
                    'lang'          => 'es',
                    'name'          => 'Última declaración del impuesto sobre la renta de personas físicas (IRPF)',
                ],

                // Última recibo de la couta para trabajadores autónomos
                [
                    'lang'          => 'es',
                    'name'          => 'Último recibo de autónomos',
                ],

                // CIF/NIF de su empresa
                [
                    'lang'          => 'es',
                    'name'          => 'CIF/NIF de su empresa',
                ],

                // Escrituras de constitución de la sociedad
                [
                    'lang'          => 'es',
                    'name'          => 'Escrituras de constitución',
                ],

                // Poder notarial
                [
                    'lang'          => 'es',
                    'name'          => 'Poder notarial',
                ],

                // Acta de titularidad real para identificar a los titulares de una sociedad mercantil
                [
                    'lang'          => 'es',
                    'name'          => 'Acta de titularidad real',
                ],

                // Balance y cuenta de resultados del año en curso
                [
                    'lang'          => 'es',
                    'name'          => 'Balance y cuenta de resultados del año en curso',
                ],

                // Modelo 390
                [
                    'lang'          => 'es',
                    'name'          => 'Modelo 390 (Resumen anual del IVA)',
                ],

                // Modelo 190
                [
                    'lang'          => 'es',
                    'name'          => 'Modelo 190 (Retenciones del IRPF del año anterior)',
                ],

                // Modelo 347
                [
                    'lang'          => 'es',
                    'name'          => 'Modelo 347 (Operaciones con terceros)',
                ],

                // Modelo 349
                [
                    'lang'          => 'es',
                    'name'          => 'Modelo 349 (Operaciones intracomunitarias)',
                ],

                // Pool bancario
                [
                    'lang'          => 'es',
                    'name'          => 'Pool bancario',
                ],

                // CIRBE (Documento emitido por la central de información de riesgos del Banco de España)
                [
                    'lang'          => 'es',
                    'name'          => 'CIRBE',
                ],

                // Impuesto de sociedades
                [
                    'lang'          => 'es',
                    'name'          => 'Impuesto de sociedades',
                ],

                // Certificado de estar al corriente de los pagos a la seguridad social
                [
                    'lang'          => 'es',
                    'name'          => 'Certificado de estar al corriente de los pagos a la seguridad social',
                ],

                // Certificado de estar al corriente de los pagos a la hacienda pública
                [
                    'lang'          => 'es',
                    'name'          => 'Certificado de estar al corriente de los pagos a la hacienda pública',
                ],
             
                // La Relación de liquidación de cotizaciones (RLC)
                [
                    'lang'          => 'es',
                    'name'          => 'RLC (antiguo TC1)',
                ],
  
                // La Relación nominal de trabajadores (RNT)
                [
                    'lang'          => 'es',
                    'name'          => 'RNT (antiguo TC2)',
                ],
             
                // La Póliza de responsabilidad civil
                [
                    'lang'          => 'es',
                    'name'          => 'Póliza de responsabilidad civil',
                ],

                // Último recibo se su póliza de responsabilidad civil
                [
                    'lang'          => 'es',
                    'name'          => 'Último recibo se su póliza de responsabilidad civil',
                ],

                // Póliza de accidente de convenio colectivo
                [
                    'lang'          => 'es',
                    'name'          => 'Póliza de accidente de convenio colectivo',
                ],

                // Último recibo se su póliza de convenio colectivo
                [
                    'lang'          => 'es',
                    'name'          => 'Último recibo se su póliza de convenio colectivo',
                ],

                // Último recibo se su póliza de convenio colectivo
                [
                    'lang'          => 'es',
                    'name'          => 'Último recibo se su póliza de convenio colectivo',
                ],

                // Contrato de servicio de prevención de riesgos ajenos a la empresa
                [
                    'lang'          => 'es',
                    'name'          => 'Contrato de servicio de prevención de riesgos ajenos a la empresa',
                ],

                // Último recibo del servicio de prevención de riesgos contratado
                [
                    'lang'          => 'es',
                    'name'          => 'Último recibo del servicio de prevención de riesgos contratado',
                ],

                // Revisión médida del trabajador
                [
                    'lang'          => 'es',
                    'name'          => 'Revisión médida del trabajador',
                ],

                // Autorización de uso de equipos de trabajo
                [
                    'lang'          => 'es',
                    'name'          => 'Autorización de uso de equipos de trabajo',
                ],

                // Certificado de prevención de riesgos laborales del puesto de trabajo
                [
                    'lang'          => 'es',
                    'name'          =>
                        'Certificado de prevención de riesgos laborales del puesto de trabajo',
                ],

                // Carné de manipulador de alimentos
                [
                    'lang'          => 'es',
                    'name'          => 'Carné de manipulador de alimentos',
                ],

                // Información relacionada con el puesto de trabajo
                [
                    'lang'          => 'es',
                    'name'          => 'Información relacionada con el puesto de trabajo',
                ],

                // Normas de cumplimiento colectivo dentro de la empresa
                [
                    'lang'          => 'es',
                    'name'          => 'Normas de cumplimiento colectivo dentro de la empresa',
                ],

                // Protocolo interno frenta al COVID-19
                [
                    'lang'          => 'es',
                    'name'          => 'Protocolo interno frenta al COVID-19',
                ],

                // Protocolo de emergencias y evacuaciones
                [
                    'lang'          => 'es',
                    'name'          => 'Protocolo de emergencias y evacuaciones',
                ],

                // Justificante de transferencia bancaria realizada
                [
                    'lang'          => 'es',
                    'name'          => 'Justificante de transferencia bancaria realizada',
                ],

                // Escritura sobre bienes inmuebles
                [
                    'lang'          => 'es',
                    'name'          => 'Escritura sobre bienes inmuebles',
                ],

                // Póliza vigemte del inmueble
                [
                    'lang'          => 'es',
                    'name'          => 'Póliza vigente del inmueble',
                ],

                // Último recibo la póliza contratada del inmueble
                [
                    'lang'          => 'es',
                    'name'          => 'Último recibo la póliza contratada del inmueble',
                ],

                // Ficha técnica del vehículo (Permiso de circulación)
                [
                    'lang'          => 'es',
                    'name'          => 'Ficha técnica del vehículo (Permiso de circulación)',
                ],

                // Tarjeta ITV del vehículo
                [
                    'lang'          => 'es',
                    'name'          => 'Tarjeta ITV del vehículo',
                ],

                // Póliza vigente del vehículo
                [
                    'lang'          => 'es',
                    'name'          => 'Póliza vigente del vehículo',
                ],

                // Último recibo de la póliza vigente del vehículo
                [
                    'lang'          => 'es',
                    'name'          => 'Último recibo de la póliza vigente del vehículo',
                ],

                // Último impuesto de circulación
                [
                    'lang'          => 'es',
                    'name'          => 'Último impuesto de circulación',
                ],
            ]
        );
    }
}
