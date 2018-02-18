<?php

namespace CrTranslateMate\Requests;

use PHPUnit\Framework\TestCase;

class SaveTranslationRequestTest extends TestCase
{
    private $input = array(
        'userInterface' => 'admin',
        'fileName' => 'filename',
        'language' => 'en-gb',
        'translation' => 'translated text',
        'key' => 'key',
    );

    public function testGetters()
    {
        $request = new SaveTranslationRequest($this->input);
        $this->assertEquals($this->input['userInterface'], $request->getInterface());
        $this->assertEquals($this->input['fileName'], $request->getFileName());
        $this->assertEquals($this->input['language'], $request->getLanguage());
        $this->assertEquals($this->input['translation'], $request->getTranslation());
        $this->assertEquals($this->input['key'], $request->getKey());
    }
}