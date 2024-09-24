<?php

namespace App\Http\Controllers;

use App\Alert;
use App\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $userMessages = auth()->user()->messages->pluck('id')->toArray();
        
        $messages = Message::get();
        
        return view('message.edit')
            ->with('messages', $messages)
            ->with('userMessages', $userMessages);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if ($request->has('messages')) {
            $request->validate([
                'messages' => 'array'
            ]);
            
            auth()->user()->messages()->sync($request->messages);
        } else {
            auth()->user()->messages()->sync([]);
        }
        
        return redirect(route('message.edit'))
            ->with(Alert::success('Message settings saved successfully!'));
    }
}
