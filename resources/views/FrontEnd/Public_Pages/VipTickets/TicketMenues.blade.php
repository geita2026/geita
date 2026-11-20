 <!-- Toolbar -->
 <div class="navbar navbar-default navbar-component navbar-xs">
    <ul class="nav navbar-nav visible-xs-block">
        <li class="full-width text-center"><a data-toggle="collapse"
                data-target="#navbar-filter"><i class="icon-menu7"></i></a>
        </li>
    </ul>

    <div class="navbar-collapse collapse" id="navbar-filter">
        <ul class="nav navbar-nav">
            <li class="{{ Route::is('online_tickets.index') ? 'active' : '' }}"><a href="{{ route('online_tickets.index') }}"><i
                        class="icon-menu7 position-left text-success"></i>
                    Book
                    Ticket</a>
            </li>
            <li class="{{ Route::is('myTicket.edit',['myTicket'=>date('syismaiswkshdiws')]) ? 'active' : ''}}"><a href="{{ route('myTicket.edit',['myTicket'=>date('syismaiswkshdiws')]) }}" ><i
                        class=" icon-ticket position-left text-warning"></i>
                    My Ticket
                </a>
            </li>
            <li class="{{ Route::is('about_sabasaba_tickets') ? 'active' : '' }}"><a href="{{ route('about_sabasaba_tickets') }}"><i
                        class=" icon-info22 position-left text-primary"></i>
                    How To Book
                    Ticket ?</a>
            </li>
        </ul>


    </div>
</div>
<!-- /toolbar -->