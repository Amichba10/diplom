<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PHPWord_IOFactory;
use Psy\Util\Str;

class ShowDocsController extends Controller
{
    public function showDocs()
    {
        $documents = Document::all();
        return view('showdocs', ['documents' => $documents]);
    }

    public function parseToFile(Document $document)
    {
        $out = $this->parse($document, true);

        $phpWord = new PhpWord();
//    $phpWord = new PhpOffice\PhpWord\PhpWord();

        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(14);

        $properties = $phpWord->getDocInfo();
        $properties->setCreator('My name');
        $properties->setCompany('My factory');
        $properties->setTitle('My title');
        $properties->setDescription('My description');
        $properties->setCategory('My category');
        $properties->setLastModifiedBy('My name');
        $properties->setCreated(mktime(0, 0, 0, 3, 12, 2014));
        $properties->setModified(mktime(0, 0, 0, 3, 14, 2014));
        $properties->setSubject('My subject');
        $properties->setKeywords('my, key, word');

        $sectionStyle = array();
        $section = $phpWord->addSection($sectionStyle);

        $text = 'Глаголы в тексте без повторений:';
        $phpWord->addParagraphStyle('Content', array('bold' => false, 'align' => 'center'));
        $section->addText($text, null, 'Content');

        $section->addText(
            $out,
            array(),
            array()
        );

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
//    $objWriter = PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save(Storage::path($document->path . '/' . 'doc1.docx'));
        return redirect()->back();
    }

    public function parseToDisplay(Document $document)
    {
        $v = $this->parse($document);
        return view('parsetodisplay', compact('v'));
    }

    public function parse(Document $document, bool $isParseToFile = false)
    {
        //  echo $document->all('path');

        //$objReader = PhpOffice\PhpWord\IOFactory::createReader('Word2007');
//    $objReader = PHPWord_IOFactory::createReader('Word2007');
        error_reporting(0);
        $objReader = IOFactory::createReader('Word2007');
        $phpWord = $objReader->load(Storage::path($document->path . '/' . $document->name));

        $body = '';

        foreach ($phpWord->getSections() as $section) {
            $arrays = $section->getElements();

            foreach ($arrays as $e) {
                if (get_class($e) === 'PhpOffice\PhpWord\Element\TextRun') {
                    foreach ($e->getElements() as $text) {
                        $font = $text->getFontStyle();
                        $size = $font->getSize() / 10;
                        $bold = $font->isBold() ? 'font-weight:700;' : '';
                        $color = $font->getColor();
                        $fontFamily = $font->getName();
                        $body .= $text->getText();
                    }
                } else if (get_class($e) === 'PhpOffice\PhpWord\Element\TextBreak') {
                    $body .= '<br />';
                } else {
                    get_class($e);
                }
            }
            break;
        }

//echo '<p><b>Текст находяйщийся в файле:</b></p>';
//echo $body;
        $bodyres = preg_replace("/(?!.[.=$'€%-])\p{P}/u", "", mb_convert_case($body, MB_CASE_LOWER));
//echo $bodyres;
        $arr = array_unique(explode(' ', $bodyres));
        for ($i = 0; $i < count($arr); $i++) {
            $arr[$i] = $arr[$i] . "<br>";
        }

        $res = implode(' ', $arr);
        $delpr = str_replace(' ', '', $res);

        //echo '<p><b>Слова в тексте без повторений:</b></p>';
        //echo $res;

        $out = '';
        preg_match_all('/[a-ҿҽәӡӷҚԥ,-]{3,}(ит|еит|оит|уп|он|ан)\b/ui', $delpr, $v);
        //dd($v[0]);
        foreach ($v[0] as $key => $value) {
            if (count($v[0]) - 1 == $key) {
                $out .= $value . "<br>";
            } else {
                $out .= $value . "<br>";
            }
        }

        if ($isParseToFile) {
            $out = str_replace("<br>", "<w:br/>", $out);
            return $out;
        }

        return $v[0];
    }
}

