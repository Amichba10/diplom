<?php

namespace App\Http\Controllers;

use App\Models\Document;
use http\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Validation\Validator;

class UploadController extends Controller
{
    public function upload(Request $request)
    {

       try {
            // загрузка файла
            if ($request->isMethod('post') && $request->file('userfile')) {

                $request->validate([

                    'userfile' => 'mimes:docx'
                ]);
                $file = $request->file('userfile');
                $upload_folder = 'public/folder';
                $filename = $file->getClientOriginalName(); // image.jpg

             if (DB::table('documents')->where('name',$filename)->first()){
                 echo 'Файл с таким названием уже существует.';

                 return view('document');
             }

                Storage::putFileAs($upload_folder, $file, $filename);

                $document = new Document();
                $document->name = $filename;
                $document->path = $upload_folder;
                $document->save();
                return redirect()->route('showDocs');
            }

            // Document::create($arr['name'=> $request->file('userfile'),
            //'path' => 'public/folder'])
        } catch (\Exception $exception) {
            echo 'Загружаемый файл не является типом docx.';
        }
        return view('document');

    }
    protected function formatValidationErrors(Validator $validator)
    {
        return $validator->errors()->all();
    }
}

