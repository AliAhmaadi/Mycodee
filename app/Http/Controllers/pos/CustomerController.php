<?php

namespace App\Http\Controllers\pos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Auth;
use Illuminate\support\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class CustomerController extends Controller
{
    public function CustomerAll()
    {
        $customers = Customer::latest()->get();
        return view('backend.customer.customer_all', compact('customers'));
    }

    public function CustomerAdd()
    {
        return view('backend.customer.customer_add');
    }

    public function CustomerStore(Request $request)
    {
        if ($request->file('customer_img')) {
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()) . '.' . $request->file('customer_img')->getClientOriginalExtension();
            $img = $manager->read($request->file('customer_img'));
            $img = $img->resize(200, 200);
            $img->toJpeg(80)->save(base_path('public/upload/customer/'.$name_gen));
            $save_url = 'upload/customer' .$name_gen;
            Customer::insert([
                'name' => $request->name,
                'mobile_nunber' => $request->mobile_nunber,
                'email' => $request->email,
                'address' => $request->address,
                'customer_img' => $save_url,
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now(),
            ]);
            
        } //end if
        
        $notification = array(
            'message' => 'Customer Inserted successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('customer.all')->with($notification);
    }

    public function CustomerEdit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('backend.customer.customer_edit', compact('customer'));
    }

    public function CustomerUpdate(Request $request)
{
    $customer_id = $request->id;
    $customer = Customer::findOrFail($customer_id); // Retrieve the customer by ID

    if ($request->file('customer_img')) {
         $image = $request->file('customer_img');
         $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
         // Image::make($image)->resize(200, 200)->save('upload/customer/'.$name_gen); 
         $save_url = 'upload/customer' .$name_gen;
         $customer->update([
            'name' => $request->name,
            'mobile_nunber' => $request->mobile_nunber,
            'email' => $request->email,
            'address' => $request->address,
            'customer_img' => $save_url,
            'updated_by' => Auth::user()->id,
            'updated_at' => Carbon::now(),
        ]);
        $notification = array(
            'message' => 'Customer Updated With Image successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('customer.all')->with($notification);
    }
    else
    {
        $customer->update([
            'name' => $request->name,
            'mobile_nunber' => $request->mobile_nunber,
            'email' => $request->email,
            'address' => $request->address,
            'updated_by' => Auth::user()->id,
            'updated_at' => Carbon::now(),
        ]);
        $notification = array(
            'message' => 'Customer Updated Without Image successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('customer.all')->with($notification);
    }

}

    public function CustomerDelete($id)
    {
        $customers = Customer::findOrFail($id);
        $img = $customers->customer_img;
        // unlink($img);

        Customer::findOrFail($id)->delete();
        $notification = array(
            'message' => 'Customer Delete successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }

}
