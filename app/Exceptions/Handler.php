<?php

namespace App\Exceptions;

use App\User;
use Throwable;
use App\Mail\ErrorMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use App\Exceptions\RecordCountException;
use App\Exceptions\SAPFeedException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];
    
    /**
     * A list of the internal exception types that should not be reported.
     *
     * @var array
     */
    protected $internalDontReport = [
        AuthenticationException::class,
        AuthorizationException::class,
        HttpException::class,
        HttpResponseException::class,
        RequestException::class, // Regular exceptions thrown from Azure Application Insights telemetry.
        ServerException::class, // Regular exceptions thrown from Azure Application Insights telemetry.
        //ModelNotFoundException::class,
        TokenMismatchException::class,
        ValidationException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];
    
    /**
     * A collection of users.
     *
     * @var Illuminate\Database\Eloquent\Collection
     */
    protected $users;

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        if ($this->shouldntReport($exception)) {
            return;
        }
        
        // If there is an error in the SAP data import, notify admin users.
        if (basename(($exception->getFile()) == 'GetLatestNotificationsAndPieceParts.php') || ($exception instanceof RecordCountException) || ($exception instanceof SAPFeedException)) {
            $this->sendMessage($exception, 'SAP Feed Error');
        } else {
            $this->sendMessage($exception, 'General Error');
        }
        
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }
    
    /**
     * Get the default context variables for logging.
     *
     * @return array
     */
    protected function context()
    {
        try {
            return array_filter([
                'url' => Request::fullUrl(),
                'input' => Request::except(['password', 'password_confirmation']),
                'userId' => Auth::id(),
                'email' => Auth::user() ? Auth::user()->email : null,
            ]);
        } catch (Throwable $e) {
            return [];
        }
    }
    
    /**
     * Send the message to all users that want the given message type.
     *
     * @param  \Throwable  $exception
     * @param  string  $messageName
     * @return void
     */
    protected function sendMessage(Throwable $exception, $messageName)
    {
        $this->users = User::get();
        
        if (count($this->users)) {
            foreach ($this->users as $user) {
                if ($user->wantsMessage($messageName)) {
                    Mail::to($user)->send(new ErrorMessage($user, $messageName, $exception));
                }
            }
        }
    }
}
