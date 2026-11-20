<div class=" form-group row">
@if ($item->applicationStatus==1 && $item->reviewComment!=Null)
    <div class=" alert alert-danger">
        <h5><b>RETURN COMMENT: </b>{{ $item->reviewComment }}</h5>
    </div>
@elseif ($item->applicationStatus>=3 && $item->applicationStatus<=4)
<div class="alert" style="background-color: rgb(236, 249, 250);">
    <h5><b>COMMENT: </b>{{ $item->reviewComment }}</h5>
</div>
@elseif ($item->applicationStatus==5)
<div class=" alert alert-success">
    <h5><b>COMMENT: </b>Congratulation your Application has been accepted, please process payment to receive your certificate of Approve.</h5>
</div>
@elseif($item->applicationStatus==2)
<div class=" alert" style="background-color: rgb(236, 249, 250);">
    <h6><b>COMMENT: </b>We acknowledge receiving your Application, We are working on it in One to two working Days. We will notify you for re-correction or proceeding with Other stage.</h6>
</div>
@endif

    
</div>