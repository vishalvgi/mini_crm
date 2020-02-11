@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Companies</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <div class="container">
                        <div class="row">
                            <div class="col-md-10"></div>
                            <div class="col-md-2"><a class="btn btn-primary" href="/home/create">Add</a></div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Name</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Logo</th>
                                            <th scope="col">Website</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($view_data as $single)
                                        <tr>
                                            <td>{{ $single->name }}</td>
                                            <td>{{ $single->email }}</td>
                                            <td>
                                                @if($single->logo)
                                                <img src="/storage/public/{{ $single->logo }}" height="100px" width="100px" />
                                                @endif
                                            </td>
                                            <td>{{ $single->website }}</td>
                                            <td>
                                                <form action="/home/{{$single->id}}" method = "post" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button  class="btn btn-link" type="submit">Delete</button>
                                                </form>/&nbsp;&nbsp; <a href="company/{{$single->id}}/employee">
                                                    Employees
                                                </a> &nbsp;&nbsp;/ &nbsp;&nbsp;
                                                <a href="/home/{{$single->id}}/edit" style="">
                                                    Edit
                                                </a> 
                                            </td>
                                        </tr>
                                        @endforeach
                                        @if(!count($view_data))
                                        <tr><td colspan="5" style="text-align: center">No data exist!</td></tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{ $view_data->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
