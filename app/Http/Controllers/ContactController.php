<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    // Store a new contact message
    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        // Create a new contact record in the database
        $contact = Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
        ]);

        // Return a response
        return response()->json(['message' => 'Contact message has been sent successfully!', 'data' => $contact], 201);
    }

    // Get all contacts
    public function index()
    {
        $contacts = Contact::all();
        return response()->json(['contacts' => $contacts]);
    }

    // Get a specific contact by ID
    public function show($id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json(['message' => 'Contact not found.'], 404);
        }

        return response()->json(['contact' => $contact]);
    }

    // Update a specific contact by ID
    public function update(Request $request, $id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json(['message' => 'Contact not found.'], 404);
        }

        // Validate the incoming request data
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'message' => 'sometimes|string',
        ]);

        // Update the contact data
        $contact->update($request->only(['name', 'email', 'message']));

        return response()->json(['message' => 'Contact updated successfully!', 'data' => $contact]);
    }

    // Delete a specific contact by ID
    public function destroy($id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json(['message' => 'Contact not found.'], 404);
        }

        // Delete the contact record
        $contact->delete();

        return response()->json(['message' => 'Contact message deleted successfully.']);
    }
}
