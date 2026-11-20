<x-app-layout>
    <title>{{ env('APP_NAME') }} | Pavilions</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
           @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('pavilions.index') }}"><i class=" icon-home9 position-left"></i>PAVILIONS</a></li>
                <li class="active">View-All-pavilion</li>
            </ul>

            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- /page header -->
    <!-- Content area -->
    <div class="content">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h6 class="panel-title text-bold">REGISTERED EXHIBITION SPACES </h6>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                        <li><a data-action="close"></a></li>
                    </ul>
                </div>
            </div>
            <div class=" panel-body">

                <div class=" row form-group">
                    <div class=" form-group col-md-12">
                        <h4 class=" text-warning"><b class=" text-danger">NOTE!</b> This process needs High tention and concentration, otherwise can lead to destraction of space arangement of the event
                            which will distable the whole process of space booking.
                        </h4>
                    </div>
                </div>


                <div class=" row form-group">
                    <form action="{{ route('space_migration.store') }}" method="POST">
                        @csrf
                        <div class=" form-group col-md-6">
                            <label>Move Space From This event <i class=" text-danger">*</i></label>
                           <select name="from_event" class="bootstrap-select " data-live-search="true" data-width="100%" placehoder="Select department" required>
                            <option value="">. . . . Select event. . . .</option>
                               <optgroup label="Pavilion Categories">
                                  @foreach ($events as $event)
                                  <option value="{{  $event->event_id}}">{{ $event->event_name }}</option>
                                  @endforeach
                               </optgroup>
                           </select>
                           <span class="text-danger"> @error('from_event'){{ $message }}@enderror</span>
                        </div>
                        <div class=" form-group col-md-6">
                            <label>Add Space To This event <i class=" text-danger">*</i></label>
                            <select name="to_event" class="bootstrap-select " data-live-search="true" data-width="100%" placehoder="Select department" required>
                                <option value="">. . . . Select event. . . .</option>
                                <optgroup label="Pavilion Categories">
                                   @foreach ($events as $event)
                                   <option value="{{  $event->event_id}}">{{ $event->event_name }}</option>
                                   @endforeach
                                </optgroup>
                            </select>
                            <span class="text-danger"> @error('from_event'){{ $message }}@enderror</span>
                        </div>

                        <div class=" col-md-12 text-right">
                            <button class=" btn btn-default border-primary text-primary btn-ladda btn-ladda-spinner">Move spaces</button>
                        </div>
                    </form>

                </div>


            </div>
        </div>
    </div>
</x-app-layout>
