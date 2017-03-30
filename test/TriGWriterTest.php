<?php

use PHPUnit\Framework\TestCase;
use pietercolpaert\hardf\TriGWriter;

/**
 * @covers Util
 */
class TriGWriterTest extends PHPUnit_Framework_TestCase
{

    public function testTriGWriter ()
    {
        //should serialize 0 triples',
        $this->shouldSerialize('');
    }
    
    public function testOneTriple () 
    {
        
        //should serialize 1 triple',
        $this->shouldSerialize(['abc', 'def', 'ghi'],
        '<abc> <def> <ghi>.' . "\n");
    }

    public function testWriter() 
    {
        
        //should serialize 2 triples',
        $this->shouldSerialize(['abc', 'def', 'ghi'],
        ['jkl', 'mno', 'pqr'],
        '<abc> <def> <ghi>.' . "\n" .
        '<jkl> <mno> <pqr>.' . "\n");

        //should serialize 3 triples',
        $this->shouldSerialize(['abc', 'def', 'ghi'],
        ['jkl', 'mno', 'pqr'],
        ['stu', 'vwx', 'yz'],
        '<abc> <def> <ghi>.' . "\n" .
        '<jkl> <mno> <pqr>.' . "\n" .
        '<stu> <vwx> <yz>.' . "\n");

        //should serialize a literal',
        $this->shouldSerialize(['a', 'b', '"cde"'],
        '<a> <b> "cde".' . "\n");

        //should serialize a literal with a type',
        $this->shouldSerialize(['a', 'b', '"cde"^^fgh'],
        '<a> <b> "cde"^^<fgh>.' . "\n");

        //should serialize a literal with a language',
        $this->shouldSerialize(['a', 'b', '"cde"@en-us'],
        '<a> <b> "cde"@en-us.' . "\n");

        //should serialize a literal containing a single quote',
        $this->shouldSerialize(['a', 'b', '"c\'de"'],
        '<a> <b> "c\'de".' . "\n");

        //should serialize a literal containing a double quote',
        $this->shouldSerialize(['a', 'b', '"c"de"'],
        '<a> <b> "c\\"de".' . "\n");

        //should serialize a literal containing a backslash'
        $this->shouldSerialize(['a', 'b', '"c\\de"'],
        '<a> <b> "c\\\\de".' . "\n");

        //should serialize a literal containing a tab character',
        $this->shouldSerialize(['a', 'b', "\"c\tde\""],
        "<a> <b> \"\"\"c\\tde\"\"\".\n");

        //should serialize a literal containing a newline character',
        /*      shouldSerialize(['a', 'b', '"c\nde"'],
                      '<a> <b> "c\\nde".\n'));*/
        $this->shouldSerialize(['a', 'b', '"c' . "\n" . 'de"'],
        '<a> <b> """c' . "\n" . 'de""".' . "\n");

        //should serialize a literal containing a cariage return character',
        $this->shouldSerialize(['a', 'b', '"c\rde"'],
        '<a> <b> "c\\rde".' . "\n");

        //should serialize a literal containing a backspace character',
        $this->shouldSerialize(['a', 'b', '"c\bde"'],
        '<a> <b> "c\\bde".' . "\n");

        //should serialize a literal containing a form feed character',
        $this->shouldSerialize(['a', 'b', '"c\fde"'],
        '<a> <b> "c\\fde".' . "\n");

        //should serialize a literal containing a line separator',
        $this->shouldSerialize(['a', 'b', '"c\u2028de"'],
        '<a> <b> "c\u2028de".' . "\n");

        //should serialize a literal containing a paragraph separator',
        $this->shouldSerialize(['a', 'b', '"c\u2029de"'],
        '<a> <b> "c\u2029de".' . "\n");

        //should serialize a literal containing special unicode characters',
        $this->shouldSerialize(['a', 'b', '"c\u0000\u0001"'],
        '<a> <b> "c\\u0000\\u0001".' . "\n");

        //should serialize blank nodes',
        $this->shouldSerialize(['_:a', 'b', '_:c'],
        '_:a <b> _:c.' . "\n");
/*
        //should not serialize a literal in the subject',
        shouldNotSerialize(['"a"', 'b', '"c"'],
        'A literal as subject is not allowed: "a"');

        //should not serialize a literal in the predicate',
        shouldNotSerialize(['a', '"b"', '"c"'],
        'A literal as predicate is not allowed: "b"');

        //should not serialize an invalid object literal',
        shouldNotSerialize(['a', 'b', '"c'],
        'Invalid literal: "c');
*/
        //should not leave leading whitespace if the prefix set is empty',
        $this->shouldSerialize([],
        ['a', 'b', 'c'],
        '<a> <b> <c>.' . "\n");

        //should serialize valid prefixes',
        $this->shouldSerialize([ "prefixes" => [ "a" => 'http://a.org/', "b" => 'http://a.org/b#', "c" => 'http://a.org/b' ] ],
        '@prefix a: <http://a.org/>.' . "\n" .
        '@prefix b: <http://a.org/b#>.' . "\n" . "\n");

        //should use prefixes when possible',
        $this->shouldSerialize([ "prefixes" => ['a' => 'http://a.org/','b' => 'http://a.org/b#','c' => 'http://a.org/b' ] ],
        ['http://a.org/bc', 'http://a.org/b#ef', 'http://a.org/bhi'],
        ['http://a.org/bc/de', 'http://a.org/b#e#f', 'http://a.org/b#x/t'],
        ['http://a.org/3a', 'http://a.org/b#3a', 'http://a.org/b#a3'],
        '@prefix a: <http://a.org/>.' . "\n" .
        '@prefix b: <http://a.org/b#>.' . "\n" . "\n" .
        'a:bc b:ef a:bhi.' . "\n" .
        '<http://a.org/bc/de> <http://a.org/b#e#f> <http://a.org/b#x/t>.' . "\n" .
        '<http://a.org/3a> <http://a.org/b#3a> b:a3.' . "\n");

        //should expand prefixes when possible',
        $this->shouldSerialize([ "prefixes" => ['a' => 'http://a.org/','b' => 'http://a.org/b#' ] ],
        ['a:bc', 'b:ef', 'c:bhi'],
        '@prefix a: <http://a.org/>.' . "\n" .
        '@prefix b: <http://a.org/b#>.' . "\n" . "\n" .
        'a:bc b:ef <c:bhi>.' . "\n");

        //should not repeat the same subjects',
        $this->shouldSerialize(['abc', 'def', 'ghi'],
        ['abc', 'mno', 'pqr'],
        ['stu', 'vwx', 'yz'],
        '<abc> <def> <ghi>;' . "\n" .
        '    <mno> <pqr>.' . "\n" .
        '<stu> <vwx> <yz>.' . "\n");

        //should not repeat the same predicates',
        $this->shouldSerialize(['abc', 'def', 'ghi'],
        ['abc', 'def', 'pqr'],
        ['abc', 'bef', 'ghi'],
        ['abc', 'bef', 'pqr'],
        ['stu', 'bef', 'yz'],
        '<abc> <def> <ghi>, <pqr>;' . "\n" .
        '    <bef> <ghi>, <pqr>.' . "\n" .
        '<stu> <bef> <yz>.' . "\n");

        //should write rdf:type as "a"',
        $this->shouldSerialize(['abc', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'def'],
        '<abc> a <def>.' . "\n");

        //should serialize a graph with 1 triple',
        $this->shouldSerialize(['abc', 'def', 'ghi', 'xyz'],
        '<xyz> {' . "\n" .
        '<abc> <def> <ghi>' . "\n" .
        '}' . "\n");

        //should serialize a graph with 3 triples',
        $this->shouldSerialize(['abc', 'def', 'ghi', 'xyz'],
        ['jkl', 'mno', 'pqr', 'xyz'],
        ['stu', 'vwx', 'yz',  'xyz'],
        '<xyz> {' . "\n" .
        '<abc> <def> <ghi>.' . "\n" .
        '<jkl> <mno> <pqr>.' . "\n" .
        '<stu> <vwx> <yz>' . "\n" .
        '}' . "\n");

        //should serialize three graphs',
        $this->shouldSerialize(['abc', 'def', 'ghi', 'xyz'],
        ['jkl', 'mno', 'pqr', ''],
        ['stu', 'vwx', 'yz',  'abc'],
        '<xyz> {' . "\n" . '<abc> <def> <ghi>' . "\n" . '}' . "\n" .
        '<jkl> <mno> <pqr>.' . "\n" .
        '<abc> {' . "\n" . '<stu> <vwx> <yz>' . "\n" . '}' . "\n");

        //should output 8-bit unicode characters as escape sequences',
        $this->shouldSerialize(['\ud835\udc00', '\ud835\udc00', '"\ud835\udc00"^^\ud835\udc00', '\ud835\udc00'],
        '<\\U0001d400> {' . "\n" . '<\\U0001d400> <\\U0001d400> "\\U0001d400"^^<\\U0001d400>' . "\n" . '}' . "\n");

        //should not use escape sequences in blank nodes',
        $this->shouldSerialize(['_:\ud835\udc00', '_:\ud835\udc00', '_:\ud835\udc00', '_:\ud835\udc00'],
        '_:\ud835\udc00 {' . "\n" . '_:\ud835\udc00 _:\ud835\udc00 _:\ud835\udc00' . "\n" . '}' . "\n");
    }
    /*
    public function testCallbackOnEnd () {
        //sends output through end
        $writer = new TriGWriter();
        $writer->addTriple(['subject' => 'a','predicate' => 'b','object' => 'c' ]);
        $writer->end(function ($error, $output) {
            $this->assertEquals("<a> <b> <c>.' . "\n" . '",$output);
        });
    }

    public function testRespectingPrefixes ()
    {
        //respects the prefixes argument when no stream argument is given', function (done) {
        $writer = new TriGWriter([ "prefixes" => ['a' => 'b#' ]]);
        $writer->addTriple(['subject' => 'b#a','predicate' => 'b#b','object' => 'b#c' ]);
        $writer->end(function ($error, $output) {
            $this->assertEquals("@prefix a: <b#>.' . "\n" . "\n" . 'a:a a:b a:c.' . "\n" . '",$output);
        });
        }*/

/*

//does not repeat identical prefixes', function (done) {
$writer = new TriGWriter();
$writer->addPrefix('a', 'b#');
$writer->addPrefix('a', 'b#');
$writer->addTriple({'subject' => 'b#a','predicate' => 'b#b','object' => 'b#c' });
$writer->addPrefix('a', 'b#');
$writer->addPrefix('a', 'b#');
$writer->addPrefix('b', 'b#');
$writer->addPrefix('a', 'c#');
$writer->end(function (error, output) {
    output.should.equal('@prefix a: <b#>.' . "\n" . "\n" . 'a:a a:b a:c.' . "\n" .
    '@prefix b: <b#>.' . "\n" . "\n" . '@prefix a: <c#>.' . "\n" . "\n");
    done(error);
});
});

//serializes triples of a graph with a prefix declaration in between', function (done) {
$writer = new TriGWriter();
$writer->addPrefix('a', 'b#');
$writer->addTriple({'subject' => 'b#a','predicate' => 'b#b','object' => 'b#c','graph' => 'b#g' });
$writer->addPrefix('d', 'e#');
$writer->addTriple({'subject' => 'b#a','predicate' => 'b#b','object' => 'b#d','graph' => 'b#g' });
$writer->end(function (error, output) {
    output.should.equal('@prefix a: <b#>.' . "\n" . "\n" . 'a:g {' . "\n" . 'a:a a:b a:c' . "\n" . '}' . "\n" .
    '@prefix d: <e#>.' . "\n" . "\n" . 'a:g {' . "\n" . 'a:a a:b a:d' . "\n" . '}' . "\n");
    done(error);
});
});

        //should accept triples with separated components', function (done) {
$writer = TriGWriter();
$writer->addTriple('a', 'b', 'c');
$writer->addTriple('a', 'b', 'd');
$writer->end(function (error, output) {
    output.should.equal('<a> <b> <c>, <d>.' . "\n");
    done(error);
});
});

        //should accept quads with separated components', function (done) {
$writer = TriGWriter();
$writer->addTriple('a', 'b', 'c', 'g');
$writer->addTriple('a', 'b', 'd', 'g');
$writer->end(function (error, output) {
    output.should.equal('<g> {' . "\n" . '<a> <b> <c>, <d>' . "\n" . '}' . "\n");
    done(error);
});
});

        //should serialize triples with an empty blank node as object', function (done) {
$writer = TriGWriter();
$writer->addTriple('a1', 'b', $writer->blank());
$writer->addTriple('a2', 'b', $writer->blank([]));
$writer->end(function (error, output) {
    output.should.equal('<a1> <b> [].' . "\n" .
    '<a2> <b> [].' . "\n");
    done(error);
});
});

        //should serialize triples with a one-triple blank node as object', function (done) {
$writer = TriGWriter();
$writer->addTriple('a1', 'b', $writer->blank('d', 'e'));
$writer->addTriple('a2', 'b', $writer->blank({'predicate' => 'd','object' => 'e' }));
$writer->addTriple('a3', 'b', $writer->blank([{'predicate' => 'd','object' => 'e' }]));
$writer->end(function (error, output) {
    output.should.equal('<a1> <b> [ <d> <e> ].' . "\n" .
    '<a2> <b> [ <d> <e> ].' . "\n" .
    '<a3> <b> [ <d> <e> ].' . "\n");
    done(error);
});
});

        //should serialize triples with a two-triple blank node as object', function (done) {
$writer = TriGWriter();
$writer->addTriple('a', 'b', $writer->blank([
    {'predicate' => 'd','object' => 'e' },
    {'predicate' => 'f','object' => '"g"' },
]));
$writer->end(function (error, output) {
    output.should.equal('<a> <b> [' . "\n" .
    '  <d> <e>;' . "\n" .
    '  <f> "g"' . "\n" .
    '].' . "\n");
    done(error);
});
});

        //should serialize triples with a three-triple blank node as object', function (done) {
$writer = TriGWriter();
$writer->addTriple('a', 'b', $writer->blank([
    {'predicate' => 'd','object' => 'e' },
    {'predicate' => 'f','object' => '"g"' },
    {'predicate' => 'h','object' => 'i' },
]));
$writer->end(function (error, output) {
    output.should.equal('<a> <b> [' . "\n" .
    '  <d> <e>;' . "\n" .
    '  <f> "g";' . "\n" .
    '  <h> <i>' . "\n" .
    '].' . "\n");
    done(error);
});
});

        //should serialize triples with predicate-sharing blank node triples as object', function (done) {
$writer = TriGWriter();
$writer->addTriple('a', 'b', $writer->blank([
    {'predicate' => 'd','object' => 'e' },
    {'predicate' => 'd','object' => 'f' },
    {'predicate' => 'g','object' => 'h' },
    {'predicate' => 'g','object' => 'i' },
]));
$writer->end(function (error, output) {
    output.should.equal('<a> <b> [' . "\n" .
    '  <d> <e>, <f>;' . "\n" .
    '  <g> <h>, <i>' . "\n" .
    '].' . "\n");
    done(error);
});
});

        //should serialize triples with nested blank nodes as object', function (done) {
$writer = TriGWriter();
$writer->addTriple('a1', 'b', $writer->blank([
    {'predicate' => 'd', object: $writer->blank() },
]));
$writer->addTriple('a2', 'b', $writer->blank([
    {'predicate' => 'd', object: $writer->blank('e', 'f') },
    {'predicate' => 'g', object: $writer->blank('h', '"i"') },
]));
$writer->addTriple('a3', 'b', $writer->blank([
    {'predicate' => 'd', object: $writer->blank([
            {'predicate' => 'g', object: $writer->blank('h', 'i') },
            {'predicate' => 'j', object: $writer->blank('k', '"l"') },
        ]) },
]));
$writer->end(function (error, output) {
    output.should.equal('<a1> <b> [' . "\n" .
    '  <d> []' . "\n" .
    '].' . "\n" .
    '<a2> <b> [' . "\n" .
    '  <d> [ <e> <f> ];' . "\n" .
    '  <g> [ <h> "i" ]' . "\n" .
    '].' . "\n" .
    '<a3> <b> [' . "\n" .
    '  <d> [' . "\n" .
    '  <g> [ <h> <i> ];' . "\n" .
    '  <j> [ <k> "l" ]' . "\n" .
    ']' . "\n" .
    '].' . "\n");
    done(error);
});
});

        //should serialize triples with an empty blank node as subject', function (done) {
$writer = TriGWriter();
$writer->addTriple($writer->blank(), 'b', 'c');
$writer->addTriple($writer->blank([]), 'b', 'c');
$writer->end(function (error, output) {
    output.should.equal('[] <b> <c>.' . "\n" .
    '[] <b> <c>.' . "\n");
    done(error);
});
});

        //should serialize triples with a one-triple blank node as subject', function (done) {
$writer = TriGWriter();
$writer->addTriple($writer->blank('a', 'b'), 'c', 'd');
$writer->addTriple($writer->blank({'predicate' => 'a','object' => 'b' }), 'c', 'd');
$writer->addTriple($writer->blank([{'predicate' => 'a','object' => 'b' }]), 'c', 'd');
$writer->end(function (error, output) {
    output.should.equal('[ <a> <b> ] <c> <d>.' . "\n" .
    '[ <a> <b> ] <c> <d>.' . "\n" .
    '[ <a> <b> ] <c> <d>.' . "\n");
    done(error);
});
});

        //should serialize triples with an empty blank node as graph', function (done) {
$writer = TriGWriter();
$writer->addTriple('a', 'b', 'c', $writer->blank());
$writer->addTriple('a', 'b', 'c', $writer->blank([]));
$writer->end(function (error, output) {
    output.should.equal('[] {' . "\n" . '<a> <b> <c>' . "\n" . '}' . "\n" .
    '[] {' . "\n" . '<a> <b> <c>' . "\n" . '}' . "\n");
    done(error);
});
});

        //should serialize triples with an empty list as object', function (done) {
$writer = TriGWriter();
$writer->addTriple('a1', 'b', $writer->list());
$writer->addTriple('a2', 'b', $writer->list([]));
$writer->end(function (error, output) {
    output.should.equal('<a1> <b> ().' . "\n" .
    '<a2> <b> ().' . "\n");
    done(error);
});
});

        //should serialize triples with a one-element list as object', function (done) {
$writer = TriGWriter();
$writer->addTriple('a1', 'b', $writer->list(['c']));
$writer->addTriple('a2', 'b', $writer->list(['"c"']));
$writer->end(function (error, output) {
    output.should.equal('<a1> <b> (<c>).' . "\n" .
    '<a2> <b> ("c").' . "\n");
    done(error);
});
});

        //should serialize triples with a three-element list as object', function (done) {
$writer = TriGWriter();
$writer->addTriple('a1', 'b', $writer->list(['c', 'd', 'e']));
$writer->addTriple('a2', 'b', $writer->list(['"c"', '"d"', '"e"']));
$writer->end(function (error, output) {
    output.should.equal('<a1> <b> (<c> <d> <e>).' . "\n" .
    '<a2> <b> ("c" "d" "e").' . "\n");
    done(error);
});
});

        //should serialize triples with an empty list as subject', function (done) {
$writer = TriGWriter();
$writer->addTriple($writer->list(),   'b1', 'c');
$writer->addTriple($writer->list([]), 'b2', 'c');
$writer->end(function (error, output) {
    output.should.equal('() <b1> <c>;' . "\n" .
    '    <b2> <c>.' . "\n");
    done(error);
});
});

        //should serialize triples with a one-element list as subject', function (done) {
$writer = TriGWriter();
$writer->addTriple($writer->list(['a']), 'b1', 'c');
$writer->addTriple($writer->list(['a']), 'b2', 'c');
$writer->end(function (error, output) {
    output.should.equal('(<a>) <b1> <c>;' . "\n" .
    '    <b2> <c>.' . "\n");
    done(error);
});
});

        //should serialize triples with a three-element list as subject', function (done) {
$writer = TriGWriter();
$writer->addTriple($writer->list(['a', '"b"', '"c"']), 'd', 'e');
$writer->end(function (error, output) {
    output.should.equal('(<a> "b" "c") <d> <e>.' . "\n");
    done(error);
});
});

        //should accept triples in bulk', function (done) {
$writer = TriGWriter();
$writer->addTriples([{'subject' => 'a','predicate' => 'b','object' => 'c' },
{'subject' => 'a','predicate' => 'b','object' => 'd' }]);
$writer->end(function (error, output) {
    output.should.equal('<a> <b> <c>, <d>.' . "\n");
    done(error);
});
});

        //should not allow writing after end', function (done) {
$writer = TriGWriter();
$writer->addTriple({'subject' => 'a','predicate' => 'b','object' => 'c' });
$writer->end();
$writer->addTriple({'subject' => 'd','predicate' => 'e','object' => 'f' }, function (error) {
    error.should.be.an.instanceof(Exception);
    error.should.have.property('message', 'Cannot write because the writer has been closed.');
    done();
});
});

        //should write simple triples in N-Triples mode', function (done) {
$writer = TriGWriter({'format' => 'N-Triples' });
$writer->addTriple('a', 'b', 'c');
$writer->addTriple('a', 'b', 'd');
$writer->end(function (error, output) {
    output.should.equal('<a> <b> <c>.' . "\n" . '<a> <b> <d>.' . "\n");
    done(error);
});
});

        //should not write an invalid literal in N-Triples mode', function (done) {
$writer = TriGWriter({'format' => 'N-Triples' });
$writer->addTriple('a', 'b', '"c', function (error) {
    error.should.be.an.instanceof(Exception);
    error.should.have.property('message', 'Invalid literal: "c');
    done();
});
});

        //should write simple quads in N-Quads mode', function (done) {
$writer = TriGWriter({'format' => 'N-Quads' });
$writer->addTriple('a', 'b', 'c');
$writer->addTriple('a', 'b', 'd', 'g');
$writer->end(function (error, output) {
    output.should.equal('<a> <b> <c>.' . "\n" . '<a> <b> <d> <g>.' . "\n");
    done(error);
});
});

        //should not write an invalid literal in N-Quads mode', function (done) {
$writer = TriGWriter({'format' => 'N-Triples' });
$writer->addTriple('a', 'b', '"c', function (error) {
    error.should.be.an.instanceof(Exception);
    error.should.have.property('message', 'Invalid literal: "c');
    done();
});
});

        //should end when the end option is not set', function (done) {
$outputStream = new QuickStream(), writer = TriGWriter(outputStream, {});
outputStream.should.have.property('ended', false);
$writer->end(function () {
    outputStream.should.have.property('ended', true);
    done();
});
});

        //should end when the end option is set to true', function (done) {
$outputStream = new QuickStream(), writer = TriGWriter(outputStream, { end: true });
outputStream.should.have.property('ended', false);
$writer->end(function () {
    outputStream.should.have.property('ended', true);
    done();
});
});

        //should not end when the end option is set to false', function (done) {
$outputStream = new QuickStream(), writer = TriGWriter(outputStream, { end: false });
outputStream.should.have.property('ended', false);
$writer->end(function () {
    outputStream.should.have.property('ended', false);
    done();
});
});
});
*/


    /**
     **/
    private function shouldSerialize() {
        $numargs = func_num_args();
        $expectedResult = func_get_arg($numargs-1);
        $i = 0;
        $prefixes = [];
        if (func_get_arg($i) !== 0 && isset(func_get_arg($i)["prefixes"] )) {
            $prefixes = func_get_arg($i)["prefixes"];
            $i++;
        }
        $writer = new TrigWriter(["prefixes"=>$prefixes]);
        for ($i; $i < $numargs-1; $i++) {
            $item = func_get_arg($i);
            $g = isset($item[3])?$item[3]:null;
            $writer->addTriple(["subject"=> $item[0], "predicate"=> $item[1], "object"=> $item[2], "graph" => $g ]);
        }
        $writer->end(function ($error, $output) use ($expectedResult) {
            $this->assertEquals($expectedResult,$output);
        });
    }

    private function shouldNotSerialize() {
        
    }


}
