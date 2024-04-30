<?php

namespace App\Http\Controllers\pos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Auth;
use Illuminate\support\Carbon;

class CategoryController extends Controller
{
    public function CategoryAll()
    {
        $Categories = Category::latest()->get();
        return view('backend.category.category_all', compact('Categories'));
    }

    public function CategoryAdd()
    {
        return view('backend.category.category_add');
    }

    public function CategoryStore(Request $request)
    {
        Category::insert([
            'name' => $request->name,
            'created_by' => Auth::user()->id,
            'created_at' => Carbon::now(),
        ]);
        $notification = array(
            'message' => 'Category Inserted successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('category.all')->with($notification);
    }

    public function CategoryEdit($id)
    {
        $category = Category::findOrFail($id);
        return view('backend.category.category_edit', compact('category'));
    }

    public function CategoryUpdate(Request $request)
    {
        $category_id = $request->id;
        Category::findOrFail($category_id)->update([
            'name' => $request->name,
            'created_by' => Auth::user()->id,
            'created_at' => Carbon::now(),
        ]);
        $notification = array(
            'message' => 'Category Updated successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('category.all')->with($notification);
    }

    public function CategoryDelete($id)
    {
        Category::findOrFail($id)->delete();
        $notification = array(
                'message' => 'Category Deleted successfully',
                'alert-type' => 'success'
            );
            return redirect()->back()->with($notification);
    }
}
