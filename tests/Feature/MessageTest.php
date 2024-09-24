<?php

namespace Tests\Feature;

use App\User;
use App\Message;
use Tests\TestCase;
use App\Mail\ErrorMessage;
use Illuminate\Support\Facades\Mail;
use App\Exceptions\RecordCountException;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MessageTest extends TestCase
{
    /**
     * Test a user can add and remove messages in their message settings.
     *
     * @return void
     */
    public function testUserCanAddMessageToMessageSettings()
    {
        $randomMessageName = Message::inRandomOrder()->first()->name;
        
        $this->actingAs($this->user)
             ->get(route('message.edit'))
             ->assertSee($randomMessageName)
             ->assertStatus(200);
             
        $messages = Message::pluck('id')->toArray();
             
        $attributes = ['messages' => $messages];
        
        $response = $this->actingAs($this->user)
                         ->call('PUT', route('message.update'), $attributes)
                         ->assertStatus(302);
             
        $this->get($response->headers->get('Location'))
             ->assertSee('Message settings saved successfully!')
             ->assertStatus(200);
             
        foreach ($attributes as $messageId) {
            $this->assertDatabaseHas('message_user', ['user_id' => $this->user->id, 'message_id' => $messageId]);
        }
        
        $response = $this->actingAs($this->user)
                         ->call('PUT', route('message.update'), [])
                         ->assertStatus(302);
                         
        $this->get($response->headers->get('Location'))
             ->assertSee('Message settings saved successfully!')
             ->assertStatus(200);
             
        $this->assertDatabaseMissing('message_user', ['user_id' => $this->user->id]);
    }
    
    /**
     * Test throwing RecordCountException sends a SAP Feed Error message to all relevant users.
     *
     * @return void
     */
    public function testRecordCountExceptionSendsMessageToRelevantUsers()
    {
        Mail::fake();
        
        $sapFeedError = Message::where('name', 'SAP Feed Error')->pluck('id')->toArray();
        
        $attributes = ['messages' => $sapFeedError];
        
        $response = $this->actingAs($this->user)
                         ->call('PUT', route('message.update'), $attributes)
                         ->assertStatus(302);
             
        $this->get($response->headers->get('Location'))
             ->assertSee('Message settings saved successfully!')
             ->assertStatus(200);
             
        $message = 'Notifications count mismatch! Expected: ' . 10 . '. but counted: ' . 3;
        
        $this->expectException(RecordCountException::class);
        
        $exception = new RecordCountException($message);
                
        throw $exception;
        
        // Assert the message is sent to the user.
        Mail::assertSent(ErrorMessage::class, function ($mail) use ($exception) {
            return ($mail->user->id === $this->user->id)
                   && ($mail->messageName === 'SAP Feed Error')
                   && ($mail->exception === $exception);
        });
        
        // Assert the message is not sent to other users.
        Mail::assertNotSent(ErrorMessage::class, function ($mail) {
            return ($mail->user->id === $this->adminUser->id);
        });
    }
    
    /**
     * Test throwing generic Exception sends a General Error message to all relevant users.
     *
     * @return void
     */
    public function testExceptionSendsMessageToRelevantUsers()
    {
        Mail::fake();
        
        $generalError = Message::where('name', 'General Error')->pluck('id')->toArray();
        
        $attributes = ['messages' => $generalError];
        
        $response = $this->actingAs($this->user)
                         ->call('PUT', route('message.update'), $attributes)
                         ->assertStatus(302);
             
        $this->get($response->headers->get('Location'))
             ->assertSee('Message settings saved successfully!')
             ->assertStatus(200);
             
        $message = 'Oops! Something went wrong!';
        
        $this->expectException(\Exception::class);
        
        $exception = new \Exception($message);
                
        throw $exception;
        
        // Assert the message is sent to the user.
        Mail::assertSent(ErrorMessage::class, function ($mail) use ($exception) {
            return ($mail->user->id === $this->user->id)
                   && ($mail->messageName === 'General Error')
                   && ($mail->exception === $exception);
        });
        
        // Assert the message is not sent to other users.
        Mail::assertNotSent(ErrorMessage::class, function ($mail) {
            return ($mail->user->id === $this->adminUser->id);
        });
    }
}
