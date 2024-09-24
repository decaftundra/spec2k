@extends('layouts.default')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Login</div>

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt"></i> Login
                                </button>

                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    Forgot Your Password?
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="alert alert-danger" role="alert">
                <h2 style="margin-top:0;">Information</h2>
                
                <p><i class="fas fa-exclamation-triangle"></i> No Export Controlled Technical Data to be uploaded as attachments and/or
                inserted as free text in any long text fields. Covered defence information is also not permitted.</p>
                
                <p>By logging in the user has read and understood the Export Controlled Technical and DFARS message.</p>
                
                <br>

                <p><i class="fas fa-exclamation-triangle"></i> Aucune donnée technique contrôlée à l'exportation ne peut être téléchargée en tant que pièce jointe
                et/ou insérée dans un texte libre dans n'importe quel champ de texte. Il en va de même pour les informations classifiées de Défense.</p>
                
                <p>En se connectant, l'utilisateur certifie avoir lu et compris les restrictions liées aux données techniques soumises au contrôle des exportations ainsi qu'aux exigences DFARS.</p>
            </div>
        </div>
    </div>
</div>
@endsection
