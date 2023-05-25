<?php

namespace App\Jobs;

use App\Models\Document;
use App\Models\Verb;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;

class InsertVerbsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Document $document)
    {
        $v = $this->parse($document);
        $exceptionalWords = DB::table("exceptionals")
            ->where("status", "APPROVED")
            ->pluck("word")
            ->toArray();

        $diff = array_diff($v, $exceptionalWords);

        $data = [];
        foreach ($diff as $word) {
            $data[] = ['word' => $word, 'filename' => $document->name];
        }

        Verb::insert($data);
        DB::statement("DELETE FROM verbs
WHERE
        id IN (
        SELECT
            id
        FROM (
            SELECT
                     id,
                     ROW_NUMBER() OVER (
        PARTITION BY word
                         ORDER BY word) AS row_num
                 FROM
                     verbs WHERE
                               filename = '$document->name'

             ) t
        WHERE row_num > 1
    )
");

//  foreach ($diff as $key => $value){
//            DB::insert('insert into verbs (word,filename) values (?,?)',[$value, $document->name]);
//
//        }

        // получить массив исключений
        // отнять от массива $v массив исключений array_diff

        //return view("parsetodisplay", compact("res"));

    }

    public function parse(Document $document)
    {
        //  echo $document->all('path');

        //$objReader = PhpOffice\PhpWord\IOFactory::createReader('Word2007');
        //    $objReader = PHPWord_IOFactory::createReader('Word2007');
        error_reporting(0);
       // echo $document->name;
        //Log::error($document->name);

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
        $arr = explode(" ", $bodyres);
       /* $arr = array_unique(explode(" ", $bodyres));
        $arr = array_diff($arr, array(''));
        $arr = array_values($arr);
*/
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
}
