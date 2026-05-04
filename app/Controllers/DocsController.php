<?php

namespace App\Controllers;

use App\Models\Doc;
use App\Core\Response;

class DocsController
{
    public function index()
    {
        $docs = Doc::pagination(3);

      
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            return Response::success("data found", [
                "docs" => $docs
            ]);
        }

        // 👉 Normal page
        return view("docs");
    }

    public function show($slug)
    { 
      
  
        $doc = Doc::query()
            ->where("slug", $slug)
            ->first();
            
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            return Response::success("data found", [
                "doc" => $doc
            ]);
        }
        
            
  
        return view("doc_show", [
            "doc" => $doc
        ]);
    }
}

