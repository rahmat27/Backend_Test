<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use Validator;

class VideoController extends Controller
{

    public function Content(Request $request)
    {   
        
        if($request->method() == 'POST') {
            $input = $request->all();
            $rules = [
                'name'=> 'required|max:200|unique:videos',
                'file' => 'required|mimes:mp4, 3gp|max:20000'
            ];

            $customMessages = [
                'name.required' => 'Nama wajib di isi.',
                'name.unique' => 'Nama Sudah diambil.',
                'file.required' => 'File wajib di isi.'
            ];

            $validator = Validator::make( $input, $rules, $customMessages );

            if ($validator->fails()) {
                $error = $validator->messages()->toArray();
                $error = $this->array_flatten($error);

                $errorParam = [
                        "meta" => [
                            "code" => 400,
                            "message" => $error
                        ]
                ];

                return response()->json($errorParam);
            }

            
            if ($request->hasFile('file')) {
                $video = $request->file('file');
                $filename = pathinfo($video->getClientOriginalName(), PATHINFO_FILENAME);
                $name = $input['name'];
                $name = str_replace(' ', '_', $name);
                $destinationPath =  public_path('gallery/');

                $data['name'] =  $name;
                $data['file_path'] = 'gallery/' . $name;                
                $data['file_ekstension'] = $video->extension();
                $data['file_type'] =  $video->getMimeType();

                $video->move($destinationPath, $name);
            }
            
            $save = Video::create($data);
            
            $success = [
                "meta" => [
                    "code" => 201,
                    "message" => 'Video berhasil ditambahkan',
                    ]
                ];
                
            return response()->json($success);
                
        }

        else{

            $host = request()->getHttpHost();

            $content = Video::get();

            foreach ($content as $value) {
                $name = $value->name;
                $value['file_url'] = $host .'gallery/' . $name;
            }
                
                $data = [
                    "meta" => [
                        "code" => 200,
                        "message" => 'Berhasil mengambil data',
                        "data" => $content,
                    ]
                ];

                return response()->json($data);
            }
            
        }
                    
        public function array_flatten($array) { 
            if (!is_array($array)) { 
                return FALSE; 
            }
        
            $result = array(); 
            foreach ($array as $key => $value) { 
                
                if (is_array($value)) { 
                    $result = array_merge($result, $this->array_flatten($value));
                    
                } else { 
                    $result[$key] = $value; 
                }
                
            }
        
        return $result; 
    }
    public function test(){
        // test commit
        // test conflict file
    }
    
}
