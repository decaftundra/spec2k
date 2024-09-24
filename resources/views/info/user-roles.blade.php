@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12">
	        <h1>User Roles</h1>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <th>Privileges</th>
                    <th>Data Admin</th>
                    <th>Site Admin</th>
                    <th>Admin</th>
                    <th>User</th>
                    
                    <tr>
                        <td>Can create and edit Spec 2k Segments</td>
                        <td><i class="text-success fas fa-check-circle"></i></td>
                        <td><i class="text-success fas fa-check-circle"></i></td>
                        <td><i class="text-success fas fa-check-circle"></i></td>
                        <td><i class="text-warning fas fa-check-circle"></i></td>
                    </tr>
                    
                    <tr>
                        <td>Can edit admin fields in all Spec 2k Segments</td>
                        <td><i class="text-success fas fa-check-circle"></i></td>
                        <td><i class="text-success fas fa-check-circle"></i></td>
                        <td><i class="text-success fas fa-check-circle"></i></td>
                        <td>&nbsp;</td>
                    </tr>
                    
                    <tr>
                        <td>Can export xml reports</td>
                        <td><i class="text-success fas fa-check-circle"></i></td>
                        <td><i class="text-success fas fa-check-circle"></i></td>
                        <td><i class="text-success fas fa-check-circle"></i></td>
                        <td><i class="text-warning fas fa-check-circle"></i></td>
                    </tr>
                    
                    <tr>
                        <td>Can edit records from the export screen</td>
                        <td><i class="text-success fas fa-check-circle"></i></td>
                        <td><i class="text-success fas fa-check-circle"></i></td>
                        <td><i class="text-success fas fa-check-circle"></i></td>
                        <td><i class="text-warning fas fa-check-circle"></i></td>
                    </tr>
                    
                    <tr>
                        <td>Can create Data Admin users</td>
                        <td><i class="text-success fas fa-check-circle"></i></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    
                    <tr>
                        <td>Can create Site Admins, Admins and standard users</td>
                        <td><i class="text-success fas fa-check-circle"></i></td>
                        <td><i class="text-warning fas fa-check-circle"></i></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    
                    <tr>
                        <td>Can perform all actions (create, update, deactivate) to users</td>
                        <td><i class="text-success fas fa-check-circle"></i></td>
                        <td><i class="text-warning fas fa-check-circle"></i></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    
                    <tr>
                        <td>Can display users</td>
                        <td><i class="text-success fas fa-check-circle"></i></td>
                        <td><i class="text-warning fas fa-check-circle"></i></td>
                        <td><i class="text-warning fas fa-check-circle"></i></td>
                        <td>&nbsp;</td>
                    </tr>
                    
                    <tr>
                        <td>Can display activities</td>
                        <td><i class="text-success fas fa-check-circle"></i></td>
                        <td><i class="text-warning fas fa-check-circle"></i></td>
                        <td><i class="text-warning fas fa-check-circle"></i></td>
                        <td>&nbsp;</td>
                    </tr>
                    
                    <tr>
                        <td>Can display reference information</td>
                        <td><i class="text-success fas fa-check-circle"></i></td>
                        <td><i class="text-success fas fa-check-circle"></i></td>
                        <td><i class="text-success fas fa-check-circle"></i></td>
                        <td><i class="text-success fas fa-check-circle"></i></td>
                    </tr>
                    
                    <tr>
                        <td>Can create and delete locations</td>
                        <td><i class="text-success fas fa-check-circle"></i></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    
                    <tr>
                        <td>Can edit locations</td>
                        <td><i class="text-success fas fa-check-circle"></i></td>
                        <td><i class="text-warning fas fa-check-circle"></i></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    
                    <tr>
                        <td>Can perform all actions (create, update, delete) to Cage Codes list</td>
                        <td><i class="text-success fas fa-check-circle"></i></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    
                    <tr>
                        <td>Can perform all actions (create, update, delete) to location parts list</td>
                        <td><i class="text-success fas fa-check-circle"></i></td>
                        <td><i class="text-warning fas fa-check-circle"></i></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    
                    <tr>
                        <td>Can perform all actions (create, update, delete) to customers</td>
                        <td><i class="text-success fas fa-check-circle"></i></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    
                    <tr>
                        <td>Can perform all actions (create, update, delete) to aircraft details</td>
                        <td><i class="text-success fas fa-check-circle"></i></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </div>
            
            <p>Key: <i class="text-success fas fa-check-circle"></i> = for all sites, <i class="text-warning fas fa-check-circle"></i> = for own site only.</p>
            
        </div>
    </div>
@endsection