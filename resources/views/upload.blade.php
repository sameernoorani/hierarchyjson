

{!! Form::open(array('route' => 'fileUpload','enctype' => 'multipart/form-data')) !!}

<div class="row cancel">

        {!! Form::file('json', array('class' => 'json')) !!}


        <button type="submit" class="btn btn-success">Upload</button>


</div>

{!! Form::close() !!}
