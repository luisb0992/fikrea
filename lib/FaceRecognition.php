<?php

/**
 * La Clase FaceRecognition
 *
 * Compara dos imágenes, obteniendo la probabilidad de que correspondan a la misma persona
 *
 * @link https://github.com/MacgyverCode/faceRecognition-PHP
 *
 * Instalación:
 *
 * Usamos una imagen de docker con la aplicación de reconocimiento (python3)
 *
 * Debemos de descargar esta imagen con:
 *
 * docker pull macgyvertechnology/face-comparison-model:2
 *
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos SL
 *
 * @example
 *
 * use Fikrea\FaceRecognition;
 *
 * $recognition = FaceRecognition::compare($image1, $image2);
 *
 * siendo $image1 e $image2 las rutas absolutas de las imágenes que se desean comparar
 *
 * Se obtiene un objeto FaceRecognition con el resultado de la comparación:
 *
 * faces        : El número de caras identificadas
 * matches      : El número de coincidencias
 * match        : Si las dos imágenes corresponden a la misma persona
 *                Lo que ocurre cuando la coincidencia es superior al 40%
 * coordinates  : Una lista de coordenadas
 */

namespace Fikrea;

class FaceRecognition extends AppObject
{
    /**
     * El número de caras identificadas
     *
     * @var int
     */
    protected int $faces;

    /**
     * El número de resultados
     *
     * @var int
     */
    protected int $matches;

    /**
     * Si las imágenes proporcionadas corresponden a la misma persona
     *
     * @var bool
     */
    protected bool $match;

    /**
     * Una lista de coordenadas con las posiciones de la caras identificadas
     *
     * @var array
     */
    protected array $coordinates;

    /**
     * El constructor
     *
     * @param array|null                        Una lista de valores o null
     */
    public function __contruct(?array $values)
    {
        // Si el valor es nulo se establece como una lista vacia
        if ($values == null) {
            $values = [];
        }

        parent::__contruct($values);
    }

    /**
     * Compara dos imágenes indicando si pertenecen o no a la misma persona
     *
     * @param string $image1                    La ruta absoluta de la primera imagen
     * @param string $image2                    La ruta absoluta de la segunda imagen
     *
     * @return array                            Un objecto FaceRecognition
     */
    public static function compare(string $image1, string $image2):self
    {
        // Obtiene el id del contenedor macgyvertechnology/face-comparison-model:2
        $id = preg_replace(
            '/[^0-9a-z]/',
            '',
            // Si se obtiene "null" puede ser un problema de permisos
            // "docker: Got permission denied while trying to connect to the Docker daemon..."
            // Podemos depurar el error enviando la salida de error a la salida estńdar con 2>&1
            //
            // Comprobar que el usuario www-data que ejecuta el servidor tenga permisos para usar docker:
            //
            // sudo usermod -aG docker www-data
            //
            shell_exec('docker run -it -d macgyvertechnology/face-comparison-model:2')
        );
         
        // Copia las imágenes a comparar en el contenedor
        exec("docker cp {$image1} {$id}:/macgyver/temp/known.jpg");
        exec("docker cp {$image2} {$id}:/macgyver/temp/test.jpg");

        // Inicia la aplicación de reconociento facial en el contenedor y da la probabilidad de coincidencia
        $result = json_decode(shell_exec("docker exec -t {$id} /bin/bash -c 'python3 /macgyver/main'"), true);
        
        // Detiene el contenedor
        exec("docker stop {$id}");
        
        // Elimina el contenedor
        exec("docker rm {$id}");

        // Da valores por defecto
        $result['match']   ??= false;                   // Reconocimiento facial fallido
        $result['matches'] ??= 0;
   
        return new self($result);
    }
}
