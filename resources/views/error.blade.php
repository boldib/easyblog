@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8" >
            <div style="background-color: #ffffffa6;">
                <div class="p-2" class="card-header"><h5 class="mt-2">Error:</h5></div>

                    {{$error}}
                    
                </div>
            </div>
        </div>

        <div class="col-4">
            <div class="mt-2">
                <x-search/>
            </div>            
        </div>

    </div>
</div>
@endsection
