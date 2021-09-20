<?php

namespace App\Services\Item;

use Illuminate\Support\Str;

//Interface
use App\Contracts\Item\CategoryRepositoryInterface;

//Resources
use App\Http\Resources\PaginationResource;

class CategoryServices{
    
    private $repositoryInterface;

    public function __construct(CategoryRepositoryInterface $repositoryInterface){
        $this->ri = $repositoryInterface;
    }

    public function categoryCreate($request){
        $fields = $request->validate([
            'name'=>'required|string|unique:categories,name',
        ]);

        $category = $this->ri->categoryCreate([
            'name' => $fields['name'],
            'slug' => Str::slug($fields['name'])
        ]);

        return response($category,201);
    }

    public function categoryUpdate($request, $id){
        $fields = $request->validate([
            
        ]);

        $category = $this->ri->categoryGetById($id);
        if($category){
            $data = $request->all();
            if($category->name==$data['name']){
                $fields = $request->validate([
                    'name'=>'required|string|max:255',
                ]);
            }
            else{
                $fields = $request->validate([
                    'name'=>'required|string|max:255|unique:categories,name',
                ]);
            }
            $data['slug'] = Str::slug($fields['name']);
            $category->update($data);
            return response($category,201);
        }else{
            return response(["failed"=>'Category not found'],404);
        }
    }
}