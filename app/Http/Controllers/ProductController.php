<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;
use File;

class ProductController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:product-list|product-create|product-edit|product-delete', ['only' => ['index','show']]);
         $this->middleware('permission:product-create', ['only' => ['create','store']]);
         $this->middleware('permission:product-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::latest()->paginate(5);
        return view('products.index',compact('products'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('products.create');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        request()->validate([
            'name' => 'required',
            'detail' => 'required',

        ]);

        $image=[];
        if($request->hasfile('image'))
         {
             $image = $request->file('image');
             //dd($image);
             foreach($image as $images)
             $name =$images->getClientOriginalName();
             $images->move(public_path().'/images/',$name);
             $data[]=$name;
             $data['image'] = $name; 
             
         }

        Product::create($data);


        return redirect()->route('products.index')
                        ->with('success','Product created successfully.');
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return view('products.show',compact('product'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        return view('products.edit',compact('product'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Product $product)
    {   
        $data = $request->all();

         request()->validate([
            'name' => 'required',
            'detail' => 'required',
            'image' =>'required',
        ]);

        //dd($product);
        //dd($request->all());
        if($request->hasfile('image'))
         {
            $data = $request->input('image');
            $photo = $request->file('image')->getClientOriginalName();
            $destination = base_path().'/public/images';
            $request->file('image')->move($destination, $photo);
            /*$image = $request->file('image'); $path = public_path().'/images/'.$product->image;            
            /*if(File::exists($path))*/ 
            /*File::delete($path);*/                                    
            /*$name =$image->getClientOriginalName();
            $image->move(public_path().'/images/',$name);$data[]=$name;        
            $data['image'] = $name;*/
            $product->update($data);                        
         }else{
            //dd($request->all());
            $product->update($data);
            }
        return redirect()->route('products.index')
                        ->with('success','Product updated successfully');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
                        ->with('success','Product deleted successfully');
    }
}