<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $this->assertTrue(true);
    }


    public function testRequestWithJSONBody()
{

        $response = $this->json('POST', '/test', '{"Pete": "Nick",
      "Barbara": "Nick",
      "Nick": "Sophie",
      "Bort": "Sophie",
      "Ronald":"Bort",
      "Beth":"Bort",
      "Sophie":"Ray"}');

$response
    ->assertStatus(201)
    ->assertExactJson([
        'created' => true,
    ]);

}


}
