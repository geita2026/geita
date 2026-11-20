<style>
    #h1{ font-size:250%; text-shadow: 1px 1px 1px rgb(0, 0, 0), 2px 2px 1px rgb(0, 0, 0);}
    #h2{ font-size:200%; text-shadow: 1px 1px 1px rgb(0, 0, 0), 2px 2px 1px rgb(0, 0, 0);}
    #h3{ font-size:150%; text-shadow: 1px 1px 1px rgb(0, 0, 0), 2px 2px 1px rgb(0, 0, 0);}
    #h4{ font-size:100%; text-shadow: 1px 1px 1px rgb(0, 0, 0), 2px 2px 1px rgb(0, 0, 0);}
</style>

<!-- Top right menu -->
<ul class="fab-menu fab-menu-absolute fab-menu-top-right" data-fab-toggle="hover" id="fab-menu-affixed-demo-right">
    <li>
        <a class="fab-menu-btn btn bg-pink-300 btn-float btn-rounded btn-icon">
            <i class="fab-icon-open  icon-help"></i>
            <i class="fab-icon-close icon-cross2"></i>
        </a>

        <ul class="fab-menu-inner">
            <li>
                <div data-fab-label='Call For Support: 255 733-002-014.'>
                    <a href="#" class="btn bg-primary btn-rounded btn-icon btn-float">
                        <i class=" icon-phone2"></i>
                    </a>
                </div>
            </li>
            <li>
                <div data-fab-label="View User manual here" >
                    <a href="{{ route('user_guide') }}" class="btn bg-primary btn-rounded btn-icon btn-float">
                       <i class=" icon-file-pdf"> </i>
                    </a>
                </div>
            </li>
        </ul>
    </li>
</ul>
<!-- /top right menu -->


