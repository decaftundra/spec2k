<?php

namespace App\Http\Controllers;

use App\Role;
use App\Alert;
use App\Customer;
use Illuminate\Http\Request;
use App\Policies\CustomerPolicy;
use App\Http\Requests\CustomerRequest;

class CustomerController extends Controller
{
    /**
     * Whitelist of allowed orderby parameters.
     *
     * @var array
     */
    public static $orderbyWhitelist = [
        'name' => 'company_name',
        'code' => 'icao'
    ];
    
    /**
     * The default order by column.
     *
     * @var string
     */
    public static $defaultOrderBy = 'name';
    
    /**
     * The default order.
     *
     * @var string
     */
    public static $defaultOrder = 'asc';
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('index', Customer::class);
        
        $search = $request->search;
        $orderby = self::$orderbyWhitelist[self::$defaultOrderBy];
        $order = self::$defaultOrder;
        
        if ($request->has('orderby') && array_key_exists($request->orderby, self::$orderbyWhitelist)) {
            $orderby = self::$orderbyWhitelist[$request->orderby];
        }
        
        if ($request->has('order') && in_array($request->order, ['desc', 'asc'])) {
            $order = $request->order;
        }
        
        $customers = Customer::search($search)->orderBy($orderby, $order)->paginate(20);
        
        $request->merge(['order' => $order == 'asc' ? 'desc' : 'asc'])->flash(); // swap order
        
        return view('customers.index')->with('customers', $customers);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Customer::class);
        
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerRequest $request)
    {
        $this->authorize('create', Customer::class);
        
        $customer = new Customer;
        $customer->company_name = $request->company_name;
        $customer->icao = $request->icao ? $request->icao : 'ZZZZZ';
        $customer->save();
        
        return redirect(route('customer.index'))->with(Alert::success('New customer created successfully!'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Customer $customer)
    {
        $this->authorize('edit', $customer);
        
        return view('customers.edit')->with('customer', $customer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerRequest $request, Customer $customer)
    {
        $this->authorize('edit', $customer);
        
        $customer->company_name = $request->company_name;
        $customer->icao = $request->icao ? $request->icao : 'ZZZZZ';
        $customer->save();
        
        return redirect(route('customer.index'))->with(Alert::success('Customer updated successfully!'));
    }
    
    /**
     * Show the form for deleting the specified resource.
     *
     * @param \App\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function delete(Customer $customer)
    {
        $this->authorize('delete', $customer);
        
        return view('customers.delete')->with('customer', $customer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomerRequest $request, Customer $customer)
    {
        $this->authorize('delete', $customer);
        
        $customer->delete();
        
        return redirect(route('customer.index'))->with(Alert::success('Customer deleted successfully!'));
    }
}
