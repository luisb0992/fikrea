<?php

namespace Tests\Unit\Fikrea;

use Tests\TestCase;

use Illuminate\Support\Facades\Storage;

use Fikrea\FaceRecognition;

class FaceRecognitionTest extends TestCase
{
    /**
     * Test para verificar si dos imágenes son de la misma persona
     *
     * @return void
     */
    /** @test */
    public function testImagesAreSamePerson():void
    {
        $image1 = Storage::disk('test')->path('child1.jpg');
        $image2 = Storage::disk('test')->path('child2.jpg');

        $recognition = FaceRecognition::compare($image1, $image2);

        // Las imágenes pertenen a la misma persona
        $this->assertEquals($recognition->match, true);
    }

    /**
     * Test para verificar si dos imágenes no son de la misma persona
     *
     * @return void
     */
    /** @test */
    public function testImagesNotAreSamePerson():void
    {
        $image1 = Storage::disk('test')->path('child1.jpg');
        $image2 = Storage::disk('test')->path('girl1.jpg');

        $recognition = FaceRecognition::compare($image1, $image2);

        // Las imágenes no pertenen a la misma persona
        $this->assertEquals($recognition->match, false);
    }

    /**
     * Test para verificar si se identifica la cara
     *
     * @return void
     */
    /** @test */
    public function testImageIsNotFace():void
    {
        $image1 = Storage::disk('test')->path('melon.jpg');
        $image2 = Storage::disk('test')->path('girl1.jpg');

        $recognition = FaceRecognition::compare($image1, $image2);

        // Las imagen suministrada no posee una cara
        $this->assertEquals($recognition->match, false);
    }
}
