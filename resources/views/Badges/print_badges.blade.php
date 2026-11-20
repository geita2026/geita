
<x-app-layout>
    <title>{{ env('APP_NAME') }} | Print Badges</title>
   {{-- page header --}}
   <div class="page-header">
       <div class="page-header-content">
        @include('layouts.heading-elements')
       </div>
   <div class="breadcrumb-line breadcrumb-line-component">
       <ul class="breadcrumb">
           <li><a href="{{ route('adviceNotes.index') }}"><i class="icon-home2 position-left"></i>EXHIBITOR BADGES</a></li>
           <li class="active">View exhibitor badges</li>
       </ul>
       <ul class="breadcrumb-elements">
          @include('layouts.help')
       </ul>
   </div>
   </div>
<!-- /page header -->

<!-- Content area -->
<div class="content">
    <div class="panel panel-flat">
        <div class=" panel-body">

            <div class=" row form-group">
                <div class=" col-md-12 form-group">
                    <a href="{{ route('generate-badges.index') }}" class=" btn btn-default border-primary text-primary"><i class=" icon-arrow-left12"></i> Back</a>
                </div>
             </div>

             @include('Badges.BadgeLayouts.BadgeFilter')
             @include('Badges.BadgeLayouts.BadgeContent')

        </div>
    </div>
</div>
</div>
</x-app-layout>
