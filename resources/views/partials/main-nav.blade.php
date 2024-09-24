<div class="row">
    <nav style="margin-top:20px;" class="navbar navbar-default">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ route('notifications.index') }}">
                    <img src="{{ asset('images/Meggitt_360.png') }}" alt="{{ config('app.name', 'Laravel') }}" width="100" height="19" />
                </a>
            </div>
        
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                @auth
                    <ul class="nav navbar-nav">
                        <li role="presentation" class="{{ set_active('notifications*') ? 'active' : '' }}">
                            <a href="{{ route('notifications.index') }}">
                                <i class="fas fa-file-alt"></i> To Do
                            </a>
                        </li>
        
                        <li role="presentation" class="{{ set_active('dataset*') ? 'active' : '' }}">
                            <a href="{{ route('datasets.index') }}">
                                <i class="fas fa-book"></i> In Progress
                            </a>
                        </li>
                        
                        <li class="dropdown {{ set_active('status*') ? 'active' : '' }}">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-archive"></i> Other <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li role="presentation" class="{{ set_active('status/on-standby*') ? 'active' : '' }}">
                                    <a href="{{ route('standby.index') }}">
                                        <i class="fas fa-pause-circle"></i> Standby
                                    </a>
                                </li>
                                <li role="presentation" class="{{ set_active('status/deleted*') ? 'active' : '' }}">
                                    <a href="{{ route('deleted.index') }}">
                                        <i class="fas fa-trash-alt"></i> Deleted
                                    </a>
                                </li>
                            </ul>
                        </li>
                        
                        <li role="presentation" class="{{ set_active('export-reports*') ? 'active' : '' }}">
                            <a href="{{ route('reports.export') }}">
                                <i class="fas fa-download"></i> Export
                            </a>
                        </li>
        
                        @can('view-all-notifications')
                            <li class="dropdown {{ set_active('users*') ? 'active' : '' }}">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-cog"></i> Admin <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li role="presentation" class="{{ set_active('users*') ? 'active' : '' }}">
                                        <a href="{{ route('user.index') }}">
                                            <i class="fas fa-users"></i> Users
                                        </a>
                                    </li>
                                    <li role="presentation" class="{{ set_active('*activity') ? 'active' : '' }}">
                                        <a href="{{ route('activity.index') }}">
                                            <i class="fas fa-briefcase"></i> Activity
                                        </a>
                                    </li>
                                    
                                    @can('create', App\Customer::class)
                                        <li role="presentation" class="{{ set_active('*customers') ? 'active' : '' }}">
                                            <a href="{{ route('customer.index') }}">
                                                <i class="fas fa-address-book"></i> Customers
                                            </a>
                                        </li>
                                    @endcan
                                    
                                    @can('index', App\Location::class)
                                        <li role="presentation" class="{{ set_active('locations*') ? 'active' : '' }}">
                                            <a href="{{ route('location.index') }}">
                                                <i class="fas fa-map-marker-alt"></i> Repair Stations
                                            </a>
                                        </li>
                                    @endcan
                                    
                                    @can('view-all-cage-codes')
                                        <li role="presentation" class="{{ set_active('cage-codes*') ? 'active' : '' }}">
                                            <a href="{{ route('cage-code.index') }}">
                                                <i class="fas fa-barcode"></i> MFR Cage Codes
                                            </a>
                                        </li>
                                    @endcan
                                    
                                    @can('index', App\PartList::class)
                                        <li role="presentation" class="{{ set_active('part-lists*') ? 'active' : '' }}">
                                            <a href="{{ route('part-list.index') }}">
                                                <i class="fas fa-clipboard-list"></i> Excluded PN
                                            </a>
                                        </li>
                                    @endcan
                                    
                                    @can('create', App\MaintenanceNotice::class)
                                        <li role="presentation" class="{{ set_active('maintenance-notice*') ? 'active' : '' }}">
                                            <a href="{{ route('maintenance-notice.index') }}">
                                                <i class="fas fa-comment-alt"></i> Maintenance Notices
                                            </a>
                                        </li>
                                    @endcan
                                    @can('create', App\BoeingData::class)
                                        <li role="presentation" class="{{ set_active('api-client-management*') ? 'active' : '' }}">
                                            <a href="{{ route('api.client-management') }}">
                                                <i class="fas fa-key"></i> Api Clients
                                            </a>
                                        </li>
                                    @endcan
                                    
                                    <li role="separator" class="divider"></li>
                                    
                                    <li class="dropdown-header">Power BI Data</li>
                                    
                                    @can('view-all-locations')
                                        <li role="presentation" class="{{ set_active('power-bi*') ? 'active' : '' }}">
                                            <a href="{{ route('power-bi.index') }}">
                                                <i class="fas fa-database"></i> Power BI Data
                                            </a>
                                        </li>
                                    @endcan
                                    
                                    <li role="separator" class="divider"></li>
                                    
                                    <li class="dropdown-header">Legacy Data</li>
                                    
                                    @can('view-all-locations')
                                        <li role="presentation" class="{{ set_active('csv-importer*') ? 'active' : '' }}">
                                            <a href="{{ route('csv-importer.create') }}">
                                                <i class="fas fa-file-csv"></i> Legacy Data CSV Import
                                            </a>
                                        </li>
                                    @endcan
                                    
                                    <li role="separator" class="divider"></li>
                                    
                                    <li class="dropdown-header">Collins/Utas Data</li>
                                    
                                    @can('view-all-locations')
                                        <li role="presentation" class="{{ set_active('utas-codes*') ? 'active' : '' }}">
                                            <a href="{{ route('utas-data.utas-codes') }}">
                                                <i class="fas fa-file-csv"></i> Manage Collins/Utas Codes
                                            </a>
                                        </li>
                                        <li role="presentation" class="{{ set_active('utas-part-numbers*') ? 'active' : '' }}">
                                            <a href="{{ route('utas-data.utas-part-numbers') }}">
                                                <i class="fas fa-file-csv"></i> Manage Collins/Utas Part Numbers
                                            </a>
                                        </li>
                                        <li role="presentation" class="{{ set_active('utas-reason-codes*') ? 'active' : '' }}">
                                            <a href="{{ route('utas-data.utas-reason-codes') }}">
                                                <i class="fas fa-file-csv"></i> Manage Collins/Utas Reason Codes
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endcan
                        
                        <li class="dropdown {{ set_active('information*') ? 'active' : '' }}">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-info-circle"></i> Reference <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li role="presentation">
                                    <a target="_blank" href="https://spec2kapp-production.azurewebsites.net/UserManual/S2k%20Online%20Reporting%20Manual.pdf">
                                        <i class="far fa-life-ring"></i> User Manual
                                    </a>
                                </li>
                                <li role="separator" class="divider"></li>
                                <li role="presentation" class="{{ set_active('information/customers*') ? 'active' : '' }}">
                                    <a href="{{ route('info.customers') }}">
                                        <i class="fas fa-address-book"></i> Customers
                                    </a>
                                </li>
                                <li role="presentation" class="{{ set_active('information/locations*') ? 'active' : '' }}">
                                    <a href="{{ route('info.locations') }}">
                                        <i class="fas fa-map-marker-alt"></i> Repair Stations
                                    </a>
                                </li>
                                <li role="presentation" class="{{ set_active('information/cage-codes*') ? 'active' : '' }}">
                                    <a href="{{ route('info.cage-codes') }}">
                                        <i class="fas fa-barcode"></i> MFR Cage Codes
                                    </a>
                                </li>
                                <li role="presentation" class="{{ set_active('information/aircraft*') ? 'active' : '' }}">
                                    <a href="{{ route('info.aircraft') }}">
                                        <i class="fas fa-plane"></i> Aircraft
                                    </a>
                                </li>
                                <li role="presentation" class="{{ set_active('information/engine-details*') ? 'active' : '' }}">
                                    <a href="{{ route('info.engine-details') }}">
                                        <i class="fas fa-fan"></i> Engine Details
                                    </a>
                                </li>
                                <li role="presentation" class="{{ set_active('information/location-parts*') ? 'active' : '' }}">
                                    <a href="{{ route('info.location-parts') }}">
                                        <i class="fas fa-clipboard-list"></i>  Excluded PN
                                    </a>
                                </li>
                                <li role="presentation" class="{{ set_active('information/rcs-failure-codes*') ? 'active' : '' }}">
                                    <a href="{{ route('info.rcs-failure-codes') }}">
                                        <i class="fas fa-exclamation-triangle"></i> RCS Failure Codes
                                    </a>
                                </li>
                                <li role="presentation" class="{{ set_active('information/shop-action-codes*') ? 'active' : '' }}">
                                    <a href="{{ route('info.shop-action-codes') }}">
                                        <i class="fas fa-exclamation-triangle"></i> Shop Action Codes
                                    </a>
                                </li>
                                <li role="presentation" class="{{ set_active('information/user-roles*') ? 'active' : '' }}">
                                    <a href="{{ route('info.user-roles') }}">
                                        <i class="fas fa-user-lock"></i> User Roles
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                @endauth
        
                <ul class="nav navbar-nav navbar-right">
                    @auth
                        <li class="dropdown {{ set_active('*password') || set_active('user-profile*') ? 'active' : '' }}">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-user"></i> {{ auth()->user()->fullname }} <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li role="presentation" class="{{ set_active('*your-activities') ? 'active' : '' }}">
                                    <a title="My Password" class="{{ set_active('*your-activities') ? 'active' : '' }}" href="{{ route('activity.show-my-activity') }}">
                                        <i class="fas fa-briefcase"></i> Your Activities
                                    </a>
                                </li>
                                <li role="presentation" class="{{ set_active('*messages*') ? 'active' : '' }}">
                                    <a title="My Message Settings" class="{{ set_active('*messages*') ? 'active' : '' }}" href="{{ route('message.edit') }}">
                                        <i class="fas fa-envelope"></i> Message Settings
                                    </a>
                                </li>
                                <li role="presentation" class="{{ set_active('*password') ? 'active' : '' }}">
                                    <a title="My Password" class="{{ set_active('*password') ? 'active' : '' }}" href="{{ route('user-profile.edit-password') }}">
                                        <i class="fa fa-key"></i> Change Password
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li role="presentation">
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                                Logout <i class="fas fa-sign-out-alt"></i>
                            </a>
        
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    @else
                        <li><a href="{{ route('login') }}">Login <i class="fas fa-sign-in-alt"></i></a></li>
                    @endauth
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</div>