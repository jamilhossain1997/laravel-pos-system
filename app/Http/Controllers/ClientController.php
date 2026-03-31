<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::query()
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%$s%")
                ->orWhere('phone', 'like', "%$s%")->orWhere('email', 'like', "%$s%"))
            ->when($request->type, fn($q, $t) => $q->where('type', $t))
            ->withCount('invoices')
            ->withSum('invoices', 'total')
            ->latest()->paginate(20);
        return view('clients.index', compact('clients'));
    }
    public function create()
    {
        return view('clients.create');
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:200',
            'phone'      => 'nullable|string|max:30',
            'email'      => 'nullable|email|max:200',
            'address'    => 'nullable|string',
            'company'    => 'nullable|string|max:200',
            'vat_number' => 'nullable|string|max:50',
            'type'       => 'required|in:retail,wholesale',
        ]);
        Client::create($data);
        return redirect()->route('clients.index')->with('success', 'Client created successfully!');
    }
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }
    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'name' => 'required',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'address' => 'nullable',
            'company' => 'nullable',
            'vat_number' => 'nullable',
            'type' => 'required|in:retail,wholesale',
            'is_active' => 'boolean',
        ]);
        $client->update($data);
        return redirect()->route('clients.index')->with('success', 'Client updated!');
    }
    public function destroy(Client $client)
    {
        $client->delete();
        return back()->with('success', 'Client deleted!');
    }
    public function ledger(Client $client)
    {
        $invoices = $client->invoices()->with('items')->latest()->paginate(15);
        return view('clients.ledger', compact('client', 'invoices'));
    }
}
