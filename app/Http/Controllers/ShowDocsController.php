<?php

namespace App\Http\Controllers;

use App\Jobs\InsertVerbsJob;
use App\Models\Document;
use App\Models\Exceptional;
use App\Models\Verb;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class ShowDocsController extends Controller
{
    public function showDocs()
    {
        $documents = Document::all();
        return view("showdocs", ["documents" => $documents]);
    }

    public function parseToFile(Document $document)
    {
        $v = $this->parse($document);
        $exceptionalWords = DB::table("exceptionals")
            ->where("status", "APPROVED")
            ->pluck("word")
            ->toArray();

        $diff = array_diff($v, $exceptionalWords);
        $out = "";
        foreach ($diff as $key => $value) {
            if (count($diff) - 1 == $key) {
                $out .= $value . "<br>";
            } else {
                $out .= $value . "<br>";
            }
        }
        $out = str_replace("<br>", "<w:br/>", $out);

        $phpWord = new PhpWord();
        //    $phpWord = new PhpOffice\PhpWord\PhpWord();

        $phpWord->setDefaultFontName("Times New Roman");
        $phpWord->setDefaultFontSize(14);

        $properties = $phpWord->getDocInfo();
        $properties->setCreator("My name");
        $properties->setCompany("My factory");
        $properties->setTitle("My title");
        $properties->setDescription("My description");
        $properties->setCategory("My category");
        $properties->setLastModifiedBy("My name");
        $properties->setCreated(mktime(0, 0, 0, 3, 12, 2014));
        $properties->setModified(mktime(0, 0, 0, 3, 14, 2014));
        $properties->setSubject("My subject");
        $properties->setKeywords("my, key, word");

        $sectionStyle = [];
        $section = $phpWord->addSection($sectionStyle);

        $text = "Глаголы в тексте без повторений:";
        $phpWord->addParagraphStyle("Content", [
            "bold" => false,
            "align" => "center",
        ]);
        $section->addText($text, null, "Content");

        $section->addText($out, [], []);

        $objWriter = IOFactory::createWriter($phpWord, "Word2007");
        $objWriter->save(Storage::path($document->path . "/" . "parsing.docx"));
        return redirect()->back();
    }

    public function parseToDisplay(Document $document)
    {
        $job = new InsertVerbsJob();

        $job->handle($document);

        return redirect()->route('showVerbs',compact('document'));
    }

    public function parse(Document $document)
    {
        //  echo $document->all('path');

        //$objReader = PhpOffice\PhpWord\IOFactory::createReader('Word2007');
        //    $objReader = PHPWord_IOFactory::createReader('Word2007');
        error_reporting(0);
        $objReader = IOFactory::createReader("Word2007");
        $phpWord = $objReader->load(
            Storage::path($document->path . "/" . $document->name)
        );

        $body = "";

        foreach ($phpWord->getSections() as $section) {
            $arrays = $section->getElements();

            foreach ($arrays as $e) {
                if (get_class($e) === "PhpOffice\PhpWord\Element\TextRun") {
                    foreach ($e->getElements() as $text) {
                        $font = $text->getFontStyle();
                        $size = $font->getSize() / 10;
                        $bold = $font->isBold() ? "font-weight:700;" : "";
                        $color = $font->getColor();
                        $fontFamily = $font->getName();
                        $body .= $text->getText();
                    }
                } elseif (
                    get_class($e) === "PhpOffice\PhpWord\Element\TextBreak"
                ) {
                    $body .= "<br />";
                } else {
                    get_class($e);
                }
            }
            break;
        }

        //echo '<p><b>Текст находяйщийся в файле:</b></p>';
        //echo $body;
        $bodyres = preg_replace(
            "/(?!.[.=$'€%-])\p{P}/u",
            "",
            mb_convert_case($body, MB_CASE_LOWER)
        );
        //echo $bodyres;
        $arr = array_unique(explode(" ", $bodyres));
        $arr = array_diff($arr, array(''));
        $arr = array_values($arr);

        for ($i = 0; $i < count($arr); $i++) {
            $arr[$i] = $arr[$i] . "<br>";
        }

        $res = implode(" ", $arr);
        $delpr = str_replace(" ", "", $res);

        //echo '<p><b>Слова в тексте без повторений:</b></p>';
        //echo $res;

        preg_match_all(
            '/[a-ҿҽәӡӷҚԥ,-]{3,}(ит|еит|оит|уп|он|ан|хьаз|хьоу|лак|цыԥхьаӡа|
                          аанӡа|ҵауа|руа|аҵо|ҵар|ҵара|ҵаша|аанӡа|ӡом|ҵамкәа|ҵазар|ндаз|ижьҭеи|
                          наҵы|зҭгьы|ҵашәа)\b/ui',
            $delpr,
            $v
        );

        return $v[0];
    }

    public function addExceptional(Request $request)
    {
        //        dd($request);
        $word = $request->post("word");

        $exceptional = new Exceptional();
        $exceptional->word = $word;
        $exceptional->status = Exceptional::STATUS_NEW;
        $exceptional->save();

        return response()->json(['success'=>'Добавлено']);
    }
    public function showVerbs(Document $document)
    {
        $verbs = DB::table("verbs")
            ->where("filename", $document->name)
            ->paginate(50);
        return view("showverbs", ["verbs" => $verbs]);
    }
}

